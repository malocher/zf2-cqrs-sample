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
 * Command CreateTodoCommand
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class CreateTodoCommand extends Message implements CommandInterface
{
    /*
     * This is just a marker class, so that all cqrs components can differentiate
     * between the messages. Every command, event and query has to be a cqrs message.
     * First the specific interface marks it as one of the three types.
     * The Cqrs\Message\Message provides all required default functionality.
     */
}
