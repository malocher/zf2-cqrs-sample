<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Malocher\Cqrs\Gate;
use Application\Cqrs\Query\GetAllOpenTodosQuery;
use Application\Cqrs\Query\GetAllClosedTodosQuery;
use Application\Cqrs\Query\GetAllTodosQuery;
use Application\Cqrs\Command\CreateTodoCommand;
use Application\Cqrs\Command\CloseTodoCommand;
use Application\Cqrs\Command\CancelTodoCommand;
use Application\Form\TodoForm;

/**
 * Class TodoController
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoController extends AbstractActionController
{
    /**
     * CQRS Gate
     * 
     * @var Gate
     */
    protected $gate;
    
    /**
     * Gate Setter
     * 
     * @param Gate $gate
     */
    public function setGate(Gate $gate) 
    {
        $this->gate = $gate;
    }
    
    /**
     * IndexAction displays all todo lists. By default, all open todos are listed.
     * 
     * Closed todos or open and closed todos are displayed if the filter param is provided.
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $filter = $this->getEvent()->getRouteMatch()->getParam('filter', 'open');
        
        //We use a CQRS Query to ask our ReadModel for the required information
        switch ($filter) {
            case 'closed':
                $query = new GetAllClosedTodosQuery();
                break;
            case 'all':
                $query = new GetAllTodosQuery();
                break;
            case 'open':
            default:
                $query = new GetAllOpenTodosQuery();
        }
        
        /*
         * The query is delegated to Application\ReadModel\TodoReaderService
         * but the controller doesn't know this fact. The only thing it knows is
         * that it must excute the query and get a result back.
         * 
         * If we want to split the TodoReaderService or replace it with
         * another implementation, we only need to change the application module.config.php
         */
        $result = $this->gate->getBus()->executeQuery($query);
        return new ViewModel(array('todos' => $result, 'filter' => $filter));
    }
    
    /**
     * Handle complete todo creation
     * 
     * In the first round the action displays a todo form and
     * in the second round the action validates the input data and creates a new todo
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction() 
    {
        $todoForm = new TodoForm();
        
        if ($this->getRequest()->isPost()) {
            $todoForm->setData($this->getRequest()->getPost());
                
            if ($todoForm->isValid()) {
                //We can pass the valid data directly to the command
                $createTodoCommand = new CreateTodoCommand($todoForm->getData());
                
                /*
                 * Just send the command over the default bus 
                 *(Application\Cqrs\Bus\DomainBus like configured in configuration)
                 * 
                 * The TodoCommandHanlder takes the command and creates a new todo.
                 * If everything works fine, it publish a TodoCreatedEvent and
                 * the TodoReaderService updates it's view with the new information.
                 * Look at the implementations for more details.
                 * 
                 * Again, our controller doesn't know anything about that process.
                 * It sends commands and executes queries and thats it.
                 */
                $this->gate->getBus()->invokeCommand($createTodoCommand);
                
                return $this->redirect()->toRoute('todo');
            }
            
        }
        
        return new ViewModel(['form' => $todoForm]);
    }
    
    /**
     * Close a todo and redirect to index
     * 
     * @return Zend\Http\Response
     */
    public function closeAction()
    {
        $todoId = $this->getEvent()->getRouteMatch()->getParam('filter');
        
        /*
         * The payload of the command is only the todoId. Possible payload values
         * for every cqrs message (command, query, event) are all scalar values, arrays
         * and instances of the Malocher\Cqrs\Payload\PayloadInterface. 
         * If any other object type is passed to a message, it throws an exception.
         * The reason for this, a message must be immutable.
         */
        $closeTodoCommand = new CloseTodoCommand($todoId);
        
        /*
         * Look at the application module.config.php to find out what hapens, when
         * a CloseTodoCommand is send over the bus.
         * Do you get it?
         */
        $this->gate->getBus()->invokeCommand($closeTodoCommand);
        
        return $this->redirect()->toRoute('todo');
    }
    
    /**
     * Cancel a todo and redirect to index
     * 
     * @return Zend\Http\Response
     */
    public function cancelAction()
    {
        $todoId = $this->getEvent()->getRouteMatch()->getParam('filter');
        
        $cancelTodoCommand = new CancelTodoCommand($todoId);
        
        $this->gate->getBus()->invokeCommand($cancelTodoCommand);
        
        return $this->redirect()->toRoute('todo');
    }
}

