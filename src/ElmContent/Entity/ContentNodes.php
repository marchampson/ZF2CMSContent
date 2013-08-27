<?php

namespace ElmContent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 

/** 
 * @ORM\Entity
 * @ORM\Table(name="content_nodes") 
 * 
 */
class ContentNodes implements InputFilterAwareInterface 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="language", type="string", length=6, nullable=true)
     * @var string
     */
    private $language = 'en';

    /**
     * @ORM\Column(name="node", type="string", length=50, nullable=true)
     * @var string
     */
    private $node;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     * @var string
     */
    private $content;
    
    /**
     * Bidirectional - Many nodes can be associated with one page (OWNING SIDE)
     *
     * @ORM\ManyToOne(targetEntity="Pages", inversedBy="contentNodes")
     */
     private $page;
     
     
     /**
     * @return the $id
     */
    public function getId ()
    {
        return $this->id;
    }

	/**
     * @return the $language
     */
    public function getLanguage ()
    {
        return $this->language;
    }

	/**
     * @return the $node
     */
    public function getNode ()
    {
        return $this->node;
    }

	/**
     * @return the $content
     */
    public function getContent ()
    {
        return $this->content;
    }

	/**
     * @return the $page
     */
    public function getPage ()
    {
        return $this->page;
    }

	/**
     * @param string $language
     */
    public function setLanguage ($language)
    {
        $this->language = $language;
    }

	/**
     * @param string $node
     */
    public function setNode ($node)
    {
        $this->node = $node;
    }

	/**
     * @param string $content
     */
    public function setContent ($content)
    {
        $this->content = $content;
    }

	/**
     * @param field_type $page
     */
    public function setPage ($page)
    {
        $this->page = $page;
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