<?php
namespace ElmContent\Form;
use Zend\Form\Form as Form;
use Zend\Form\Element as Element;

class ProductForm extends Form
{
    
	public function __construct($name = null)
	{
	    // we want to ignore the name passed
	    parent::__construct('product');
	    $this->setAttribute('method', 'post');
	    
	    /*
	     * Hidden Elements
	     */
	    $id = new Element\Hidden('id');
	    $main_image_value = new Element\Hidden('main_image_value');
	    $delete_main_image = new Element\Hidden('delete_main_image');
	    $background_image_value = new Element\Hidden('background_image_value');
	    $header_image_value = new Element\Hidden('header_image_value');
	    
		$name = new Element\Text('name');
		$name->setLabel('Name')
			->setAttributes(array('maxlength' => 128, 'class' => 'text'));
		
		$status = new Element\Select('status');
		$status->setLabel('Page Status');
		$status->setOptions(array('options' => array('Draft' => 'Draft','Live' => 'Live', 'Private' => 'Private')));
		
		$featured = new Element\Checkbox('featured');
		$featured->setLabel('Featured');
		
		$description = new Element\Textarea('description');
		$description->setLabel('Main page content');
		
		$image = new Element\File('main_image');
		$image->setLabel('Main image');
		
		$submit = new Element\Submit('submit');
		$submit->setAttribute('value', 'send');
		
		$this->add($id)
		    ->add($main_image_value)
		    ->add($delete_main_image)
		    ->add($name)
		    ->add($status)
			->add($featured)
			->add($description)
			->add($image)
			->add($submit);
	}
}