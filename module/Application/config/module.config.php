<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
    'service_manager' => array(
        'invokables' => array(
            'todo_reader_service' => 'Application\ReadModel\TodoReaderService'
        ),
        'factories' => array(
            'todo_repository' => function($sl) {
                $todoRepository = new \Application\Domain\Repository\TodoRepository();
                $todoRepository->setEntityFactory(new \Application\Domain\Entity\EntityFactory());
                return $todoRepository;
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
    'cqrs' => array(
        'adapters' => array(
            array(
                'class' => 'Cqrs\Adapter\ArrayMapAdapter',
                'buses' => array(
                    'Application\Cqrs\Bus\DomainBus' => array(
                        'Application\Cqrs\Command\CreateTodoCommand' => array(
                            'alias' => 'todo_repository',
                            'method' => 'createTodo'
                        ),
                        'Application\Cqrs\Command\CloseTodoCommand' => array(
                            'alias' => 'todo_repository',
                            'method' => 'closeTodo'
                        ),
                        'Application\Cqrs\Event\TodoCreatedEvent' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'onTodoCreated'
                        ),
                        'Application\Cqrs\Event\TodoClosedEvent' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'onTodoClosed'
                        ),
                        'Application\Cqrs\Query\GetAllOpenTodosQuery' => array(
                            'alias' => 'todo_reader_service',
                            'method' => 'getAllOpenTodos'
                        )
                    )
                )
            )
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
