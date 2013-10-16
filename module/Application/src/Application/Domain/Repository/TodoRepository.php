<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Domain\Repository;

use Application\Cqrs\Command\CreateTodoCommand;
use Application\Cqrs\Command\CloseTodoCommand;
use Application\Cqrs\Command\CancelTodoCommand;
use Application\Cqrs\Event\TodoCreatedEvent;
use Application\Cqrs\Event\TodoClosedEvent;
use Application\Cqrs\Event\TodoCanceledEvent;
use Application\Cqrs\Payload\TodoPayload;
use Application\Domain\Entity\EntityFactory;
use Application\Domain\Entity\Todo;
/**
 * Repository TodoRepository
 * 
 * The repository uses the filesystem to store todos. 
 * Each todo is saved in an own file data/todos/[todoId].json
 * 
 * To keep the application as simple as possible we pass on error handling.
 * In a later example, when we deal with transactions and event sourcing, we will
 * add error handling for filesystem errors and logic exceptions.
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoRepository
{
    use \Cqrs\Adapter\AdapterTrait;
    
    protected $storageDir = 'data/todos/';
    
    protected $todosData = array();
    
    /**
     *
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * Set the entity factory
     * 
     * Even when the TodoRepository is loaded to handle a command, the dependencies
     * are injected by it's factory, cause the TodoRepository is loaded via ServiceManager
     * 
     * @param EntityFactory $factory
     * @return void
     */
    public function setEntityFactory(EntityFactory $factory) 
    {
        $this->entityFactory = $factory;
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
        
        $todoPayload = new TodoPayload();
        $todoPayload->extractFromEntity($todo);
        
        $this->writeToFile($todoPayload);
        
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
        $todo = $this->getTodo($command->getTodoId());
        $todo->close();
        
        $todoPayload = new TodoPayload();
        $todoPayload->extractFromEntity($todo);
        
        $this->writeToFile($todoPayload);
        
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
        $this->deleteFile($command->getTodoId());
        
        $todoCanceledEvent = new TodoCanceledEvent($command->getTodoId());
        
        $this->getBus()->publishEvent($todoCanceledEvent);
    }
    
    /**
     * Get a Todo
     * 
     * @param int $todoId
     * @return Todo
     */
    public function getTodo($todoId) 
    {
        $todo = new Todo($todoId);
        
        $todoData = $this->readFile($todoId);
        
        $todo->setTitle($todoData['title']);
        $todo->setDescription($todoData['description']);
        $todo->setState($todoData['state']);
        
        return $todo;
    }
    
    /**
     * Read todo data from file
     * 
     * @param int $todoId
     * @return array
     */
    protected function readFile($todoId)
    {
        return json_decode(file_get_contents($this->storageDir . $todoId . '.json'), true);        
    }
    
    
    /**
     * Write todo data to file
     * 
     * @param TodoPayload $payload
     * @return void
     */
    protected function writeToFile(TodoPayload $payload)
    {
        file_put_contents(
            $this->storageDir . $payload->getId() . '.json', 
            json_encode($payload->getArrayCopy())
        );
    }
    
    /**
     * Delete a todo file
     * 
     * @param int $todoId
     * @return void
     */
    protected function deleteFile($todoId) 
    {
        unlink($this->storageDir . $todoId . '.json');
    }
}
