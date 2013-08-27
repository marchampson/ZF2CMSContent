<?php
namespace ElmContent\Form;

use Zend\Form\Form;
use Zend\Form\Element as Element;

class CategoryForm extends Form
{
    protected $parentCategories;
    protected $fieldsetArray;
    
    public function setService(\ElmContent\Service\CategoryService $service)
    {
        $this->parentCategories = $service;
        $parentCategoriesArray = array(0 => 'Select one');
        $parentCategoryList = $this->parentCategories->getParentCategories();
        if(count($parentCategoryList) > 0) {
            foreach($parentCategoryList as $k => $v) {
                $parentCategoriesArray[$k] = $v;
            }
        }
        $this->get('parent_id')->setOptions(array('options' => $parentCategoriesArray, 'description' => ''));
    }
    
	public function __construct($name = null)
	{
		parent::__construct('category');
		$this->setAttribute('method', 'post');
		
		$id = new Element\Hidden('id');
		
		$name = new Element\Text('name');
		$name->setLabel('Name')
			   ->setAttributes(array('maxlength' => 128));
		//$name->setOptions(array('description' => 'Description text goes here'));
		
		$title = new Element\Text('title');
		$title->setLabel('Title')
		->setAttributes(array('maxlength' => 128));
		
		$parentId = new Element\Select('parent_id');
        $parentId->setLabel('Select parent category if applicable');
		
		$image_preview = new Element\Image('image_preview');
		$image_preview->setLabel('Image preview');
		
		
		$image = new Element\File('image');
		$image->setLabel('Image');
		
		$image_delete = new Element\Checkbox('image_delete');
		$image_delete->setLabel('Delete image');
		
		$description = new Element\Textarea('description');
		$description->setLabel('Description');
		//$description->setOptions(array('wysiwyg' => 1));
		
		$url = new Element\Text('url');
		$url->setLabel('Url');
                
                $status = new Element\Select('status');
		$status->setLabel('Category Status');
		$status->setOptions(array('options' => array('Draft' => 'Draft','Live' => 'Live')));
		
		$this->fieldsetArray = array(
		        'config' => array(
		                'id',
		                'name',
		                'title',
		                'parent_id',
                                'status'
		                  
		        ),
		        'content' => array(
		                'description',
		                'url'
		        ),
		        'images' => array(
		                'image_preview',
		                'image',
		                'image_delete'
		                
		        ),
		        
		        
		);
		

		$this->add($id)
		->add($name)
		->add($title)
		->add($parentId)
		->add($image_preview)
		->add($image)
		->add($image_delete)
		->add($description)
		->add($url)
                ->add($status);
                
		
	}
	
	public function getFieldsetArray()
	{
	    return $this->fieldsetArray;
	}
	
}