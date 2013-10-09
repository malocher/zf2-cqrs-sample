<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Cqrs\Query;

use Cqrs\Query\QueryInterface;
use Cqrs\Message\Message;

/**
 * Query Class GetAllOpenTodosQuery
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class GetAllOpenTodosQuery extends Message implements QueryInterface
{
    
}
