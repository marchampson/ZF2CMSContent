<?php
namespace ElmContent\Controller;
/**
 *
 * @author marchampson
 *
 */
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\Adapter as Adapter;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use ElmContent\Model\Pages;
use ElmContent\Model\Webpage;
use ElmContent\Form\WebpageForm;
use ElmContent\Form\WebpageFilter;
use ElmContent\Utilities\Text;
use ElmContent\Utilities\Image;
use ElmContent\Utilities\PageUtils;
use CgmConfigAdmin\Service\ConfigAdmin as ConfigAdminService;

class WebpageController extends AbstractActionController
{
    protected $pagesTable;
    protected $contentNodesTable;
    protected $categoryAssociationsTable;
    
    /**
     * @var ConfigAdminService
     */
    protected $configAdminService;
    
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
    
    public function getCategoryAssociationsTable()
    {
        if(!$this->categoryAssociationsTable) {
            $sm = $this->getServiceLocator();
            $this->categoryAssociationsTable = $sm->get('ElmContent\Model\CategoryAssociationsTable');
        }
        return $this->categoryAssociationsTable;
    }
    
    /**
     * @return ConfigAdminService
     */
    public function getConfigAdminService()
    {
        if (!$this->configAdminService) {
            $this->configAdminService = $this->getServiceLocator()->get('cgmconfigadmin');
        }
        return $this->configAdminService;
    }
    
    /**
     * @param  ConfigAdminService $service
     * @return ConfigOptionsController
     */
    public function setConfigAdminService(ConfigAdminService $service)
    {
        $this->configAdminService = $service;
        return $this;
    }
    
    public function listAction ()
    {
        $config = $this->getServiceLocator()->get('Config');
    
        /*
         * Get Params namespace options = array( 'keywords' => array(), 'nodes'
                 * => array(), 'sort' => array(), )
        */
        $this->namespace = $slug = $this->getEvent()
        ->getRouteMatch()
        ->getParam('namespace');
        if ($this->namespace == '') {
            $this->namespace = 'webpage';
        }
    
        // Invoke PageController Utilities
        $pageUtils = $this->getServiceLocator()->get('page_utility');
        $pageUtils->setNamespace($this->namespace);
        
        $pagesArray = $pageUtils->getPagesDataArray();

        $view = new ViewModel(
                array('data' => json_encode($pagesArray),
                      'bData' => array('url' => '/elements/webpage/add', 'text' => 'Add Page', 'namespace' => 'webpage')
                ));
    
        // Don't render the layout if this is an ajax call
        if ($this->params()->fromPost('ajax')) {
            $view->setTerminal(true);
        }
        
        return $view;
    }
    
