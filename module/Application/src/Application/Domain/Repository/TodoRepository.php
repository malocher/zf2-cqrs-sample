<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Domain\Repository;

use Application\Cqrs\Payload\TodoPayload;
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
    protected $storageDir = 'data/todos/';
    
    /**
     * Persist a todo
     * 
     * @param Todo $todo
     * @return void
     */
    public function saveTodo(Todo $todo)
    {
        $todoPayload = new TodoPayload();
        $todoPayload->extractFromEntity($todo);
        
        $this->writeToFile($todoPayload);
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
     * Remove todo from storage
     * 
     * @param Todo $todo
     * @return void
     */
    public function removeTodo(Todo $todo)
    {
        $this->deleteFile($todo->getId());
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
