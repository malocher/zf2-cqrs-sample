<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\ReadModel;

use Application\Cqrs\Query\GetAllOpenTodosQuery;
/**
 * ReadModel Class TodoReaderService
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoReaderService
{
    use \Cqrs\Adapter\AdapterTrait;
    
    public function getAllOpenTodos(GetAllOpenTodosQuery $query)
    {
        return array();
    }
}
