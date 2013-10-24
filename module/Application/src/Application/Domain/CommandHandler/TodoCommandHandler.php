<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Domain\CommandHandler;

use Application\Cqrs\Payload\TodoPayload;
use Application\Cqrs\Command\CreateTodoCommand;
use Application\Cqrs\Command\CloseTodoCommand;
use Application\Cqrs\Command\CancelTodoCommand;
use Application\Cqrs\Event\TodoCreatedEvent;
use Application\Cqrs\Event\TodoClosedEvent;
use Application\Cqrs\Event\TodoCanceledEvent;
use Application\Domain\Entity\EntityFactory;
use Application\Domain\Repository\TodoRepository;
/**
 *  TodoCommandHandler
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoCommandHandler
{
    use \Cqrs\Adapter\AdapterTrait;
    
    /**
     *
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     *
     * @var TodoRepository
     */
    protected $todoRepository;


    /**
     * Set the entity factory
     * 
     * Even when the TodoCommandHandler is loaded to handle a command, the dependencies
     * are injected by it's factory, cause the TodoCommandHandler is loaded via ServiceManager
     * 
     * @param EntityFactory $factory
     * @return void
     */
    public function setEntityFactory(EntityFactory $factory) 
    {
        $this->entityFactory = $factory;
    }
    
    /**
     * Set TodoRepository
     * 
     * @param TodoRepository $todoRepository
     * @return void
     */
    public function setTodoRepository(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }
    
    /**
     * Handle the CreateTodoCommand
     * 
     * @param    CreateTodoCommand $command
     * @triggers TodoCreatedEvent
     * @return   void
     */
    public function createTodo(CreateTodoCommand $command)
    {
        $todo = $this->entityFactory->createNewTodo($command->getPayload());
        
        $this->todoRepository->saveTodo($todo);
        
        $todoPayload = new TodoPayload();
        $todoPayload->extractFromEntity($todo);
        
        $todoCreatedEvent = new TodoCreatedEvent($todoPayload);
        
        $this->getBus()->publishEvent($todoCreatedEvent);
    }
    
    /**
     * Handle the CloseTodoCommand
     * 
     * @param    CloseTodoCommand $command
     * @triggers TodoClosedEvent
     * @return   void
     */
    public function closeTodo(CloseTodoCommand $command)
    {
        $todo = $this->todoRepository->getTodo($command->getTodoId());
        $todo->close();
        
        $this->todoRepository->saveTodo($todo);
        
        $eventData = array('id' => $todo->getId(), 'state' => $todo->getState());
        $todoClosedEvent = new TodoClosedEvent($eventData);
        $this->getBus()->publishEvent($todoClosedEvent);
    }
    
    /**
     * Handle the CancelTodoCommand
     * 
     * @param    CancelTodoCommand $command
     * @triggers TodoCanceledEvent
     * @return   void
     */
    public function cancelTodo(CancelTodoCommand $command) 
    {
        $todo = $this->todoRepository->getTodo($command->getId());
        
        $todo->cancle();
        
        $this->todoRepository->saveTodo($todo);
        
        $todoCanceledEvent = new TodoCanceledEvent($command->getTodoId());
        
        $this->getBus()->publishEvent($todoCanceledEvent);
    }
}
