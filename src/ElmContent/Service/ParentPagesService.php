<?php
/*
 * Use this file to set up any dependencies for the Item Controller
 * 
 */
namespace ElmContent\Service;
//use ElmContent\Entity\Pages;
//use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ParentPagesService implements ServiceLocatorAwareInterface
{
	
	protected $serviceLocator;
	protected $pagesTable;
	protected $contentNodesTable;

	public function getServiceLocator ()
	{
		return $this->serviceLocator;
	}
	
	public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
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
	
	public function getParentPages()
	{
		
		$pages = $this->getPagesTable()->fetch(array('namespace' => 'webpage'));
		
		$parentPageArray = array(); // instantiate
		
		if(count($pages) > 0) {
			foreach($pages as $page) {
				$parentPageArray[$page->id] = $page->name;						
			}	
		}
		
		return $parentPageArray;	
	}
	
	public function getContentNode($pageId, $node, $lang ='en')
	{
	    return $this->getContentNodesTable()->getContentNode($pageId, $node);
	}
	
}