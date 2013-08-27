<?php

/**
 * 
 * @author marchampson
 *
 */
namespace ElmContent\Form\Element;

use Zend\Form\Element;
use Zend\Form\ElementInterface;

class ImageList extends Element implements ElementInterface
{
    protected $valueOptions = array();
    protected $pageId;
    protected $imageDir;
    protected $images;
    
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'image-list',
        'name' => 'imagelist',
        'options' => array()   
    ); 

    public function setImageDir($imageDir)
    {
        $this->imageDir = $imageDir;
        return $this;
    }
    
    public function getImageDir()
    {
        return $this->imageDir;
    }

    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }
    
    public function getImages()
    {
        return $this->images;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
        return $this;
    }
    
    public function getPageId()
    {
        return $this->pageId;
    }
    
    /**
     * @param  array $options
     * @return Select
     */
    
    public function setValueOptions(array $options)
    {
        $this->valueOptions = $options;
        return $this;
    }
    
    public function getValueOptions()
    {
        return $this->valueOptions;
    }
    
}