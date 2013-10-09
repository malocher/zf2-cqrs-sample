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
 * Entity Todo
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class Todo
{
    protected $id;
    
    protected $title;
    
    protected $description;
    
    protected $state = 'open';
    
    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
    
    public function close() 
    {
        $this->state = 'done';
    }
    
    public function is_open() 
    {
        return $this->state == 'open';
    }
    
    public function is_done()
    {
        return $this->state == 'done';
    }
}
