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
 * Event TodoCreatedEvent
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoCreatedEvent extends Message implements EventInterface
{
}
