<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Module configuration for the application module (main module)
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            /*
             * All todo routes start with /todo
             * following by an action: index, add, close, cancel
             * and an optional filter (a query filter for index action, todoId in case of close and cancel action)
             */
            'todo' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/todo',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Todo',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:action[/:filter]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'filter'     => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    /*
     * The service manager acts as CQRS handler- and listener loader
     * 
     * The defined aliases todo_reader_service and todo_command_handler are used
     * in the CQRS configuration {@see below}
     * 
     * Each class in your application can be a CQRS CommandHandler, QueryHandler or EventListener,
     * if it can be received from service manager
     */
    'service_manager' => array(
        'invokables' => array(
            'todo_reader_service' => 'Application\ReadModel\TodoReaderService',
            'todo_repository'     => 'Application\Domain\Repository\TodoRepository',
            'entity_factory'      => 'Application\Domain\Entity\EntityFactory',
        ),
        'factories' => array(
            'todo_command_handler' => function($sl) {
                $todoCommandHandler = new \Application\Domain\CommandHandler\TodoCommandHandler();
                $todoCommandHandler->setTodoRepository($sl->get('todo_repository'));
                $todoCommandHandler->setEntityFactory($sl->get('entity_factory'));
                
                return $todoCommandHandler;
            }
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
        /*
         * The CQRS Gate can be received from main service manager
         * 
         * In this controller callback factory we get the controller loader as argument.
         * The main service manager is accessible via getServiceLocator getter and
         * finally we can inject the CQRS Gate into controller with calling the get method
         * on the service manager with the CQRS Gate alias "cqrs.gate" as argument.
         */
        'factories' => array(
            'Application\Controller\Todo' => function($cl) {
                $c = new \Application\Controller\TodoController();
                $c->setGate($cl->getServiceLocator()->get('cqrs.gate'));
                return $c;
            }
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    /*
     * You can use the full set of configuration options provided by cqrs-php
     * {@see https://github.com/crafics/cqrs-php/tree/master/iterations/Iteration}
     * 
     * Put everything under the key cqrs. 
     * The codeliner/zf2-cqrs-module pass the configuration to Cqrs\Configuration\Setup,
     * first time you request the cqrs.gate from the service manager.
     */
    'cqrs' => array(
        /*
         * We use only one bus in this example and set it as default, so we can
         * call $cqrsGate->getBus() without the need to tell the gate wich bus we want to get.
         * 
         * You could also have multiple buses, f.e. an extra error bus, a frontend bus, 
         * or a bus for each domain (if you have more than one)
         */
        'default_bus' => 'domain-bus',
        'adapters' => array(
            /**
             * CQRS Adapters help you to setup your system.
             * We use the ArrayMapAdapter here. It is a very simple Adapter.
             * We have to map comands, queries and events to handlers and listeners
             * by hand. 
             * 
             * There are other adapters available, f.e. an AnnotationAdapter or an Adapter
             * that works with coneventions to do the mapping. 
             */
            'Cqrs\Adapter\ArrayMapAdapter' => array(                
                'buses' => array(
                    /*
                     * Register all commands, queries and events on the DomainBus
                     */
                    'Application\Cqrs\Bus\DomainBus' => array(
                        /*
                         * Each Adapter has it's own configuration structure.
                         * The ArrayMapAdapter needs complete mapping information
                         * for each cqrs message (command, query, event) 
                         */
                        'Application\Cqrs\Command\CreateTodoCommand' => array(
                            /*
                             * The alias of a handler or listener should match to
                             * an alias used within the service manager.
                             */
                            'alias' => 'todo_command_handler',
                            'method' => 'createTodo'
                        ),
                        'Application\Cqrs\Command\CloseTodoCommand' => array(
                            'alias' => 'todo_command_handler',
                            'method' => 'closeTodo'
                        ),
                        'Application\Cqrs\Command\CancelTodoCommand' => array(
                            'alias' => 'todo_command_handler',
                            'method' => 'cancelTodo'
                        ),
                        'Application\Cqrs\Event\TodoCreatedEvent' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'onTodoCreated'
                        ),
                        'Application\Cqrs\Event\TodoClosedEvent' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'onTodoClosed'
                        ),
                        'Application\Cqrs\Event\TodoCanceledEvent' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'onTodoCanceled'
                        ),
                        'Application\Cqrs\Query\GetAllOpenTodosQuery' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'getAllOpenTodos'
                        ),
                        'Application\Cqrs\Query\GetAllClosedTodosQuery' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'getAllClosedTodos'
                        ),
                        'Application\Cqrs\Query\GetAllTodosQuery' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'getAllTodos'
                        )
                    )
                )
            )
        )
    ),
);