    public function addAction()
    {
        // Get config settings
        $config = $this->getServiceLocator()->get('Config');
        
        $data = ''; // initialise
        
        $form = $this->getServiceLocator()->get('ElmContent\Form\WebpageForm');
                
        // Remove the preview fields
        $form->remove('main_image_preview');
        $form->remove('featured_image_preview');

        // Pimp the form
        $FormUtilities = $this->getServiceLocator()->get('form_utility');
        $FormUtilities->bespokeFields($form, 'ElmContent\Form\WebpageForm');
        
        $request = $this->getRequest();
        if ($request->isPost()) {

            $page = new Pages();
            //$webpage = new \ElmContent\Model\Webpage;
            $webpage = $this->getServiceLocator()->get('ElmContent\Model\Webpage');
        
            // Set InputFilters
            $form->setInputFilter($webpage->getInputFilter());
        
            // Send form data to filter
            $form->setData($request->getPost());
            
            //print_r($request->getPost());
        
            // Check valid
            if ($form->isValid()) {
                
                $data = $form->getData();
                
                $page->exchangeArray($data);
        
                $id = $this->getPagesTable()->savePage($page,'webpage');
        
                // Upload file
                $File = $this->params()->fromFiles('main_image');
                        $image = '';
        
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('main_image', $webpage->getImageDir() .'/' . $id .'/', $File)) {
                        $image = $File['name'];
                    }
                } 
                
                $this->getContentNodesTable()->saveContentNode($id, 'main_image', $image, $language = 'en');
                
                $File = $this->params()->fromFiles('featured_image');
                $image = '';
                
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('featured_image', $webpage->getImageDir() .'/' . $id .'/', $File)) {
                        $image = $File['name'];
                    }
                }
                
                $this->getContentNodesTable()->saveContentNode($id, 'featured_image', $image, $language = 'en');
        
                // Category Associations
                // Add associations based on form post
                foreach($this->params()->fromPost() as $k => $v) {
                    if(substr($k,0,9) == 'cat_assoc') {
                        $categoryId = substr($k,10);
                        $this->getCategoryAssociationsTable()->saveCategoryAssociation($categoryId, $id, 'webpage');
                    }
                }
        
                // Save content nodes
                foreach(get_object_vars($webpage) as $node => $value) {
                    if(is_object($form->get($node))) {
                        $this->getContentNodesTable()->saveContentNode($id, $node, $form->get($node)->getValue(), $language = 'en');
                    }
                }
        
                // Reroute back to list
                return $this->redirect()->toRoute('pages-cms');
            } else {
                $message = '1.21 Gigawatts - what was I thinking?!?';
            }
            $data = $form->getData();
            //$data = '';
        }
        
        $this->layout('layout/forms');
        
        return new ViewModel(array(
                'form' => $form,
                'data' => $data,
        ));
    }
    
    public function editAction()
    {
        // Get config settings
        $config = $this->getServiceLocator()->get('Config');
        
        $data = ''; // initialise
        
        // Edit params
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        
        if(! $id) {
            return $this->redirect()->toRoute('pages-cms');
        }
        
        // We can bind the main page data to the form
        // but content nodes have to be individually populated
        // due to the db structure
        $page = $this->getPagesTable()->getPage($id);

        $webpage = $this->getServiceLocator()->get('ElmContent\Model\Webpage');

        $form = $this->getServiceLocator()->get('ElmContent\Form\WebpageForm');
        
        $form->bind($page);
        
        // Set the content items
        foreach($this->getContentNodesTable()->fetchAll($id, 'en') as $node) {
            if($form->get($node['node'])) {
        
                $form->get($node['node'])->setValue($node['content']);
        
                if($form->get($node['node'])->getAttribute('type') == 'file') {
                    // Check and remove preview fields
                    
                    if($node['content'] == '' || $node['content'] == null) {
                        // Remove preview/delete if no content to preview
                        $form->remove($node['node'].'_preview');
                        $form->remove($node['node'].'_delete');
                    } else {
                        // Display content
                        $form->get($node['node'].'_preview')->setAttribute('src',$webpage->getImageDir().'/'.$id.'/'.$node['content']);
                    }
                }
            }
        }

        // Pimp the form
        $FormUtilities = $this->getServiceLocator()->get('form_utility');
        $FormUtilities->bespokeFields($form, 'ElmContent\Form\WebpageForm');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            // Set InputFilters
            $form->setInputFilter($webpage->getInputFilter());
        
            // Send form data to filter
            $form->setData($request->getPost());
        
            // Check valid
            if ($form->isValid()) {
                
                $data = $form->getData();
                
                $this->getPagesTable()->savePage($data,'webpage');

                $main_image = '';

                // Upload file
                $File = $this->params()->fromFiles('main_image');
        
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('main_image', $webpage->getImageDir() .'/' .$id, $File)) {
                        $main_image = $File['name'];
                    }
                } else {
                    if($form->get('main_image_preview')) {
                        $storedImage = $form->get('main_image_preview')->getAttribute('src');
                        $main_image = basename($storedImage);
                    }
                }
                
                // Check if we are deleting
                if($form->get('main_image_delete')) {
                    if($form->get('main_image_delete')->getValue() == 1 && $main_image != '') {
                        unlink(dirname(__DIR__).'/../../../../public'.$webpage->getImageDir() .'/' . $id .'/'. $main_image);
                        // Remove the content node reference to image
                        $this->getContentNodesTable()->saveContentNode($id, 'main_image', '', $language = 'en');
                        // Remove the content node alt tag.
                        $this->getContentNodesTable()->saveContentNode($id, 'main_image_alt', '', $language = 'en');
                    } else {
                        $this->getContentNodesTable()->saveContentNode($id, 'main_image', $main_image, $language = 'en');
                    }
                } else {
                    $this->getContentNodesTable()->saveContentNode($id, 'main_image', $main_image, $language = 'en');
                }
                
                unset($File);
                
                $featured_image = '';
                
                // Upload file
                $File = $this->params()->fromFiles('featured_image');
                
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('featured_image', $webpage->getImageDir() .'/' .$id, $File)) {
                        $featured_image = $File['name'];
                    }
                } else {
                    if($form->get('featured_image_preview')) {
                        $storedImage = $form->get('featured_image_preview')->getAttribute('src');
                        $featured_image = basename($storedImage);
                    }
                }
                
                // Check if we are deleting
                if($form->get('featured_image_delete')) {
                    if($form->get('featured_image_delete')->getValue() == 1 && $featured_image != '') {
                        unlink(dirname(__DIR__).'/../../../../public'.$webpage->getImageDir() .'/' . $id .'/'. $featured_image);
                        // Remove the content node reference to image
                        $this->getContentNodesTable()->saveContentNode($id, 'featured_image', '', $language = 'en');
                        
                    } else {
                        $this->getContentNodesTable()->saveContentNode($id, 'featured_image', $featured_image, $language = 'en');
                    }
                } else {
                    $this->getContentNodesTable()->saveContentNode($id, 'featured_image', $featured_image, $language = 'en');
                }
                
                // Category Associations
                // Delete existing for this pageId
                $this->getCategoryAssociationsTable()->deleteByPageId($id, 'webpage');
                // Add associations based on form post
                foreach($this->params()->fromPost() as $k => $v) {
                    if(substr($k,0,9) == 'cat_assoc') {
                        $categoryId = substr($k,10);
                        $this->getCategoryAssociationsTable()->saveCategoryAssociation($categoryId, $id, 'webpage');
                    }
                }
                
                // Save content nodes
                foreach(get_object_vars($webpage) as $node => $value) {
                    if(is_object($form->get($node))) {
                        $this->getContentNodesTable()->saveContentNode($id, $node, $form->get($node)->getValue(), $language = 'en');
                    }
                }
        
                // Reroute back to list
                return $this->redirect()->toRoute('pages-cms');
            } else {
                $message = '1.21 Gigawatts - what was I thinking?!?';
            }
            $data = $form->getData();
            //$data = '';
        }
        
        $this->layout('layout/forms');
        $view = new ViewModel(array(
                'id' => $id,
                'form' => $form,
                'data' => $data,
        ));
        $view->setTemplate('elm-content/webpage/add.phtml');        
        return $view; 
    }
    
    public function openAction()
    {
        $prettyurl = $this->getEvent()->getRouteMatch()->getParam('prettyurl');
        
        if(! $prettyurl) {
            return $this->redirect()->toRoute('client-404');
        }
        
        $page = $this->getPagesTable()->fetch(array('prettyurl' => $prettyurl));
        if($page->id != '') {
            $contentNodes = $this->getContentNodesTable()->fetchAll($page->id, 'en');
        }
    }
    
    public function confirmDeleteAction()
    {
        $id = ( int ) $this->getEvent ()->getRouteMatch ()->getParam ( 'id' );
        if (! $id) {
            return $this->redirect ()->toRoute ( 'pages-cms' );
        }
         
        // Find the record
        $page = $this->getPagesTable()->getPage($id);
         
        return new ViewModel(array('id' => $id, 'pageName' => $page->name));
    }
    
    public function deleteAction()
    {
        $id = ( int ) $this->getEvent ()->getRouteMatch ()->getParam ( 'id' );
        if (! $id) {
            return $this->redirect ()->toRoute ( 'pages-cms' );
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                // remove all associations
                $this->getCategoryAssociationsTable()->deleteByPageId($id, 'webpage');
                 
                // Content nodes
                $sql = "delete from content_nodes where page_id = $id";
                $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                $result = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
                 
                // Delete the page
                $this->getPagesTable()->deletePage($id);
                 
               
            }
        
            // Redirect to page list
            return $this->redirect()->toRoute('pages-cms');
        }
        
        return array(
                'id' => $id,
                'page' => $this->getPagesTable()->getPage($id)
        );
    }
    
    /* ================= AJAX Functions ================== */
    
    public function setpagestatusfromlistAction()
    {
        $pageId = $this->params()->fromPost('pageId');
        $status = $this->params()->fromPost('status');
    
        if ($pageId != '' && $status != '') {
            try {
                $this->getContentNodesTable()->saveContentNode($pageId, 'status', $status, $language = 'en');
                $result = true;
            } catch (Exception $e) {
                $result = false;
            }
        }
        
        $view = new JsonModel(array($result));
        $view->setTerminal(true);
        return $view;
    }
    
    public function setpagepositionsfromlistAction()
    {
        $positions = $this->params()->fromPost('positions');
        
        $return = true;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        foreach($positions as $k => $v) {
            $page = $this->getPagesTable()->getPage($v);
            if($page) {
                try {
                    $sql = "update pages set position = $k where id = $v";
                    $result = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
                } catch(Exception $e) {
                    $return = false;
                }
            }
            
        }
    
        $view = new JsonModel(array($return));
        $view->setTerminal(true);
        return $view;
    }

    public function setimagepositionsfromlistAction()
    {
        $positions = $this->params()->fromPost('positions');
        $pageId = $this->params()->fromPost('pageId');
        
        $return = true;

        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        foreach($positions as $k => $v) {
            try {
                $sql = "update page_images set position = $k where image = '$v' and page_id = $pageId";
                $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);           
            } catch(Exception $e) {
                $return = false;
            }
        }
    
        $view = new JsonModel(array($return));
        $view->setTerminal(true);
        return $view;
    }
}

?>