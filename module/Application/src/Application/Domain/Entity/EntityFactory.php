<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Domain\Entity;

/**
 *  EntityFactory
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class EntityFactory
{
    /**
     * Create an todo
     * 
     * @return Todo
     */
    public function createNewTodo($data)
    {
        $todo = new Todo(uniqid());
        
        $todo->setTitle($data['title']);
        $todo->setDescription($data['description']);
        $todo->setState('open');
        
        return $todo;
    }
}
