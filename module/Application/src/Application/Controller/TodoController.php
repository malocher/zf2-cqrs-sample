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
use Application\Cqrs\Command\CreateTodoCommand;
use Application\Cqrs\Bus\DomainBus;
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
        $query = new GetAllOpenTodosQuery();
        $result = $this->gate->getBus(DomainBus::NAME)->executeQuery($query);
        return new ViewModel(array('todos' => $result));
    }
    
    public function addAction() 
    {
        $todoForm = new TodoForm();
        
        if ($this->getRequest()->isPost()) {
            $todoForm->setData($this->getRequest()->getPost());
                
            if ($todoForm->isValid()) {
                $createTodoCommand = new CreateTodoCommand($todoForm->getData());
                
                $this->gate->getBus(DomainBus::NAME)->invokeCommand($createTodoCommand);
                
                return $this->redirect()->toUrl('/application/todo');
            } else {
                return new ViewModel(['form' => $todoForm]);
            }
        } else {
            return new ViewModel(['form' => $todoForm]);
        }
        
    }
}

