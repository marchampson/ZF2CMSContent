<?php
namespace ElmContent\Form;
use Zend\Form\Form as Form;
use Zend\Form\Fieldset;
use Zend\Form\Element as Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;


class WebpageForm extends Form implements InputFilterProviderInterface
{
    protected $parentPages;
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
    
    public function setPageService(\ElmContent\Service\ParentPagesService $service)
    {
        $this->parentPages = $service;
        $parentPagesArray = array(0 => 'Select one');
        $parentPageList = $this->parentPages->getParentPages();
        if(count($parentPageList) > 0) {
            foreach($parentPageList as $k => $v) {
                $parentPagesArray[$k] = $v;
            }
        }
        // If using fieldsets
        //$this->get('config')->get('parent_id')->setOptions(array('options' => $parentPagesArray, 'description' => ''));
        $this->get('parent_id')->setOptions(array('options' => $parentPagesArray, 'description' => ''));
    }
    
    public function setCategoryService(\ElmContent\Service\CategoryService $service)
    {
        $this->categories = $service;
        $uriBits = explode('/',$this->getRequest()->getUri()->getPath());
        $pageId = ((int) $uriBits[count($uriBits) -1]) ? $uriBits[count($uriBits) -1] : null;
        // If using fieldsets
        //$this->get('categories')->get('categorypicker')->setValueOptions($this->categories->getCategoryFormList($pageId));
        $this->get('categorypicker')->setValueOptions($this->categories->getCategoryFormList($pageId));
    }
    
	public function __construct($name = null)
	{
	    // we want to ignore the name passed
	    parent::__construct('webpage');
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
		
		$parentId = new Element\Select('parent_id');
        $parentId->setLabel('Select parent page if applicable');
        $parentId->setOptions(array('options' => array('empty_value' => '')));
		
		$headline = new Element\Text('headline');
		$headline->setLabel('Headline')
		->setAttributes(array('maxlength' => 128));
		
		$status = new Element\Select('status');
		$status->setLabel('Page Status');
		$status->setOptions(array('options' => array('Draft' => 'Draft','Live' => 'Live', 'Private' => 'Private')));
		
		$introduction = new Element\Textarea('introduction');
		$introduction->setLabel('Introductory text');
		$introduction->setOptions(array('wysiwyg' => 1));
		
		$description = new Element\Textarea('description');
		$description->setLabel('Main page content');
		$description->setOptions(array('wysiwyg' => 1));
		
		$main_image_preview = new Element\Image('main_image_preview');
		$main_image_preview->setLabel('Main image preview');
		
		$main_image_delete = new Element\Checkbox('main_image_delete');
		$main_image_delete->setLabel('Delete main image');
		
		$main_image = new Element\File('main_image');
		$main_image->setLabel('Main image');
		
		$main_image_alt = new Element\Text('main_image_alt');
		$main_image_alt->setLabel('Main image ALT value')
		->setAttributes(array('maxlength' => 128, 'class' => 'text'));
		
		$metaTitle = new Element\Text('metaTitle');
		$metaTitle->setLabel('META title');
		
		$metaDescription = new Element\Textarea('metaDescription');
		$metaDescription->setLabel('META description');
		
		$featured = new Element\Checkbox('featured');
		$featured->setLabel('Feature on home page');
		
		$parent_featured = new Element\Checkbox('parent_featured');
		$parent_featured->setLabel('Feature on parent page');
		
		$featured_headline = new Element\Text('featured_headline');
		$featured_headline->setLabel('Featured headline')
		->setAttributes(array('maxlength' => 128));
		
		$featured_description = new Element\Textarea('featured_description');
		$featured_description->setLabel('Featured description');
		
		$featured_image_preview = new Element\Image('featured_image_preview');
		$featured_image_preview->setLabel('Featured image preview');
		$featured_image_preview->setAttribute('src', 'http://e3-electrox.local/modules/elm-content/img/layout/icons/e3-icon.png');
		
		$featured_image_delete = new Element\Checkbox('featured_image_delete');
		$featured_image_delete->setLabel('Delete featured image');
		
		$featured_image = new Element\File('featured_image');
		$featured_image->setLabel('Featured image');
		
		//$submit = new Element\Submit('submit');
		//$submit->setAttribute('value', 'send');
		
		$this->fieldsetArray = array(
		        'config' => array(
		                'id',
		                'name',
		                'status',
		                'parent_id',
		        ),
		        'content' => array(
		                'headline',
		                'introduction',
		                'description'
		        ),
		        'images' => array(
		                'main_image_preview',
                        'main_image',
		                'main_image_delete',
		                'main_image_alt'		                
		        ),
		        'meta' => array(
		                'metaTitle',
		                'metaDescription'
		        ),
		        'featured' => array(
		                'featured_headline',
		                'featured_description',
		                'featured_image_preview',
		                'featured_image',
		                'featured_image_delete',
		                'featured',
		                'parent_featured'
		        ),
		        'categories' => array(
		                'categorypicker'
		        )
		);
		
		$this->add($id)
		    ->add($name)
		    ->add($headline)
		    ->add($status)
		    ->add($parentId)
		    ->add($introduction)
		    ->add($description)
		    ->add($main_image_preview)
		    ->add($main_image)
		    ->add($main_image_delete)
		    ->add($main_image_alt)
		    ->add($metaTitle)
		    ->add($metaDescription)
		    ->add($featured)
		    ->add($parent_featured)
		    ->add($featured_headline)
		    ->add($featured_description)
		    ->add($featured_image)
		    ->add($featured_image_preview)
		    ->add($featured_image_delete)
		    ->add($cp);
		/*
		$config = new Fieldset('config');
		$config->add($id)
		    ->add($name)
		    ->add($headline)
			->add($status)
			->add($parentId)
			->add($featured)
			->add($parent_featured);
		
		$content = new Fieldset('content');
		$content->add($introduction)
			    ->add($description);
		
		$images = new Fieldset('images');
		$images->add($main_image)
    			->add($main_image_delete)
    			->add($main_image_alt);
		
		// commented out main_image_preview
		
		$meta = new Fieldset('meta');
		$meta->add($metaTitle)
			    ->add($metaDescription);

		$categories = new Fieldset('categories');
		$categories->add($cp);
		
		$this->fieldsetArray = array('config','content','images','meta','categories');
		$this->add($config);
		$this->add($content);
		$this->add($images);
		$this->add($meta);
		$this->add($categories);
		//foreach($this->fieldsets as $fieldset) {
		//    $this->add(${$fieldset});
		//}
		*/
	}
	
	public function getFieldsetArray()
	{
	    return $this->fieldsetArray;
	}
	
	/**
	 * @return array
	 */
	public function getInputFilterSpecification()
	{
	    return array(
	            'parent_id' => array(
	                    'required' => false,
	
	            )
	    );
	}
}