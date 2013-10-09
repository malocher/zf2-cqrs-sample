<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
/**
 *  TodoForm
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class TodoForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => 'Title',
            ),
            'type'  => 'Text',
        ));
        
        $this->add(array(
            'name' => 'description',
            'options' => array(
                'label' => 'Description',
            ),
            'type'  => 'Textarea',
        ));
        
        $this->add(array(
            'name' => 'send',
            'type'  => 'Submit',
            'attributes' => array(
                'value' => 'Submit',
            ),
        ));
    }
    
    public function getInputFilter()
    {
        if (is_null($this->filter)) {
            $titleInput = new Input('title');
            $titleInput->setRequired(true);

            $descInput = new Input('description');
            $descInput->allowEmpty();

            $filter = new InputFilter();
            $filter->add($titleInput)->add($descInput);
            $this->filter = $filter;
        }
        
        return $this->filter;
    }
}
