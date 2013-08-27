<?php
namespace ElmContent\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use ElmContent\Utilities\Text;

class Category implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $title;
    public $prettyUrl;
    public $parent_id;
    public $image;
    public $description;
    public $url;
    public $position;
    public $status;
    protected $inputFilter;
    protected $imageDir = '/images/upload/category';
    
    public function getImageDir()
    {
        return $this->imageDir;
    }
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $textUtils = new Text();
        $this->prettyUrl = $textUtils->prettyUrl($this->name);
        $this->parent_id = (isset($data['parent_id'])) ? $data['parent_id'] : null;
        $this->image = (isset($data['image'])) ? $data['image'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->url = (isset($data['url'])) ? $data['url'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
    }

    public function setImageName($image)
    {
        $this->image = $image;
    }
    
    public function getArraycopy()
    {
        return get_object_vars($this);
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
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
                    'name' => 'parent_id',
                    'required' => false,
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