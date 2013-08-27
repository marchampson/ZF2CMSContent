<?php

namespace ElmContent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 

/**
 * A page record
 * @ORM\Entity
 * @ORM\Table(name="pages")
 * 
 * 
 * @property int $parentId
 * @property string $namespace
 * @property string $language
 * @property string $name
 * @property int $createdDate
 * @property string $createdBy
 * @property string $modifiedDate
 * @property string $modifiedBy
 * @property int $position
 * @property string $role
 * @property int $userId
 * 
 * @property int $id
 * 
 * @author marchampson
 *
 */

class Pages implements InputFilterAwareInterface 
{
     /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
       
    /**
     * @ORM\Column(type="integer", name="parent_id")
     */
    protected $parentId;
    
    /**
     * @ORM\Column(name="namespace", type="string", length=50, nullable=true)
     */
    protected $namespace;

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="date_created", type="integer", nullable=true)
     */
    protected $createdDate;

    /**
     * @ORM\Column(name="created_by", type="string", length=100, nullable=true)
     */
    protected $createdBy;

     /**
     * @ORM\Column(name="date_modified", type="integer", nullable=true)
     */
    protected $modifiedDate;

    /**
     * @ORM\Column(name="modified_by", type="string", length=100, nullable=true)
     */
    protected $modifiedBy;

    /**
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;
    
    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $role;
    
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $userId;
    
    
    /**
     * unidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="ContentNodes", mappedBy="page", cascade={"persist", "remove"})
     */
    protected $contentNodes;
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="CategoryAssociations", mappedBy="pages", cascade={"persist", "remove"})
     */
    protected $categories;
    
 
    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->createdDate = $this->modifiedDate = time();
    	//$this->categories = new ArrayCollection();
       	//$this->tags = new Arraycollection(); 
        //$this->contentNodes = new ArrayCollection();
    }
    
    public function addCategory(Categories $category)
    {
        $category->addPage($this);
        $this->categories[] = $category;
    }
    
    /**
     * @return the $id
     */
    public function getId ()
    {
        return $this->id;
    }
    
	/**
     * @return the $parentId
     */
    public function getParentId ()
    {
        return $this->parentId;
    }

	/**
     * @return the $namespace
     */
    public function getNamespace ()
    {
        return $this->namespace;
    }

	/**
     * @return the $name
     */
    public function getName ()
    {
        return $this->name;
    }

	/**
     * @return the $createdDate
     */
    public function getCreatedDate ()
    {
        return $this->createdDate;
    }

	/**
     * @return the $createdBy
     */
    public function getCreatedBy ()
    {
        return $this->createdBy;
    }

	/**
     * @return the $modifiedDate
     */
    public function getModifiedDate ()
    {
        return $this->modifiedDate;
    }

	/**
     * @return the $modifiedBy
     */
    public function getModifiedBy ()
    {
        return $this->modifiedBy;
    }

	/**
     * @return the $position
     */
    public function getPosition ()
    {
        return $this->position;
    }

	/**
     * @return the $role
     */
    public function getRole ()
    {
        return $this->role;
    }

	/**
     * @return the $userId
     */
    public function getUserId ()
    {
        return $this->userId;
    }

	/**
     * @return the $contentNodes
     */
    public function getContentNodes ()
    {
        return $this->contentNodes;
    }

	/**
     * @return the $categories
     */
    public function getCategories ()
    {
        return $this->categories;
    }

	/**
     * @param number $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }
    
  
	/**
     * @param number $parentId
     */
    public function setParentId ($parentId)
    {
        $this->parentId = $parentId;
    }

	/**
     * @param string $namespace
     */
    public function setNamespace ($namespace)
    {
        $this->namespace = $namespace;
    }

	/**
     * @param string $name
     */
    public function setName ($name)
    {
        $this->name = $name;
    }

	/**
     * @param number $createdDate
     */
    public function setCreatedDate ($createdDate)
    {
        $this->createdDate = $createdDate;
    }

	/**
     * @param string $createdBy
     */
    public function setCreatedBy ($createdBy)
    {
        $this->createdBy = $createdBy;
    }

	/**
     * @param Ambigous <string, number> $modifiedDate
     */
    public function setModifiedDate ($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;
    }

	/**
     * @param string $modifiedBy
     */
    public function setModifiedBy ($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

	/**
     * @param number $position
     */
    public function setPosition ($position)
    {
        $this->position = $position;
    }

	/**
     * @param string $role
     */
    public function setRole ($role)
    {
        $this->role = $role;
    }

	/**
     * @param number $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

	/**
     * @param field_type $contentNodes
     */
    public function setContentNodes ($contentNodes)
    {
        $this->contentNodes = $contentNodes;
    }

	/**
     * @param Categories $categories
     */
    public function setCategories ($categories)
    {
        $this->categories = $categories;
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
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
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
