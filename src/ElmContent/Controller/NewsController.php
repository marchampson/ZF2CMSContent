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
use ElmContent\Model\Pages;
use ElmContent\Model\Webpage;
use ElmContent\Form\WebpageForm;
use ElmContent\Form\WebpageFilter;
use ElmContent\Utilities\Text;
use ElmContent\Utilities\Image;
use CgmConfigAdmin\Service\ConfigAdmin as ConfigAdminService;

class NewsController extends AbstractActionController
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
    
        $this->namespace = 'news';
        
        // Invoke PageController Utilities
        $pageUtils = $this->getServiceLocator()->get('page_utility');
        $pageUtils->setNamespace($this->namespace);
        
        $pagesArray = $pageUtils->getPagesDataArray();

        $view = new ViewModel(
                array('data' => json_encode($pagesArray),
                      'bData' => array('url' => '/elements/news/add', 'text' => 'Add Article', 'namespace' => 'news')
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
        // Get config settings
        $config = $this->getServiceLocator()->get('Config');
        
        $data = ''; // initialise
        
        $form = $this->getServiceLocator()->get('ElmContent\Form\NewsForm');
        
        // Remove the preview fields
        $form->remove('image_preview');
        $form->remove('list_image_preview');

        // Pimp the form
        $FormUtilities = $this->getServiceLocator()->get('form_utility');
        $FormUtilities->bespokeFields($form, 'ElmContent\Form\NewsForm');
        
        $request = $this->getRequest();
        if ($request->isPost()) {

            $page = new Pages();
            $news = new \ElmContent\Model\News;
        
            // Set InputFilters
            $form->setInputFilter($news->getInputFilter());
        
            // Send form data to filter
            $form->setData($request->getPost());
            
            //print_r($request->getPost());
        
            // Check valid
            if ($form->isValid()) {
                
                $data = $form->getData();
                
                $page->exchangeArray($data);
        
                $id = $this->getPagesTable()->savePage($page,'news');
        
                // Upload files
                $File = $this->params()->fromFiles('image');
                $image = '';
        
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('main_image', $news->getImageDir() .'/' . $id .'/', $File)) {
                        $image = $File['name'];
                    }
                } 
                
                $this->getContentNodesTable()->saveContentNode($id, 'image', $image, $language = 'en');
                
                $File = $this->params()->fromFiles('list_image');
                $list_image = '';
                
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('list_image', $news->getImageDir() .'/' . $id .'/', $File)) {
                        $list_image = $File['name'];
                    }
                }
                
                $this->getContentNodesTable()->saveContentNode($id, 'list_image', $list_image, $language = 'en');
                
        
                // Category Associations
                // Add associations based on form post
                foreach($this->params()->fromPost() as $k => $v) {
                    if(substr($k,0,9) == 'cat_assoc') {
                        $categoryId = substr($k,10);
                        $this->getCategoryAssociationsTable()->saveCategoryAssociation($categoryId, $id, 'news');
                    }
                }
        
                // Save content nodes
                foreach(get_object_vars($news) as $node => $value) {
                    if(is_object($form->get($node))) {
                        $this->getContentNodesTable()->saveContentNode($id, $node, $form->get($node)->getValue(), $language = 'en');
                    }
                }
        
                // Reroute back to list
                return $this->redirect()->toRoute('news-cms');
            } else {
                $message = '1.21 Gigawatts - what was I thinking?!?';
            }
            $data = $form->getData();
            //$data = '';
        }
        
        $this->layout('layout/forms');
        
        $view = new ViewModel(array(
                'form' => $form,
                'data' => $data,
        ));
        $view->setTemplate('elm-content/webpage/add.phtml');
        return $view;
    }
    
    public function editAction()
    {
        // Get config settings
        $config = $this->getServiceLocator()->get('Config');
        
        $data = ''; // initialise
        
        // Edit params
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        
        if(! $id) {
            return $this->redirect()->toRoute('news-cms');
        }
        
        // We can bind the main page data to the form
        // but content nodes have to be individually populated
        // due to the db structure
        $page = $this->getPagesTable()->getPage($id);
        $news = new \ElmContent\Model\News;
        
        // Image fields used
        $imageFields = array('image', 'list_image');
        
        $form = $this->getServiceLocator()->get('ElmContent\Form\NewsForm');
        
        $form->bind($page);
        
        // Set the content items
        $contentNodes = $this->getContentNodesTable()->fetchAll($id, 'en');
        foreach($contentNodes as $node) {
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
                        $form->get($node['node'].'_preview')->setAttribute('src',$news->getImageDir().'/'.$id.'/'.$node['content']);
                    }
                }
            }
        }

        // Pimp the form
        $FormUtilities = $this->getServiceLocator()->get('form_utility');
        $FormUtilities->bespokeFields($form, 'ElmContent\Form\NewsForm');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Set InputFilters
            $form->setInputFilter($news->getInputFilter());
        
            // Send form data to filter
            $form->setData($request->getPost());
        
            // Check valid
            if ($form->isValid()) {
                
                $data = $form->getData();
                
                $this->getPagesTable()->savePage($data,'news');

                $image = '';
                // Upload file
                $File = $this->params()->fromFiles('image');
        
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile("image", $news->getImageDir() .'/' .$id, $File)) {
                        $image = $File['name'];
                    }
                } else {
                    if($form->get("image_preview")) {
                        $storedImage = $form->get("image_preview")->getAttribute('src');
                        $image = basename($storedImage);
                    }
                }
                
                // Check if we are deleting
                if($form->get("image_delete")) {
                    if($form->get("image_delete")->getValue() == 1 && $image != '') {
                        unlink(dirname(__DIR__).'/../../../../public'.$news->getImageDir() .'/' . $id .'/'. $image);
                        // Remove the content node reference to image
                        $this->getContentNodesTable()->saveContentNode($id, "image", '', $language = 'en');
                        // Remove the content node alt tag.
                        $this->getContentNodesTable()->saveContentNode($id, "image", '', $language = 'en');
                    } else {
                        $this->getContentNodesTable()->saveContentNode($id, "image", $image, $language = 'en');
                    }
                } else {
                    $this->getContentNodesTable()->saveContentNode($id, "image", $image, $language = 'en');
                }
                unset($File);
                $image = '';
                // Upload file
                $File = $this->params()->fromFiles('list_image');
                
                if($File['name'] != '') {
                    //$ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile("list_image", $news->getImageDir() .'/' .$id, $File)) {
                        $image = $File['name'];
                    }
                } else {
                    if($form->get("list_image_preview")) {
                        $storedImage = $form->get("list_image_preview")->getAttribute('src');
                        $image = basename($storedImage);
                    }
                }
                
                // Check if we are deleting
                if($form->get("list_image_delete")) {
                    if($form->get("list_image_delete")->getValue() == 1 && $image != '') {
                        unlink(dirname(__DIR__).'/../../../../public'.$news->getImageDir() .'/' . $id .'/'. $image);
                        // Remove the content node reference to image
                        $this->getContentNodesTable()->saveContentNode($id, "list_image", '', $language = 'en');
                        // Remove the content node alt tag.
                        $this->getContentNodesTable()->saveContentNode($id, "list_image", '', $language = 'en');
                    } else {
                        $this->getContentNodesTable()->saveContentNode($id, "list_image", $image, $language = 'en');
                    }
                } else {
                    $this->getContentNodesTable()->saveContentNode($id, "list_image", $image, $language = 'en');
                }
                    
                
                // Category Associations
                // Delete existing for this pageId
                $this->getCategoryAssociationsTable()->deleteByPageId($id, 'news');
                // Add associations based on form post
                foreach($this->params()->fromPost() as $k => $v) {
                    if(substr($k,0,9) == 'cat_assoc') {
                        $categoryId = substr($k,10);
                        $this->getCategoryAssociationsTable()->saveCategoryAssociation($categoryId, $id, 'news');
                    }
                }
                
                // Save content nodes
                foreach(get_object_vars($news) as $node => $value) {
                    if(is_object($form->get($node))) {
                        $this->getContentNodesTable()->saveContentNode($id, $node, $form->get($node)->getValue(), $language = 'en');
                    }
                }
        
                // Reroute back to list
                return $this->redirect()->toRoute('news-cms');
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
            return $this->redirect ()->toRoute ( 'news-cms' );
        }
         
        // Find the record
        $page = $this->getPagesTable()->getPage($id);
         
        return new ViewModel(array('id' => $id, 'pageName' => $page->name));
    }
    
    public function deleteAction()
    {
        $id = ( int ) $this->getEvent ()->getRouteMatch ()->getParam ( 'id' );
        if (! $id) {
            return $this->redirect ()->toRoute ( 'news-cms' );
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                // remove all associations
                $this->getCategoryAssociationsTable()->deleteByPageId($id, 'news');
                 
                // Content nodes
                $sql = "delete from content_nodes where page_id = $id";
                $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                $result = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
                 
                // Delete the page
                $this->getPagesTable()->deletePage($id);
                 
               
            }
        
            // Redirect to page list
            return $this->redirect()->toRoute('news-cms');
        }
        
        return array(
                'id' => $id,
                'page' => $this->getPagesTable()->getPage($id)
        );
    }
}