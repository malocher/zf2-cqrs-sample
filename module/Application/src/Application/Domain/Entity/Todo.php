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
    
    /**
     * Constructor
     * 
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
    
    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * 
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * 
     * @param string $state
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }
    
    /**
     * Change state to <done>
     * 
     * @return void
     */
    public function close() 
    {
        $this->state = 'done';
    }
    
    /**
     * 
     * @return boolean
     */
    public function isOpen() 
    {
        return $this->state == 'open';
    }
    
    /**
     * 
     * @return boolean
     */
    public function isDone()
    {
        return $this->state == 'done';
    }
}
