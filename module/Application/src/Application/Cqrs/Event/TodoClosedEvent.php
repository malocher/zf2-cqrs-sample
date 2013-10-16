<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Cqrs\Event;

use Cqrs\Event\EventInterface;
use Cqrs\Message\Message;
/**
 * Event TodoClosedEvent
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoClosedEvent extends Message implements EventInterface
{
    /**
     * Get the todoId
     * 
     * You can extend CQRS messages to provide easy to use getters.
     * This gives the consumer of the message a better picture of how to use it.
     * 
     * @return int
     */
    public function getTodoId()
    {
        return $this->payload['id'];
    }
    
    /**
     * Get the new state of the todo after closing it
     * 
     * @return string
     */
    public function getNewTodoState()
    {
        return $this->payload['state'];
    }
}
