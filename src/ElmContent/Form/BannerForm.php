<?php
namespace ElmContent\Form;
use Zend\Form\Form as Form;
use Zend\Form\Fieldset;
use Zend\Form\Element as Element;

class BannerForm extends Form
{
    protected $categories;
    protected $request;
    protected $fieldsetArray;
    
    //get Request
    public function setRequest($request)
    {
        $this->request = $request;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function setCategoryService(\ElmContent\Service\CategoryService $service)
    {
        $this->categories = $service;
        $uriBits = explode('/',$this->getRequest()->getUri()->getPath());
        $pageId = ((int) $uriBits[count($uriBits) -1]) ? $uriBits[count($uriBits) -1] : null;
        $this->get('categorypicker')->setValueOptions($this->categories->getCategoryFormList($pageId));
    }
    
	public function __construct($name = null)
	{
	    // we want to ignore the name passed
	    parent::__construct('banner');
	    $this->setAttribute('method', 'post');
	    
	    /*
	     * Hidden Elements
	     */
	    $id = new Element\Hidden('id');
	    
	    $cp = new \ElmContent\Form\Element\Category('categorypicker');
	    $cp->setLabel('Categories');
	    
		$name = new Element\Text('name');
		$name->setLabel('Name')
			   ->setAttributes(array('maxlength' => 128));
		$name->setOptions(array('description' => 'Description text goes here'));
		
		$status = new Element\Select('status');
		$status->setLabel('Banner Status');
		$status->setOptions(array('options' => array('Draft' => 'Draft','Live' => 'Live')));
		
		$start_date = new Element\Text('startDate');
		$start_date->setLabel('Display from  (YYYY-MM-D D)');
		
		$end_date = new Element\Text('endDate');
		$end_date->setLabel('Display to (YYYY-MM-DD)');
		
		$headline = new Element\Text('headline');
		$headline->setLabel('Headline or title');
		
		$description = new Element\Textarea('description');
		$description->setLabel('Main content');
		
		$image_preview = new Element\Image('image_preview');
		$image_preview->setLabel('Image preview');
		
		$image_delete = new Element\Checkbox('image_delete');
		$image_delete->setLabel('Delete image');
		
		$image = new Element\File('image');
		$image->setLabel('Banner image');

		$video = new Element\File('video');
		$video->setLabel('Banner video');

		$video_delete = new Element\File('video_delete');
		$video_delete->setLabel('Delete video');
		
		$link = new Element\Text('link');
		$link->setLabel('Clickthrough URL');
		
		//$submit = new Element\Submit('submit');
		//$submit->setAttribute('value', 'send');
		
		$this->fieldsetArray = array(
		        'config' => array(
		                'id',
		                'name',
		                'status',
		                'startDate',
		                'endDate',
		                'link'
		        ),
		        'content' => array(
		                'headline',
		                'description'
		                
		        ),
		        'media' => array(
		                'image_preview',
                        'image',
		                'image_delete',
		                'video',
		                'video_delete'
		        ),
		        'categories' => array(
		                'categorypicker'
		        )
		);
		
		$this->add($id)
		    ->add($name)
		    ->add($status)
		    ->add($start_date)
		    ->add($end_date)
		    ->add($link)
		    ->add($headline)
		    ->add($description)
		    ->add($image_preview)
		    ->add($image)
		    ->add($image_delete)
		    ->add($video)
		    ->add($video_delete)
		    ->add($cp);
		
	}
	
	public function getFieldsetArray()
	{
	    return $this->fieldsetArray;
	}
}