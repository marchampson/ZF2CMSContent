<?php
/*
 * Use this file to set up any dependencies for the Item Controller
 * 
 */
namespace ElmContent\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PagePickerService implements ServiceLocatorAwareInterface
{
	
	protected $serviceLocator;
	
	protected $pagesTable;
	protected $contentNodesTable;
	protected $webpageAssociationsTable;
	
	public function getPagesTable()
	{
	    if(!$this->pagesTable) {
	        $sm = $this->getServiceLocator();
	        $this->pagesTable = $sm->get('ElmContent\Model\PagesTable');
	    }
	    return $this->pagesTable;
	}
	
	public function getContentNodesTable()
	{
	    if(!$this->contentNodesTable) {
	        $sm = $this->getServiceLocator();
	        $this->contentNodesTable = $sm->get('ElmContent\Model\ContentNodesTable');
	    }
	    return $this->contentNodesTable;
	}
	
	public function getWebpageAssociationsTable()
	{
	    if(!$this->webpageAssociationsTable) {
	        $sm = $this->getServiceLocator();
	        $this->webpageAssociationsTable = $sm->get('ElmContent\Model\WebpageAssociationsTable');
	    }
	    return $this->webpageAssociationsTable;
	}
	
	public function getServiceLocator ()
	{
		return $this->serviceLocator;
	}
	
	public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}

	public function getPages($fields = array())
	{
		
		$pages = $this->getPagesTable()->fetch($fields);
		
		$pageArray = array(); // instantiate
		
		if(count($pages) > 0) {
			foreach($pages as $page) {
				$pageArray[$page->id] = $page->name;						
			}	
		}
		
		return $pageArray;	
	}
	
	public function getPageFormList($pageId, $parentPageId = null, $namespace = 'webpage', $webPageId = null)
	{
    
	    if($parentPageId) {
	        $pages = $this->getPagesTable()->fetch(array('parent_id' => $parentPageId, 'namespace' => $namespace));
	    } else {
	        $pages = $this->getPagesTable()->fetch(array('namespace' => $namespace));
	    }
	    
	    if($webPageId != null) {
	        $pages = $this->getWebpageAssociationsTable()->findByWebpageId($webPageId, $namespace);
	        if(count($pages) > 0) {
	            $pageIdsArray = array();
    	        foreach($pages as $page) {
    	            $pageIdsArray[] = $page->content_id;
    	        }
    	        $pages = $this->getPagesTable()->fetch(array('id' => $pageIdsArray, 'namespace' => $namespace));
	        }
	    }
	    
        
	    if($pageId != null) {
	        // If page id is int then check to see if any have been set
	        if((int) $pageId) {
	            $associated_webpage_result = $this->getWebpageAssociationsTable()->findByContentId($pageId);
	            if(count($associated_webpage_result) > 0) {
	                $associatedWebpagesArray = array();
	                foreach($associated_webpage_result as $associateWebpage) {
	                    $associatedWebpagesArray[] = $associateWebpage['webpage_id'];
	                }
	            }
	        }
	    }
	    
	    
	    $pagesArray = array();
	    if(count($pages) > 0) {
	        foreach($pages as $page) {
	            $checked = (isset($associatedWebpagesArray) && in_array($page->id,$associatedWebpagesArray)) ? 1 : 0;
	            $pagesArray[] = array('id' => $page->id, 'name'=>$page->name, 'checked' => $checked);
	        }
	    }
	    
	    return $pagesArray;
	}
	
	public function getContentNode($pageId, $node, $lang ='en')
	{
	    return $this->getContentNodesTable()->getContentNode($pageId, $node);
	}
	
	
}