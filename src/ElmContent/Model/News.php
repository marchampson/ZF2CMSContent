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

class News implements InputFilterAwareInterface
{
    protected $namespace = 'news';
    
    
    public $headline;
    public $status;
    public $featured;
    public $venue;
    public $brief;
    public $description;
    public $notestoeditors;
    public $image_alt;
    public $clickthrough;
    
    public $metaTitle;
    public $metaDescription;
    
    protected $image;
    protected $list_image;
    protected $language;
    protected $inputFilter;
    protected $imageDir = '/images/upload/news';
    
    public function getImageDir()
    {
        return $this->imageDir;
    }
    
    public function exchangeArray($data)
    {
        $this->language = (isset($data['language'])) ? $data['language'] : 'en';
        $this->headline = (isset($data['headline'])) ? $data['headline'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->featured = (isset($data['featured'])) ? $data['featured'] : null;
        $this->venue = (isset($data['venue'])) ? $data['venue'] : null;
        $this->brief = (isset($data['brief'])) ? $data['brief'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->notestoeditors = (isset($data['notestoeditors'])) ? $data['notestoeditors'] : null;
        $this->image = (isset($data['image'])) ? $data['image'] : null;
        $this->image_alt = (isset($data['image_alt'])) ? $data['image_alt'] : null;
        $this->list_image = (isset($data['list_image'])) ? $data['list_image'] : null;
        $this->clickthrough = (isset($data['clickthrough'])) ? $data['clickthrough'] : null;
        $this->metaTitle  = (isset($data['metaTitle'])) ? $data['metaTitle'] : null;
        $this->metaDescription  = (isset($data['metaDescription'])) ? $data['metaDescription'] : null;
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
