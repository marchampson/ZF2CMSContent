<?php

/**
 * 
 * @author marchampson
 *
 */
namespace ElmContent\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use ElmContent\Utilities\Text;

class Webpage implements InputFilterAwareInterface
{
    protected $namespace = 'webpage';
    
    
    public $headline;
    public $status;
    public $introduction;
    public $description;
    public $main_image_alt;
    public $metaTitle;
    public $metaDescription;
    public $featured;
    public $parent_featured;
    public $featured_headline;
    public $featured_description;
    
    
    protected $prettyurl;
    protected $main_image;
    protected $featured_image;
    protected $language;
    protected $inputFilter;
    protected $imageDir = '/images/upload';
    
    public function getImageDir()
    {
        return $this->imageDir;
    }
    
    public function exchangeArray($data)
    {
        $this->language = (isset($data['language'])) ? $data['language'] : 'en';
        $this->headline = (isset($data['headline'])) ? $data['headline'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->main_image = (isset($data['main_image'])) ? $data['main_image'] : null;
        $this->introduction = (isset($data['introduction'])) ? $data['introduction'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->main_image_alt = (isset($data['main_image_alt'])) ? $data['main_image_alt'] : null;
        $this->metaTitle  = (isset($data['metaTitle'])) ? $data['metaTitle'] : null;
        $this->metaDescription  = (isset($data['metaDescription'])) ? $data['metaDescription'] : null;
        $this->featured = (isset($data['featured'])) ? $data['featured'] : null;
        $this->parent_featured = (isset($data['parent_featured'])) ? $data['parent_featured'] : null;
        $this->featured_image = (isset($data['featured_image'])) ? $data['featured_image'] : null;
        $this->featured_headline = (isset($data['featured_headline'])) ? $data['featured_headline'] : null;
        $this->featured_description = (isset($data['featured_description'])) ? $data['featured_description'] : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("not used");
    }
    
    public function getInputFilter()
    {
        if(!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            $inputFilter->add($factory->createInput(array(
                    'name' => 'id',
                    'filters' => array(
                            array('name'=>'Int'),
                    ),
            
            )));
            
            $inputFilter->add($factory->createInput(array(
                    'name' => 'name',
                    'required' => true,
                    'filters' => array(
                            array('name'=>'StripTags'),
                            array('name'=>'StringTrim'),
                    ),
                    'validators' => array(
                            array(
                                    'name' => 'StringLength',
                                    'options' => array(
                                            'encoding' => 'UTF-8',
                                            'min' => 1,
                                            'max' => 100,
                                    ),
                            ),
                    ),
            
            )));
            $inputFilter->add($factory->createInput(array(
                    'name' => 'headline',
                    'required' => true,
                    'filters' => array(
                            array('name'=>'StripTags'),
                            array('name'=>'StringTrim'),
                    ),
                    'validators' => array(
                            array(
                                    'name' => 'StringLength',
                                    'options' => array(
                                            'encoding' => 'UTF-8',
                                            'min' => 1,
                                            'max' => 100,
                                    ),
                            ),
                    ),
            
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

}
