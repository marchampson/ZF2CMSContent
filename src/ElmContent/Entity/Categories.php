<?php

namespace ElmContent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 

/** 
 * @ORM\Entity
 * @ORM\Table(name="categories") 
 * 
 */

class Categories implements InputFilterAwareInterface 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(name="prettyurl", type="string", length=100, nullable=true)
     * @var string
     */
    private $prettyUrl;

    /**
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     * @var integer
     */
    private $parentId;

    /**
     * @ORM\Column(name="slug", type="string", length=100, nullable=true)
     * @var string
     */
    private $slug;

    /**
     * @Column(name="thumbnail", type="string", length=255, nullable=true)
     * @var string
     */
    private $thumbNail;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(name="access_level", type="string", length=50, nullable=true)
     * @var string
     */
    private $accessLevel;

    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     * @var string
     */
    private $url;

    /**
     * @ORM\Column(name="position", type="integer", nullable=false)
     * @var integer
     */
    private $position;
    
    /** 
     * @ORM\ManyToMany(targetEntity="Pages", mappedBy="categories")
     */  
    private $pages;
    
    /**
     * Entity constructor
     */
    
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