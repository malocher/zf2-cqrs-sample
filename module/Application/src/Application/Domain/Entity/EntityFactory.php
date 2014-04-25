<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Domain\Entity;

use Zend\Stdlib\Exception\InvalidArgumentException;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * EntityFactory
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class EntityFactory extends ClassMethods
{
    /**
     * Create a todo
     * 
     * @param  array $data
     * @return Todo
     */
    public function createNewTodo(array $data)
    {
        // override/specify defaults for new instances
        $data['id']    = uniqid();
        $data['state'] = 'open';
        return $this->hydrate($data, null);
    }
    
    /**
     * Extract a Todo object into an array
     * 
     * @param  Todo $todo the object to extract
     * @return array
     * @throws InvalidArgumentException when the object is not a Todo
     */
    public function extract($todo)
    {
        if (!$todo instanceof Todo) {
            $message = sprintf(
                "Extraction object must be an instance of Todo, %s given",
                is_object($todo) ? get_class($todo) : gettype($todo)
            );
            throw new InvalidArgumentException($message);
        }
        
        return parent::extract($todo);
    }
    
    /**
     * Hydrate a Todo object with array data.
     * 
     * If the object is not a Todo, a new Todo will be instanciated.
     * 
     * @param  array $data   the hydration data
     * @param  Todo  $object the object to hydrate
     * @return Todo
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof Todo) {
            $object = new Todo(isset($data['id']) ? $data['id'] : uniqid());
        }
        
        return parent::hydrate($data, $object);
    }
}
