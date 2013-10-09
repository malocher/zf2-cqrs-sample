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
use Application\Cqrs\Payload\TodoPayload;
use Application\Domain\Entity\Todo;
use Application\Domain\Entity\EntityFactory;
/**
 *  TodoRepository
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoRepository
{
    use \Cqrs\Adapter\AdapterTrait;
    
    protected $storageFile = 'data/todos.json';
    
    protected $todosData = array();
    
    /**
     *
     * @var EntityFactory
     */
    protected $entityFactory;


    public function __construct()
    {
        if (file_exists($this->storageFile)) {
            $this->todosData = json_decode(file_get_contents($this->storageFile), true);
        }
    }
    
    public function setEntityFactory(EntityFactory $factory) 
    {
        $this->entityFactory = $factory;
    }


    public function createTodo(CreateTodoCommand $command)
    {
        $todo = $this->entityFactory->createNewTodo($command->getPayload());
        $todoPayload = new TodoPayload();
        $todoPayload->extractFromEntity($todo);
        
        $this->todosData[$todo->getId()] = $todoPayload->getArrayCopy();
        $this->updateFile();
    }
    
 
    protected function updateFile()
    {
        file_put_contents($this->storageFile, json_encode($this->todosData));
    }
}
