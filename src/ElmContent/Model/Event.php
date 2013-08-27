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

class Event implements InputFilterAwareInterface
{
    protected $namespace = 'event';
    
    
    public $status;
    public $featured;
    public $venue;
    public $headline;
    public $synopsis;
    public $description;
    public $familyFriendly;
    public $start_date;
    public $end_date;
    public $startTime;
    public $endTime;
    public $price;
    public $venue_name;
    public $venue_address_1;
    public $venue_address_2;
    public $venue_town;
    public $venue_county;
    public $venue_postcode;
    public $venue_phone;
    public $venue_email;
    public $venue_website;
    
    public $metaTitle;
    public $metaDescription;
    
    protected $image;
    protected $language;
    protected $inputFilter;
    protected $imageDir = '/images/upload/events';
    
    public function getImageDir()
    {
        return $this->imageDir;
    }
    
    public function exchangeArray($data)
    {
        $this->language = (isset($data['language'])) ? $data['language'] : 'en';
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->featured = (isset($data['featured'])) ? $data['featured'] : null;
        $this->venue = (isset($data['venue'])) ? $data['venue'] : null;
        $this->headline = (isset($data['headline'])) ? $data['headline'] : null;
        $this->synopsis = (isset($data['synopsis'])) ? $data['synopsis'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->familyFriendly = (isset($data['familyFriendly'])) ? $data['familyFriendly'] : null;
        $this->startTime = (isset($data['startTime'])) ? $data['startTime'] : null;
        $this->endTime = (isset($data['endTime'])) ? $data['endTime'] : null;
        $this->price = (isset($data['price'])) ? $data['price'] : null;
        $this->image = (isset($data['image'])) ? $data['image'] : null;
        $this->metaTitle  = (isset($data['metaTitle'])) ? $data['metaTitle'] : null;
        $this->metaDescription  = (isset($data['metaDescription'])) ? $data['metaDescription'] : null;
        $this->venue_name = (isset($data['venue_name'])) ? $data['venue_name'] : null;
        $this->venue_address_1 = (isset($data['venue_address_1'])) ? $data['venue_address_1'] : null;
        $this->venue_address_2 = (isset($data['venue_address_2'])) ? $data['venue_address_2'] : null;
        $this->venue_town = (isset($data['venue_town'])) ? $data['venue_town'] : null;
        $this->venue_county = (isset($data['venue_county'])) ? $data['venue_county'] : null;
        $this->venue_postcode = (isset($data['venue_postcode'])) ? $data['venue_postcode'] : null;
        $this->venue_phone = (isset($data['venue_phone'])) ? $data['venue_phone'] : null;
        $this->venue_email = (isset($data['venue_email'])) ? $data['venue_email'] : null;
        $this->venue_website = (isset($data['venue_website'])) ? $data['venue_website'] : null;
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
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

}
