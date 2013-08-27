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

class Banner implements InputFilterAwareInterface
{
    protected $namespace = 'banner';
    public $status;
    public $startDate;
    public $endDate;
    public $headline;
    public $link;
    public $description;
    
    protected $image;
    protected $video;
    protected $language;
    protected $inputFilter;
    protected $imageDir = '/images/upload/banners';
    protected $videoDir = '/videos/upload/banners';
    
    public function getImageDir()
    {
        return $this->imageDir;
    }

    public function getVideoDir()
    {
        return $this->videoDir;
    }
    
    public function exchangeArray($data)
    {
        $this->language = (isset($data['language'])) ? $data['language'] : 'en';
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->startDate = (isset($data['startDate'])) ? $data['startDate'] : null;
        $this->endDate = (isset($data['endDate'])) ? $data['endDate'] : null;
        $this->link = (isset($data['link'])) ? $data['link'] : null;
        $this->headline = (isset($data['headline'])) ? $data['headline'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->image = (isset($data['image'])) ? $data['image'] : null;
        $this->video = (isset($data['video'])) ? $data['video'] : null;
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
                                            'max' => 128,
                                    ),
                            ),
                    ),
            
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'startDate',
                'required' => false,
                'allowEmpty' => false,    
            )));
            
            $inputFilter->add($factory->createInput(array(
                    'name' => 'endDate',
                    'required' => false,
                    'allowEmpty' => false,
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
    
}
