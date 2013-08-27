<?php
namespace ElmContent\Form;
use Zend\Form\Form as Form;
use Zend\Form\Fieldset;
use Zend\Form\Element as Element;

class EventForm extends Form
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
        $venueArray = array(0 => '00 Select one');
        $venueList = $this->venue->getPages(array('namespace' => 'directory'));
        if(count($venueList) > 0) {
            foreach($venueList as $k => $v) {
                $isVenue = $this->venue->getContentNode($k,'venue');
                if(!in_array($isVenue->content, array(0, NULL))) {
                    $venueArray[$k] = $v;
                }
                
            }
        }
        asort($venueArray);
        $this->get('venue')->setOptions(array('options' => $venueArray, 'description' => ''));
    }

    public function setCategoryService(\ElmContent\Service\CategoryService $service)
    {
        $this->categories = $service;
        $uriBits = explode('/',$this->getRequest()->getUri()->getPath());
        $pageId = ((int) $uriBits[count($uriBits) -1]) ? $uriBits[count($uriBits) -1] : null;
        $this->get('categorypicker')->setValueOptions($this->categories->getCategoryFormList($pageId, 127));
    }
    
	public function __construct($name = null)
	{
	    // we want to ignore the name passed
	    parent::__construct('events');
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
		$status->setLabel('Page Status');
		$status->setOptions(array('options' => array('Draft' => 'Draft','Live' => 'Live', 'Private' => 'Private')));

		$featured = new Element\Checkbox('featured');
		$featured->setLabel('Featured');

		$venue = new Element\Select('venue');
        $venue->setLabel('Select venue if applicable');
        $venue->setOptions(array('options' => array('0' => 'Select one')));
		
		$headline = new Element\Text('headline');
		$headline->setLabel('Headline')
		->setAttributes(array('maxlength' => 128));

		$synopsis = new Element\Textarea('synopsis');
		$synopsis->setLabel('Synopsis (List view)');
		
		$description = new Element\Textarea('description');
		$description->setLabel('Main content');
		$description->setOptions(array('wysiwyg' => 1));

		$familyFriendly = new Element\Checkbox('familyFriendly');
		$familyFriendly->setLabel('Family friendly');
		
		$startDate = new Element\DateSelect('start_date');
		$startDate->setLabel('Start date');
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
		
		
		$endDate = new Element\DateSelect('end_date');
		$endDate->setLabel('End date');
		$endDate->setOptions(array(
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
		
		
		$startTime = new Element\Text('startTime');
		$startTime->setLabel('Start time (HH:MM)');

		$endTime = new Element\Text('endTime');
		$endTime->setLabel('End time (HH:MM)');
		
		$price = new Element\Text('price');
		$price->setLabel('Price');
		
		$image_preview = new Element\Image('image_preview');
		$image_preview->setLabel('Image preview');
		
		$image_delete = new Element\Checkbox('image_delete');
		$image_delete->setLabel('Delete image');
		
		$image = new Element\File('image');
		$image->setLabel('Main image');
		
		$metaTitle = new Element\Text('metaTitle');
		$metaTitle->setLabel('META title');
		
		$metaDescription = new Element\Textarea('metaDescription');
		$metaDescription->setLabel('META description');
		
		$venue_name = new Element\Text('venue_name');
		$venue_name->setLabel('Venue name');
		
		$venue_address_1 = new Element\Text('venue_address_1');
		$venue_address_1->setLabel('Venue address');
		
		$venue_address_2 = new Element\Text('venue_address_2');
		$venue_address_2->setLabel('Venue address 2');
		
		$venue_town = new Element\Text('venue_town');
		$venue_town->setLabel('Venue town');
		
		$venue_county = new Element\Text('venue_county');
		$venue_county->setLabel('Venue county');
		
		$venue_postcode = new Element\Text('venue_postcode');
		$venue_postcode->setLabel('Venue postcode');
		
		$venue_phone = new Element\Text('venue_phone');
		$venue_phone->setLabel('Venue phone');
		
		$venue_email = new Element\Text('venue_email');
		$venue_email->setLabel('Venue email');
		
		$venue_website = new Element\Text('venue_website');
		$venue_website->setLabel('Venue website');
		
		//$submit = new Element\Submit('submit');
		//$submit->setAttribute('value', 'send');
		
		$this->fieldsetArray = array(
		        'config' => array(
		                'id',
		                'name',
		                'status',
		                'featured',
		                'start_date',
		                'end_date',
		                'startTime',
		                'endTime',
		                'familyFriendly'
		        ),
		        'venue' => array(
		                'venue',
		                'venue_name',
		                'venue_address_1',
		                'venue_address_2',
		                'venue_town',
		                'venue_county',
		                'venue_postcode',
		                'venue_phone',
		                'venue_email',
		                'venue_website'
		        ),
		        'content' => array(
		                'headline',
		                'synopsis',
		                'description'
		        ),
		        'images' => array(
		                'image_preview',
                        'image',
		                'image_delete'
		                
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
		    ->add($status)
		    ->add($featured)
		    ->add($venue)
		    ->add($startDate)
		    ->add($endDate)
		    ->add($startTime)
		    ->add($endTime)
		    ->add($familyFriendly)
		    ->add($headline)
		    ->add($synopsis)
		    ->add($description)
		    ->add($image_preview)
		    ->add($image)
		    ->add($image_delete)
		    ->add($metaTitle)
		    ->add($metaDescription)
		    ->add($cp)
		    ->add($venue_name)
		    ->add($venue_address_1)
		    ->add($venue_address_2)
		    ->add($venue_town)
		    ->add($venue_county)
		    ->add($venue_postcode)
		    ->add($venue_phone)
		    ->add($venue_email)
		    ->add($venue_website);
		
	}
	
	public function getFieldsetArray()
	{
	    return $this->fieldsetArray;
	}
}