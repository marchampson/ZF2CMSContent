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

class Pages implements InputFilterAwareInterface
{
    protected $namespace = 'webpage';
    
    public $id;
    public $parent_id;
    public $name;
    public $prettyurl;
    public $date_created;
    public $created_by;
    public $date_modified;
    public $modified_by;
    public $position;
    public $language;
    public $start_date;
    public $end_date;
    
    protected $inputFilter;
    
    /**
     * @return the $namespace
     */
    public function getNamespace ()
    {
        return $this->namespace;
    }

	/**
     * @param string $namespace
     */
    protected function setNamespace ($namespace)
    {
        $this->namespace = $namespace;
    }

	public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->parent_id = (isset($data['parent_id'])) ? $data['parent_id'] : 0;        
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        if (isset($data['name'])) {
            $textUtils = new Text();
            $this->prettyurl = $textUtils->prettyUrl($data['name']);
        } else {
            $this->prettyurl = null;
        }
        $this->date_created = time();
        $this->created_by = (isset($data['created_by'])) ? $data['created_by'] : null;
        $this->date_modified = time();
        $this->modified_by = (isset($data['modified_by'])) ? $data['modified_by'] : null;
        $this->position = (isset($data['position'])) ? $data['position'] : null;
        $this->language = (isset($data['language'])) ? $data['language'] : null;
        $this->start_date = (isset($data['start_date'])) ? $data['start_date'] : null;
        $this->end_date = (isset($data['end_date'])) ? $data['end_date'] : null;
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
                    'required' => true,
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
