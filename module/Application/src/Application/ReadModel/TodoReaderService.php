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
 * This ReadModel is seperated from our domain logic. It listens on the various 
 * todo events to keep it's view information up to date.
 * To achieve fast response times, the ReadModel store the data in the way the 
 * frontend needs it to display. In other words: the TodoController wants to display
 * the open todos, the closed todos or both and therefor the ReadModel maintain to files
 * data/open-todos.json and data/closed-todos.json. 
 * 
 * If it has to ask the domain for all open todos, the TodoRepository has to load each todo,
 * check the state and at it to a collection if state is open. This would be a very slow
 * process compared to the snapshot approach that is used here.
 * 
 * On the other site, the ReadModel should not know anything about the todo rules (DRY principle). When is
 * a todo open and when is it closed? The model only knows that a new todo is
 * always open and when a todo is closed a TodoClosedEvent is triggered.
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoReaderService
{
    use \Cqrs\Adapter\AdapterTrait;
    
    /**
     *
     * @var string 
     */
    protected $openTodosFile = 'data/open-todos.json';
    
    /**
     *
     * @var string 
     */
    protected $closedTodosFile = 'data/closed-todos.json';

    /**
     *
     * @var array
     */
    protected $openTodos = array();
    
    /**
     *
     * @var array
     */
    protected $closedTodos = array();
    
    /**
     * Listener for the TodoCreatedEvent
     * 
     * Adds the created todo to the list of open todos
     * 
     * @param TodoCreatedEvent $event
     * @return void
     */
    public function onTodoCreated(TodoCreatedEvent $event) 
    {
        $this->addToOpenTodos($event->getPayload());
    }
    
    /**
     * Listener for the TodoClosedEvent
     * 
     * Moves the todo from the open list to the closed list
     * 
     * @param TodoClosedEvent $event
     * @return void
     */
    public function onTodoClosed(TodoClosedEvent $event)
    {
        $this->addToClosedTodos($event->getTodoId(), $event->getNewTodoState());
    }
    
    /**
     * Listener for the TodoCanceledEvent
     * 
     * Removes the todo from the lists
     * 
     * @param TodoCanceledEvent $event
     */
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

    /**
     * Get a list of all open todos
     * 
     * @param GetAllOpenTodosQuery $query
     * @return array
     */
    public function getAllOpenTodos(GetAllOpenTodosQuery $query)
    {
        $this->loadOpenTodos();
        return $this->openTodos;
    }
    
    /**
     * Get a list of all closed todos
     * 
     * @param GetAllClosedTodosQuery $query
     * @return array
     */
    public function getAllClosedTodos(GetAllClosedTodosQuery $query)
    {
        $this->loadClosedTodos();
        return $this->closedTodos;
    }
    
    /**
     * Get a list of open and closed todos
     * 
     * @param GetAllTodosQuery $query
     * @return array
     */
    public function getAllTodos(GetAllTodosQuery $query)
    {
        $this->loadAllTodos();
        
        return $this->openTodos + $this->closedTodos;
    }
    
    /**
     * Load open todos from file and cache them internaly
     * 
     * @return void
     */
    protected function loadOpenTodos()
    {
        if (file_exists($this->openTodosFile)) {
            $this->openTodos = json_decode(file_get_contents($this->openTodosFile), true);
        }
    }
    
    /**
     * Load closed todos from file and cache them internaly
     * 
     * @return void
     */
    protected function loadClosedTodos()
    {
        if (file_exists($this->closedTodosFile)) {
            $this->closedTodos = json_decode(file_get_contents($this->closedTodosFile), true);
        }
    }
    
    /**
     * Load open and closed todos from file and cache them internaly
     * 
     * @return void
     */
    protected function loadAllTodos()
    {
        $this->loadOpenTodos();
        $this->loadClosedTodos();
    }

    /**
     * Add todo data to list of open todos
     * 
     * @param array $todoData
     * @return void
     */
    protected function addToOpenTodos(array $todoData)
    {
        $this->loadOpenTodos();
        $this->openTodos[$todoData['id']] = $todoData;
        $this->writeOpenTodosToFile();
    }
    
    /**
     * Move todo data from open todo list to closed list and set new todo state
     * 
     * @param array $todoData
     * @return void
     */
    protected function addToClosedTodos($todoId, $newState)
    {
        $this->loadAllTodos();
        $todoData = $this->openTodos[$todoId];
        unset($this->openTodos[$todoId]);
        $todoData['state'] = $newState;
        $this->closedTodos[$todoId] = $todoData;
        $this->writeOpenTodosToFile();
        $this->writeClosedTodosToFile();
    }
    
    /**
     * Write cached open todo list to file
     * 
     * @return void
     */
    protected function writeOpenTodosToFile()
    {
        file_put_contents($this->openTodosFile, json_encode($this->openTodos));
    }
    
    /**
     * Write cached closed todo list to file
     * 
     * @return void
     */
    protected function writeClosedTodosToFile()
    {
        file_put_contents($this->closedTodosFile, json_encode($this->closedTodos));
    }
}
