<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Cqrs\Command;

use Malocher\Cqrs\Command\CommandInterface;
use Malocher\Cqrs\Message\Message;
/**
 * Command CloseTodoCommand
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class CloseTodoCommand extends Message implements CommandInterface
{
    /**
     * Constructor
     * 
     * In our simple example we only work with the payload argument of a message,
     * the other three optional arguments come in play when we want to deal with event or command sourcing.
     * 
     * @param int|array $payload
     * @param string    $id
     * @param int       $timestamp
     * @param float     $version
     */
    public function __construct($payload = null, $id = null, $timestamp = null, $version = 1.0)
    {
        //We want to unify the payload, put first we call the parent contructor
        //to check if payload is valid
        parent::__construct($payload, $id, $timestamp, $version);
        
        //Unify the payload, we only need the todoId
        if (is_array($this->payload)) {
            $this->payload = $this->payload['id'];
        }
    }
    
    /**
     * Get the todoId
     * 
     * You can extend CQRS messages to provide easy to use getter.
     * This gives the consumer of the message a better picture of how to use it.
     * 
     * @return int
     */
    public function getTodoId()
    {
        return $this->payload;
    }
}
