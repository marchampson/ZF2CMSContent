<?php
namespace ElmContent\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as BaseFormElement;
use ElmContent\Form\Element\Category;
use ElmContent\Form\Element\Page;
use ElmContent\Form\Element\ImageList;

class FormElement extends BaseFormElement
{
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        } 

        if ($element instanceof Category) {
            $helper = $renderer->plugin('form_category');
           
            return $helper($element);
        }
        
        if ($element instanceof Page) {
            $helper = $renderer->plugin('form_page');
             
            return $helper($element);
        }

        if ($element instanceof ImageList) {
            $helper = $renderer->plugin('form_imagelist');
             
            return $helper($element);
        }

        if ($element instanceof ImageMultiUpload) {
            $helper = $renderer->plugin('form_imagemultiupload');
             
            return $helper($element);
        }


        return parent::render($element);
    }
    
}