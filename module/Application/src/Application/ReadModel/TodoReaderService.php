<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\ReadModel;

use Application\Cqrs\Query\GetAllOpenTodosQuery;
use Application\Cqrs\Query\GetAllClosedTodosQuery;
use Application\Cqrs\Query\GetAllTodosQuery;
use Application\Cqrs\Event\TodoCreatedEvent;
use Application\Cqrs\Event\TodoClosedEvent;
use Application\Cqrs\Event\TodoCanceledEvent;

/**
 * ReadModel Class TodoReaderService
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoReaderService
{
    use \Cqrs\Adapter\AdapterTrait;
    
    protected $openTodosFile = 'data/open-todos.json';
    
    protected $closedTodosFile = 'data/closed-todos.json';


    protected $openTodos = array();
    
    protected $closedTodos = array();
    
    public function onTodoCreated(TodoCreatedEvent $event) 
    {
        $this->addToOpenTodos($event->getPayload());
    }
    
    public function onTodoClosed(TodoClosedEvent $event)
    {
        $this->addToClosedTodos($event->getTodoId());
    }
    
    public function onTodoCanceled(TodoCanceledEvent $event)
    {
        $this->loadAllTodos();
        
        if (isset($this->openTodos[$event->getTodoId()])) {
            unset($this->openTodos[$event->getTodoId()]);
            $this->writeOpenTodosToFile();
        }
        
        if (isset($this->closedTodos[$event->getTodoId()])) {
            unset($this->closedTodos[$event->getTodoId()]);
            $this->writeClosedTodosToFile();
        }
    }

    public function getAllOpenTodos(GetAllOpenTodosQuery $query)
    {
        $this->loadOpenTodos();
        return $this->openTodos;
    }
    
    public function getAllClosedTodos(GetAllClosedTodosQuery $query)
    {
        $this->loadClosedTodos();
        return $this->closedTodos;
    }
    
    public function getAllTodos(GetAllTodosQuery $query)
    {
        $this->loadAllTodos();
        
        return $this->openTodos + $this->closedTodos;
    }
    
    protected function loadOpenTodos()
    {
        if (file_exists($this->openTodosFile)) {
            $this->openTodos = json_decode(file_get_contents($this->openTodosFile), true);
        }
    }
    
    protected function loadClosedTodos()
    {
        if (file_exists($this->closedTodosFile)) {
            $this->closedTodos = json_decode(file_get_contents($this->closedTodosFile), true);
        }
    }
    
    protected function loadAllTodos()
    {
        $this->loadOpenTodos();
        $this->loadClosedTodos();
    }


    protected function addToOpenTodos(array $todoData)
    {
        $this->loadOpenTodos();
        $this->openTodos[$todoData['id']] = $todoData;
        $this->writeOpenTodosToFile();
    }
    
    protected function addToClosedTodos($todoId)
    {
        $this->loadAllTodos();
        $todoData = $this->openTodos[$todoId];
        unset($this->openTodos[$todoId]);
        $todoData['state'] = 'closed';
        $this->closedTodos[$todoId] = $todoData;
        $this->writeOpenTodosToFile();
        $this->writeClosedTodosToFile();
    }
    
    protected function writeOpenTodosToFile()
    {
        file_put_contents($this->openTodosFile, json_encode($this->openTodos));
    }
    
    protected function writeClosedTodosToFile()
    {
        file_put_contents($this->closedTodosFile, json_encode($this->closedTodos));
    }
}
