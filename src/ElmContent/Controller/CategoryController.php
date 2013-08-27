<?php
namespace ElmContent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ElmContent\Model\Category;
use ElmContent\Form\CategoryForm;

class CategoryController extends AbstractActionController
{
	protected $categoryTable;
	
	public function getCategoryTable()
	{
		if(!$this->categoryTable) {
			$sm = $this->getServiceLocator();
			$this->categoryTable = $sm->get('ElmContent\Model\CategoryTable');
		}
		return $this->categoryTable;
	}
	
	public function indexAction()
	{
		return new ViewModel(array(
					'categories' => $this->getCategoryTable()->fetchAll(),
				));
	}
	
	public function listAction ()
	{
	    $config = $this->getServiceLocator()->get('Config');
	
	    $categories = $this->getCategoryTable()->fetchAll(); 
	
	    
	    $categoriesArray = array();
	    
	    foreach($categories as $category) {
	        //print_r($page) . '<br />';
	        $categoriesArray[] = array(
	                'rowId' => $category->id,
	                'heading' => array(
	                        array('type' => 'state',
                                    'state' => array(
                                            array('value'=>strtolower($category->status),
                                                    'type' => 'select',
                                                    'options' => array('draft', 'live')
                                            ),
                                            array('value' => '', 'type' => 'status')
                                            
                                    ),
                                    'span' => 2
        
                            ),
                            array('value' => $category->name, 'type' => 'string', 'span' => 5),
                            array('type' => 'actions',
                                    'span' => 3,
                                    'actions' => array(
                                            array('url' => '/elements/category/edit/'.$category->id,
                                                    'type' => 'edit',
                                                    'text' => 'Edit'),
                                            array('url' => '/elements/category/delete/'.$category->id,
                                                    'type' => 'delete',
                                                    'text' => 'Delete')
                                    ),
        
                            )
	                 
	                ),
	
	        );
	    }
	    
	    $view = new ViewModel(
                array('data'=> json_encode($categoriesArray),
                      'bData' => array('url' => '/elements/category/add', 'text' => 'Add Category', 'namespace' => 'category')
                ));
    
        // Don't render the layout if this is an ajax call
        if ($this->params()->fromPost('ajax')) {
            $view->setTerminal(true);
        }
        $view->setTemplate('elm-content/webpage/list.phtml');
        return $view;
	}
	
	public function addAction()
	{
	    $form = $this->getServiceLocator()->get('ElmContent\Form\CategoryForm');
	    $form->setAttribute('action','/elements/category/add');
	    // Remove image preview and delete fields
	    $form->remove('image_preview');
	    $form->remove('image_delete');

	    // Pimp the form
        $FormUtilities = $this->getServiceLocator()->get('form_utility');
        $FormUtilities->bespokeFields($form, 'ElmContent\Form\CategoryForm');
	    
	    $request = $this->getRequest();
	    if($request->isPost()) {
	        $category = new Category();
	        $form->setInputFilter($category->getInputFilter());
	        $form->setData($request->getPost());
	        
	        if($form->isValid()) {
	            
	            $category->exchangeArray($form->getData());
	            
	            // Upload file
	            $File = $this->params()->fromFiles('image');
	            
	            if($File['name'] != '') {
	                $ImageUtilities = $this->getServiceLocator()->get('image_utility');
	                if($ImageUtilities->uploadImageFile('image', $category->getImageDir(), $File)) {
	                    $image = $File['name'];
	                }
	            }  
	            
	            // Set image value
	            $category->image = $image;
	            
	            // Save category
	            $this->getCategoryTable()->saveCategory($category);
	            
	            // Redirect to list of categories
	            return $this->redirect()->toRoute('category');
	        } 
	    }
	    $this->layout('layout/forms');
	    $view = new ViewModel(array('form' => $form));
	    $view->setTemplate('elm-content/webpage/add.phtml');
	    return $view;
	}
	
	public function editAction()
	{
	    $id = (int) $this->params()->fromRoute('id', 0);
	    if(!$id) {
	        return $this->redirect()->toRoute('category', array(
	                'action' => 'add'
	        ));
	    }
	    
	    $category = $this->getCategoryTable()->getCategory($id);
	    
	    $form = $this->getServiceLocator()->get('ElmContent\Form\CategoryForm');
	    
	    // Remove image preview and delete if no image set
	    if($category->image == '') {
	        $form->remove('image_preview');
	        $form->remove('image_delete');
	    } else {
	        $form->get('image_preview')->setAttribute('src',$category->getImageDir().'/'.$category->image);
	    }
	    $form->bind($category);

	    // Pimp the form
        $FormUtilities = $this->getServiceLocator()->get('form_utility');
        $FormUtilities->bespokeFields($form, 'ElmContent\Form\CategoryForm');

	    $request = $this->getRequest();
	    if($request->isPost()) {
	        $form->setInputFilter($category->getInputFilter());
	        $form->setData($request->getPost());
	        
	        if($form->isValid()) {
	            
	            $data = $form->getData();

	            $image = '';
	            
	            // Upload file
	            $File = $this->params()->fromFiles('image');
	             
	            if($File['name'] != '') {
	                $ImageUtilities = $this->getServiceLocator()->get('image_utility');
	                if($ImageUtilities->uploadImageFile('image', $category->getImageDir(), $File)) {
	                    $image = $File['name'];
	                }
	            } else {
	                if($form->get('image_preview')) {
	                    $storedImage = $form->get('image_preview')->getAttribute('src');
	                    $image = basename($storedImage);
	                }
	            }
	            
	            // Check if we are deleting
	            if($form->get('image_delete')) {
	                if($form->get('image_delete')->getValue() == 1 && $image != '') {
	                    unlink(dirname(__DIR__).'/../../../../public'.$category->getImageDir() .'/'. $image);
	                    $image = '';
	                }
	            }

	            // Set image value
	            $data->image = $image;

	            // Save category
	            $this->getCategoryTable()->saveCategory($data);
	            
	            // Redirect to the list of categories
	            return $this->redirect()->toRoute('category');
	        }
	    }
	    $this->layout('layout/forms');
	    $view = new ViewModel(array(
	            'id' => $id,
	            'form' => $form,
	    ));
	    $view->setTemplate('elm-content/webpage/add.phtml');
	    return $view;
	}
	
	public function deleteAction()
	{
	    $id = (int) $this->params()->fromRoute('id', 0);
	    if(!$id) {
	        return $this->redirect()->toRoute('category');
	    }
	    
	    $request = $this->getRequest();
	    if($request->isPost()) {
	        $del = $request->getPost('del', 'No');
	        
	        if($del == 'Yes') {
	            $id = (int) $request->getPost('id');
	            $this->getCategoryTable()->deleteCategory($id);
 	        }
 	        
 	        // Redirect to list of categories
 	        return $this->redirect()->toRoute('category');
	    }
	    
	    return array(
	            'id' => $id,
	            'category' => $this->getCategoryTable()->getCategory($id)
	    );
	}
	
}