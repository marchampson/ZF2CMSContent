<?php

namespace ElmContent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 

/**
 * @ORM\Entity
 * @ORM\Table(name="category_associations")
 */
class CategoryAssociations implements InputFilterAwareInterface 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    

    /**
     * @ORM\Column(name="category_id", type="string", length=100, nullable=true)
     * @var string
     */
    private $categoryId;

    /**
     * @ORM\Column(name="parent_id", type="string", length=100, nullable=true)
     * @var string
     */
    private $parentId;

   /**
     * Bidirectional - Many tags can be associated with one page (OWNING SIDE)
     *
     * @ORM\ManyToOne(targetEntity="Pages", inversedBy="categories")
     * @ORM\JoinColumn(name="pages_id", referencedColumnName="id")
     */
    private $pages;
    
    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }
    
    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        // $this->id  = $data['id']; //etc
        foreach(get_object_vars($this) as $v) {
            $this->{$v} = $data[$v];
        }
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
    
            $factory = new InputFactory();
    
            $inputFilter->add($factory->createInput(array(
                    'name'       => 'id',
                    'required'   => true,
                    'filters' => array(
                            array('name'    => 'Int'),
                    ),
            )));
    
            $this->inputFilter = $inputFilter;
        }
    
        return $this->inputFilter;
    }
}