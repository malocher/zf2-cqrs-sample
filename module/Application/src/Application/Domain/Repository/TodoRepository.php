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
use Application\Cqrs\Event\TodoCreatedEvent;
use Application\Cqrs\Payload\TodoPayload;
use Application\Cqrs\Bus\DomainBus;
use Application\Domain\Entity\EntityFactory;
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
        
        $this->getBus(DomainBus::NAME)->publishEvent($todoCreatedEvent);
    }
    
 
    protected function writeToFile(TodoPayload $payload)
    {
        file_put_contents(
            $this->storageDir . $payload->getId() . '.json', 
            json_encode($payload->getArrayCopy())
        );
    }
}
