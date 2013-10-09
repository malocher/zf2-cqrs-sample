<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Cqrs\Payload;

use Cqrs\Message\PayloadInterface;
use Application\Domain\Entity\Todo;
/**
 *  TodoPayload
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoPayload implements PayloadInterface
{
    protected $id;
    
    protected $title;
    
    protected $description;
    
    protected $state = 'open';
    
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
    
    public function extractFromEntity(Todo $todo) 
    {
        $this->id = $todo->getId();
        $this->title = $todo->getTitle();
        $this->description = $todo->getDescription();
        $this->state = $todo->getState();
    }

    public function getArrayCopy()
    {
        return array(
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'state'       => $this->state
        );
    }
}
