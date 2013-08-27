<?php
/*
 * Use this file to set up any dependencies for the Item Controller
 * 
 */
namespace ElmContent\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryService implements ServiceLocatorAwareInterface
{
	
	protected $serviceLocator;
	
	protected $categoryTable;
	protected $categoryAssociationsTable;
	
	public function getCategoryTable()
	{
	    if(!$this->categoryTable) {
	        $sm = $this->getServiceLocator();
	        $this->categoryTable = $sm->get('ElmContent\Model\CategoryTable');
	    }
	    return $this->categoryTable;
	}
	
	public function getCategoryAssociationsTable()
	{
	    if(!$this->categoryAssociationsTable) {
	        $sm = $this->getServiceLocator();
	        $this->categoryAssociationsTable = $sm->get('ElmContent\Model\CategoryAssociationsTable');
	    }
	    return $this->categoryAssociationsTable;
	}
	
	public function getServiceLocator ()
	{
		return $this->serviceLocator;
	}
	
	public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	public function getParentCategories()
	{
		$categories = $this->getCategoryTable()->fetchAll();
		
		$parentCategoriesArray = array(); // instantiate
		
		if(count($categories) > 0) {
			foreach($categories as $category) {
				$parentCategoriesArray[$category->id] = $category->name;						
			}	
		}
		
		return $parentCategoriesArray;	
	}
	
	public function getCategoryFormList($pageId, $categoryParentId = null)
	{
    
	    if($categoryParentId) {
	        $categories = $this->getCategoryTable()->fetch(array('parent_id' => $categoryParentId));
	    } else {
	        $categories = $this->getCategoryTable()->fetchAll();
	    }
	    

	    if($pageId != null) {
	        // If page id is int then check to see if any have been set
	        if((int) $pageId) {
	            $associated_categories_result = $this->getCategoryAssociationsTable()->findByPageId($pageId);
	            if(count($associated_categories_result) > 0) {
	                $associatedCategoryArray = array();
	                foreach($associated_categories_result as $assocCat) {
	                    $associatedCategoryArray[] = $assocCat['category_id'];
	                }
	            }
	        }
	    }
	    
	    $categoriesArray = array();
	    if(count($categories) > 0) {
	        foreach($categories as $category) {
	            $checked = (isset($associatedCategoryArray) && in_array($category->id,$associatedCategoryArray)) ? 1 : 0;
	            $categoriesArray[] = array('id' => $category->id, 'name'=>$category->name, 'checked' => $checked);
	        }
	    }
	    
	    return $categoriesArray;
	}
	
}