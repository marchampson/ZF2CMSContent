<?php
/*
 * Extend just the select element?
 * Field needs to use both the category tbl and the category associations tbl
 * 
 *  List all categories and highlight those already associated
 *  
 *  Store changes in session or jquery and post with form.
 */

/**
 * 
 * @author marchampson
 *
 */
namespace ElmContent\Form\Element;

use Zend\Form\Element;
use Zend\Form\ElementInterface;

class Category extends Element implements ElementInterface
{
    protected $valueOptions = array();
    
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'category',
        'name' => 'categorypicker',
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