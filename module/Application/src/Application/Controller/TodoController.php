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
use Cqrs\Gate;
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
    
    public function setGate(Gate $gate) 
    {
        $this->gate = $gate;
    }
    
    public function indexAction()
    {
        $filter = $this->getEvent()->getRouteMatch()->getParam('filter', 'open');
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
        
        $result = $this->gate->getBus()->executeQuery($query);
        return new ViewModel(array('todos' => $result, 'filter' => $filter));
    }
    
    public function addAction() 
    {
        $todoForm = new TodoForm();
        
        if ($this->getRequest()->isPost()) {
            $todoForm->setData($this->getRequest()->getPost());
                
            if ($todoForm->isValid()) {
                $createTodoCommand = new CreateTodoCommand($todoForm->getData());
                
                $this->gate->getBus()->invokeCommand($createTodoCommand);
                
                return $this->redirect()->toUrl('/todo');
            } else {
                return new ViewModel(['form' => $todoForm]);
            }
        } else {
            return new ViewModel(['form' => $todoForm]);
        }
        
    }
    
    public function closeAction()
    {
        $todoId = $this->getEvent()->getRouteMatch()->getParam('filter');
        
        $closeTodoCommand = new CloseTodoCommand($todoId);
        
        $this->gate->getBus()->invokeCommand($closeTodoCommand);
        
        return $this->redirect()->toUrl('/todo');
    }
    
    public function cancelAction()
    {
        $todoId = $this->getEvent()->getRouteMatch()->getParam('filter');
        
        $cancelTodoCommand = new CancelTodoCommand($todoId);
        
        $this->gate->getBus()->invokeCommand($cancelTodoCommand);
        
        return $this->redirect()->toUrl('/todo');
    }
}

