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
use Application\Cqrs\Event\TodoCreatedEvent;
use Application\Cqrs\Event\TodoClosedEvent;
use Application\Cqrs\Payload\TodoPayload;
use Application\Domain\Entity\EntityFactory;
use Application\Domain\Entity\Todo;
/**
 *  TodoRepository
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
    
    public function setEntityFactory(EntityFactory $factory) 
    {
        $this->entityFactory = $factory;
    }


    public function createTodo(CreateTodoCommand $command)
    {
        $todo = $this->entityFactory->createNewTodo($command->getPayload());
        
        $todoPayload = new TodoPayload();
        $todoPayload->extractFromEntity($todo);
        
        $this->writeToFile($todoPayload);
        
        $todoCreatedEvent = new TodoCreatedEvent($todoPayload);
        
        $this->getBus()->publishEvent($todoCreatedEvent);
    }
    
    public function getTodo($todoId) 
    {
        $todo = new Todo($todoId);
        
        $todoData = $this->readFile($todoId);
        
        $todo->setTitle($todoData['title']);
        $todo->setDescription($todoData['description']);
        $todo->setState($todoData['state']);
        
        return $todo;
    }
    
    public function closeTodo(CloseTodoCommand $command)
    {
        $todo = $this->getTodo($command->getTodoId());
        $todo->close();
        
        $todoPayload = new TodoPayload();
        $todoPayload->extractFromEntity($todo);
        
        $this->writeToFile($todoPayload);
        
        $todoClosedEvent = new TodoClosedEvent($command->getTodoId());
        $this->getBus()->publishEvent($todoClosedEvent);
    }
    
    protected function readFile($todoId)
    {
        return json_decode(file_get_contents($this->storageDir . $todoId . '.json'), true);        
    }
    
    
    protected function writeToFile(TodoPayload $payload)
    {
        file_put_contents(
            $this->storageDir . $payload->getId() . '.json', 
            json_encode($payload->getArrayCopy())
        );
    }
    
    protected function deleteFile($todoId) 
    {
        unlink($this->storageDir . $todoId . '.json');
    }
}
