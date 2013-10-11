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
 * Event TodoCanceledEvent
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoCanceledEvent extends Message implements EventInterface
{
    public function __construct($payload = null, $id = null, $timestamp = null, $version = 1.0)
    {
        parent::__construct($payload, $id, $timestamp, $version);
        
        if (is_array($this->payload)) {
            $this->payload = $this->payload['id'];
        }
    }
    
    public function getTodoId()
    {
        return $this->payload;
    }
}
