<?php

/**
 * 
 * @author marchampson
 *
 */
namespace ElmContent\Form\Element;

use Zend\Form\Element;
use Zend\Form\ElementInterface;

class Page extends Element implements ElementInterface
{
    protected $valueOptions = array();
    
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'page',
        'name' => 'pagepicker',
        'options' => array()   
    ); 
    
    /**
     * @param  array $options
     * @return Select
     */
    
    public function setValueOptions(array $options)
    {
        $this->valueOptions = $options;
        return $this;
    }
    
    public function getValueOptions()
    {
        return $this->valueOptions;
    }
    
}