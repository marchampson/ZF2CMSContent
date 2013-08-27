<?php
namespace ElmContent\Form;
use Zend\Form\Form as Form;
use Zend\Form\Fieldset;
use Zend\Form\Element as Element;

class NewsForm extends Form
{
    protected $categories;
    protected $venue;
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

    public function setPageService(\ElmContent\Service\PagePickerService $service)
    {
        $this->venue = $service;
        $venueArray = array(0 => 'Select one');
        $venueList = $this->venue->getPages(array('namespace' => 'directory'));
        if(count($venueList) > 0) {
            foreach($venueList as $k => $v) {
                $venueArray[$k] = $v;
            }
        }
        $this->get('venue')->setOptions(array('options' => $venueArray, 'description' => ''));
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
	    parent::__construct('news');
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
		
		$headline = new Element\Text('headline');
		$headline->setLabel('Headline')
		->setAttributes(array('maxlength' => 128));
		
		$status = new Element\Select('status');
		$status->setLabel('Page Status');
		$status->setOptions(array('options' => array('Draft' => 'Draft','Live' => 'Live', 'Private' => 'Private')));
		
		$featured = new Element\Checkbox('featured');
		$featured->setLabel('Featured');

		$venue = new Element\Select('venue');
                $venue->setLabel('Select venue if applicable');
                $venue->setOptions(array('options' => array('0' => 'Select one')));
		
		$startDate = new Element\DateSelect('start_date');
		$startDate->setLabel('Date');
		$startDate->setOptions(array(
						'create_empty_option' => true,
						'min_year'            => date('Y'),
						'max_year'            => date('Y') + 5,
						'day_attributes'      => array(
								'style'            => 'width: 22%'
						),
						'month_attributes'    => array(
								'style'            => 'width: 35%'
						),
						'year_attributes'     => array(
								'style'            => 'width: 25%'
						)));
		
		$brief = new Element\Textarea('brief');
		$brief->setLabel('Synopsis');
		
		$description = new Element\Textarea('description');
		$description->setLabel('Main content');
		$description->setOptions(array('wysiwyg' => 1));
		
		$notes_to_editors = new Element\Textarea('notes_to_editors');
		$notes_to_editors->setLabel('Notes to editors');
		$notes_to_editors->setOptions(array('wysiwyg' => 1));
		
		$image_preview = new Element\Image('image_preview');
		$image_preview->setLabel('Image preview');
		
		$image_delete = new Element\Checkbox('image_delete');
		$image_delete->setLabel('Delete image');
		
		$image = new Element\File('image');
		$image->setLabel('Main image');
		
		$image_alt = new Element\Text('image_alt');
		$image_alt->setLabel('Image ALT value')
		->setAttributes(array('maxlength' => 128, 'class' => 'text'));
		
		$list_image_preview = new Element\Image('list_image_preview');
		$list_image_preview->setLabel('List image preview');
		
		$list_image_delete = new Element\Checkbox('list_image_delete');
		$list_image_delete->setLabel('Delete list image');
		
		$list_image = new Element\File('list_image');
		$list_image->setLabel('List image');
		
		$clickthrough = new Element\Text('clickthrough');
		$clickthrough->setLabel('Clickthrough URL');
		
		$metaTitle = new Element\Text('metaTitle');
		$metaTitle->setLabel('META title');
		
		$metaDescription = new Element\Textarea('metaDescription');
		$metaDescription->setLabel('META description');
		
		//$submit = new Element\Submit('submit');
		//$submit->setAttribute('value', 'send');
		
		$this->fieldsetArray = array(
		        'config' => array(
		                'id',
		                'name',
		                'headline',
		                'status',
		                'featured',
		                'venue',
		                'start_date',
		                'clickthrough'
		        ),
		        'content' => array(
		                'brief',
		                'description',
		                'notes_to_editors'
		        ),
		        'images' => array(
		                'image_preview',
                        'image',
		                'image_delete',
		                'image_alt',
		                'list_image_preview',
		                'list_image',
		                'list_image_delete',
		        ),
		        'meta' => array(
		                'metaTitle',
		                'metaDescription'
		        ),
		        'categories' => array(
		                'categorypicker'
		        )
		);
		
		$this->add($id)
		    ->add($name)
		    ->add($headline)
		    ->add($status)
		    ->add($featured)
		    ->add($venue)
		    ->add($startDate)
		    ->add($clickthrough)
		    ->add($brief)
		    ->add($description)
		    ->add($notes_to_editors)
		    ->add($image_preview)
		    ->add($image)
		    ->add($image_delete)
		    ->add($image_alt)
		    ->add($list_image_preview)
		    ->add($list_image)
		    ->add($list_image_delete)
		    ->add($metaTitle)
		    ->add($metaDescription)
		    ->add($cp);
		
	}
	
	public function getFieldsetArray()
	{
	    return $this->fieldsetArray;
	}
}