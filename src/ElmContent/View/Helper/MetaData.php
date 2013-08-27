<?php
namespace ElmContent\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model;

class MetaData extends AbstractHelper
{
    
	
	public function __construct()
	{
		
	}
	
    public function __invoke()
    {
        
        $title = ($this->view->metaTitle != '') ? $this->view->metaTitle : '';
        $this->view->headTitle($title);
    }
}