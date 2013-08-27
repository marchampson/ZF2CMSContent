<?php
/**
 *
 * @author march
 */
namespace ElmContent\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\Adapter as Adapter;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use ElmContent\Model\Pages;
use ElmContent\Model\Webpage;
use ElmContent\Form\WebpageForm;
use ElmContent\Form\WebpageFilter;
use ElmBase\Utilities\Text;
use Zend\Validator\File\Size;
use CgmConfigAdmin\Service\ConfigAdmin as ConfigAdminService;

class ItemController extends AbstractActionController
{
    protected $pagesTable;
    protected $contentNodesTable;
    protected $categoryAssociationsTable;
    /**
     * @var ConfigAdminService
     */
    protected $configAdminService;

    public $namespace;

    public $nodes;

    public $sort;

    public $search;

    public $form;

    public $inputFilter;

    public $keywords = array();

    public $excludeFieldsArray = array();

    protected $mediaFieldsArray;

    public $localeLanguage = 'en';
    
    //private $parentPages;
    
    /*
    public function setParentPagesService($service)
    {
    	$this->parentPages = $service;
	}
	*/
	
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
        
        // Retrieve or define the filter variables
        $this->sort = array(
                'name' => 'asc'
        );
        if ($this->params()->fromPost('sort') != '') {
            $sortBits = explode(' ', $this->params()->fromPost('sort'));
            $this->sort = array(
                    $sortBits[0] => $sortBits[1]
            );
        }
        
        // Search
        if ($this->params()->fromPost('search') != '') {
            $search = $this->params()->fromPost('search');
            
            // Place keyword(s) in an array.
            $this->keywords = explode(' ', $search);
        }
        
        // Build Search
        // Pages table
        $sql = 'select t1.id, t1.name';
        
        // Additional selects and joins for each node
        // Get list nodes from config or default
        if (isset($config[$this->namespace]['list']['nodes']) &&
                 is_array($config[$this->namespace]['list']['nodes'])) {
            $this->nodes = $config[$this->namespace]['list']['nodes'];
        } else {
            $this->nodes = array(
            		'featured',
                    'status',
            );
        }
        if (count($this->nodes) > 0) {
            $i = 2;
            foreach ($this->nodes as $node) {
                $sql .= ", t$i.$node";
                $i ++;
            }
        }
        
        $sql .= ' from (select id, name from pages where namespace = "' .
                 $this->namespace . '"';
        
        if (count($this->keywords) > 0) {
            $sql .= 'and (';
            $i = 0;
            foreach ($this->keywords as $keyword) {
                if ($i > 0) {
                    $sql .= ' or ';
                }
                $sql .= ' name like "%' . $keyword . '%" ';
                $i ++;
            }
            $sql .= ')';
        }
        
        $sql .= ') t1 ';
        
        // Inner Join for every node
        if (count($this->nodes) > 0) {
            $i = 2;
            foreach ($this->nodes as $node) {
                $sql .= ' inner join (select content as ' . $node .
                         ', page_id from content_nodes where node = "' . $node .
                         '") t' . $i . ' on t1.id = t' . $i . '.page_id';
                $i ++;
            }
        }
        
        if (count($this->sort) > 0) {
            $sql .= ' order by ';
            foreach ($this->sort as $k => $v) {
                $sql .= "$k $v ";
            }
        }
        
        // Run query
        //$adapter = new Adapter($config['db']);
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $pages = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        
        // Build up the list json
        $jsonArray = array('headers' => array('name', 'status'));
        
        $pagesArray = array();
        foreach($pages as $page) {
            //print_r($page) . '<br />';
            $pagesArray[] = array(
                    'rowId' => $page->id,
                    'cells' => array(
                                array('value' => $page->name, 'type' => 'string'),
                                array('value' => $page->status, 'type' => 'select', 'options'=> array('Draft', 'Live', 'Private')),
                                array('type' => 'actions',
                                        'actions' => array(
                                                array('url' => '/elements/content/item/edit/'.$this->namespace.'/'.$page->id,
                                                        'type' => 'btn-warning edit',
                                                        'text' => 'Edit'),
                                                array('url' => '/elements/content/item/delete/'.$this->namespace.'/'.$page->id,
                                                        'type' => 'btn-danger delete',
                                                        'text' => 'Delete')
                                
                                
                                        )
                                )
                                 
                   ),     
                    
            );
        }
        $jsonArray['data'] = $pagesArray;        
        $view = new ViewModel(
                array('data' => json_encode($jsonArray)
                ));
        
        // Don't render the layout if this is an ajax call
        if ($this->params()->fromPost('ajax')) {
            $view->setTerminal(true);
        }
        
        return $view;
    }

    /**
     *
     * @return json
     */
    public function togglefeaturedAction ()
    {
        $pageId = $this->params()->fromPost('pageId');
        
        if ($pageId != '') {

            $result = $this->getContentNodesTable()->fetch(array(
            		'page_id' => $pageId,
            		'node' => 'featured',
            		'language' => 'en'));
           
            if (count($result) > 0) {
                $toggle = ($result['content'] == 1) ? 0 : 1;
                
                $this->getContentNodesTable()->saveContentNode($pageId, 'featured', $toggle, 'en');
                
                $result = new JsonModel(
                        array(
                                $toggle
                        ));
                
                return $result;
            }
        }
    }
    
    public function setpagestatusfromlistAction()
    {
        $pageId = $this->params()->fromPost('pageId');
        $status = $this->params()->fromPost('status');
        
        if ($pageId != '') {
        
            $sql = 'update content_nodes set content="' . $status .
            '" where page_id =' . $pageId .
            ' and node="status" and language="en"';
            $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare($sql);
            $rows = $stmt->execute();
            
            $result = new JsonModel(
                    array(
                            $rows
                    ));
    
            return $result;
        }
    }
   

    /**
     * Add new content Item
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction ()
    {
        $config = $this->getServiceLocator()->get('Config');
        $this->namespace = $this->getEvent()
            ->getRouteMatch()
            ->getParam('namespace');
        $data = '';
        
        /*
         * CREATE INSTANCE OF THE FORM AND CONFIGURE
         */
        // When the class is dynamic you must put the full path name in.
        $formName = 'ElmContent\\Form\\' . ucfirst($this->namespace) . 'Form';
        
        // Factory is used to inject dependencies if needed (see Module.php) so
        // we call the form via the serviceLocator
        $this->form = $this->getServiceLocator()->get($formName);
        
        // Pimp the form
        $this->bespokeFields($this->form);
        
        // Remove the preview fields
        $this->form->remove('main_image_preview');
        
        // Set the submit button value
        $this->form->get('submit')->setValue('Add');
        
        // HTTP Request handline
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            // New page
            $page = new Pages();

            // New namespace
            $itemClass = "ElmContent\\Model\\".ucfirst($this->namespace);
            $item = new $itemClass();
            
            // Set InputFilters
            $this->form->setInputFilter($page->getInputFilter());
            $this->form->setInputFilter($item->getInputFilter());
            
            $this->form->setData($request->getPost());
            
            if($this->form->isValid()) {
                
                $page->exchangeArray($this->form->getData());
                
                $item->exchangeArray($this->form->getData());
                                
                $pageId = $this->getPagesTable()->savePage($page, $this->namespace);
                
                // Upload file
                $File = $this->params()->fromFiles('main_image');
                
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('main_image', $item->imageDir . '/' . $pageId, $File)) {
                        $item->main_image = $File['name'];
                    }
                } else {
                    if($this->form->get('preview')) {
                        $storedImage = $this->form->get('preview')->getAttribute('src');
                        $item->main_image = basename($storedImage);
                    }
                }
                
                // Category Associations
                // Delete existing for this pageId
                $this->getCategoryAssociationsTable()->deleteByPageId($pageId);
                // Add associations based on form post
                foreach($this->params()->fromPost() as $k => $v) {
                    if(substr($k,0,9) == 'cat_assoc') {
                        $categoryId = substr($k,10);
                        $this->getCategoryAssociationsTable()->saveCategoryAssociation($categoryId, $pageId);
                    }
                }
                
                // Save content nodes
                foreach($item->getFields() as $node) {
                	// Media File Types
                	
                	// Other file types
                    $this->getContentNodesTable()->saveContentNode($pageId, $node, $item->{$node}, $language = 'en');
                }

                // Redirect to list
                return $this->redirect()->toRoute('page-list');
            } else {
                $message = '1.21 Gigawatts - what was I thinking?!?';
            }
            $data = $this->form->getData();
        }
        
        return new ViewModel(array(
                'form' => $this->form,
                'data' => $data
        ));
    }

    /**
     * Add new content Item
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction ()
    {
        $config = $this->getServiceLocator()->get('Config');
        
        $data = '';
        
        // Edit params
        $id = (int) $this->getEvent()
            ->getRouteMatch()
            ->getParam('id');
        $this->namespace = $this->getEvent()
            ->getRouteMatch()
            ->getParam('namespace');
        
        if (! $id) {
            return $this->redirect()->toRoute('page-list');
        }
        
        $page = $this->getPagesTable()->getPage($id);
        // Get Content Nodes
        
        // New namespace
        $itemClass = "ElmContent\\Model\\".ucfirst($this->namespace);
        $item = new $itemClass();
        
        $formName = 'ElmContent\\Form\\' . ucfirst($this->namespace) . 'Form';

        $this->form = $this->getServiceLocator()->get($formName);

        $this->bespokeFields($this->form);
        
        // Set the standard fields
        $this->form->bind($page);
        
        // Set the content items
        foreach($this->getContentNodesTable()->fetchAll($id, 'en') as $node) {
            if($this->form->get($node['node'])) {

                $this->form->get($node['node'])->setValue($node['content']);
                
                if($this->form->get($node['node'])->getAttribute('type') == 'file') {
                // Check and remove preview fields
	                if($node['content'] == '' || $node['content'] == null) {
						// Remove preview/delete if no content to preview                	
	                	$this->form->remove($node['node'].'_preview');
	                	$this->form->remove($node['node'].'_delete');
	                } else {
	                	// Display content
	                	$this->form->get($node['node'].'_preview')->setAttribute('src',"/images/upload/".$id."/small/".$node['content']);
	                }
                }
            }
        }
        
        $this->form->get('submit')->setValue('Save Changes');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $pageModel = new Pages();
            
            // Set InputFilters
            $this->form->setInputFilter($page->getInputFilter());
            $this->form->setInputFilter($item->getInputFilter());
            
            // Send form data to filter
            $this->form->setData($request->getPost());
            
            // Check valid
            if ($this->form->isValid()) {
                
                
                // Upload file
                $File = $this->params()->fromFiles('main_image');
                
                if($File['name'] != '') {
                    $ImageUtilities = $this->getServiceLocator()->get('image_utility');
                    if($ImageUtilities->uploadImageFile('main_image', $item->imageDir .'/' .$id, $File)) {
                        $item->main_image = $File['name'];
                    }
                } else {
                    if($this->form->get('preview')) {
                        $storedImage = $this->form->get('preview')->getAttribute('src');
                        $item->main_image = basename($storedImage);
                    }
                }
                
                $this->getPagesTable()->savePage($this->form->getData());
                
                // Category Associations
                // Delete existing for this pageId
                $this->getCategoryAssociationsTable()->deleteByPageId($id);
                // Add associations based on form post
                foreach($this->params()->fromPost() as $k => $v) {
                    if(substr($k,0,9) == 'cat_assoc') {
                        $categoryId = substr($k,10);
                        $this->getCategoryAssociationsTable()->saveCategoryAssociation($categoryId, $id);
                    }
                }
                
                // Save content nodes
                foreach($item->getFields() as $node) {
                    $this->getContentNodesTable()->saveContentNode($id, $node, $item->{$node}, $language = 'en');
                    /*
                	switch($node) {
                	
                		case 'main_image':
                			// Check we're not deleting; main_image_delete would be 1 if posted.
                			if($this->params()->fromPost('main_image_delete')) {
                				// Remove the images (Original, small, medium, large)
                				$imageNode =  $this->getContentNodesTable()->getContentNode($id,'main_image');
                				if(is_object($imageNode)) {

                					$imageName = $imageNode->content;
                					
                					unlink(dirname(__DIR__).'/../../../../public/images/upload/' . $id . '/original/' . $imageName);
                					unlink(dirname(__DIR__).'/../../../../public/images/upload/' . $id . '/small/' . $imageName);
                					unlink(dirname(__DIR__).'/../../../../public/images/upload/' . $id . '/medium/' . $imageName);
                					unlink(dirname(__DIR__).'/../../../../public/images/upload/' . $id . '/large/' . $imageName);

                					// Remove the content node reference to image
                					$this->getContentNodesTable()->saveContentNode($id, 'main_image', '', $language = 'en');
                					// Remove the content node alt tag.
                					$this->getContentNodesTable()->saveContentNode($id, 'main_image_alt', '', $language = 'en');
                				}
                			} else {
                				// Check we're actually uploading
                				$this->uploadImageFile($id, $node);
                				// Store alt tag
                				$alt_node = $node . '_alt';
                				
                				$alt_value = ($this->params()->fromPost($alt_node) != '') ? $this->params()->fromPost($alt_node) : $this->params()->fromPost('name');
                				$this->getContentNodesTable()->saveContentNode($id, $alt_node, $alt_value, $language = 'en');
                			}
                			break;
                			
                		default:
                			$this->getContentNodesTable()->saveContentNode($id, $node, $this->params()->fromPost($node), $language = 'en');
                			break;
                	*/
                    
                }
                
                // Reroute back to list
                return $this->redirect()->toRoute('page-list');
            } else {
                $message = '1.21 Gigawatts - what was I thinking?!?';
            }
            $data = $this->form->getData();
            //$data = '';
        }
        
        return new ViewModel(array(
                'id' => $id,
                'form' => $this->form,
                'data' => $data,
        ));
    }
    
    public function uploadImageFile($id, $node)
    {
    	// Image directories
    	$dirs = array('small','medium','large');
    	 
    	// Check we have the file
    	$File = $this->params()->fromFiles($node);
    	
    	// Size validation
    	$size = new Size(array('max'=>2000000)); // max bytes filesize
    	 
    	$adapter = new \Zend\File\Transfer\Adapter\Http();
    	$adapter->setValidators(array($size), $File['name']);
    	if($adapter->isValid()) {
    		$path = dirname(__DIR__).'/../../../../../public/images/upload/' . $id;
    		if (! is_dir($path))
    		{
    			umask(0);
    			if(!@mkdir($path, 0777)) {
    				$error = error_get_last();
    				echo $error['message'] . '<br />' . $path;
    				die('end');
    			}
    			 
    		}
    	
    		$path = dirname(__DIR__).'/../../../../../public/images/upload/' . $id . '/original';
    		if (! is_dir($path))
    		{
    			umask(0);
    			if(!@mkdir($path, 0777)) {
    				$error = error_get_last();
    				echo $error['message'] . '<br />' . $path;
    				die('end');
    			}
    	
    		}
    	
    		$adapter->setDestination($path);
    	
    		if($adapter->receive($File['name'])) {
    	
    			$thumbnailer = $this->getServiceLocator()->get('WebinoImageThumb');
    	
    			foreach($dirs as $dir) {
    				$path = dirname(__DIR__).'/../../../../../public/images/upload/' . $id . '/' . $dir;
    				if (! is_dir($path))
    				{
    					umask(0);
    					mkdir($path, 0777);
    				}
    	
    				$thumb = $thumbnailer->create(dirname(__DIR__).'/../../../../../public/images/upload/' . $id . '/original/' . $File['name'], $options = array());
    	
    				switch($dir) {
    					case 'small':
    						$thumb->resizePercent(25);
    						$thumb->save($path . '/' . $File['name']);
    						break;
    	
    					case 'medium':
    						$thumb->resizePercent(50);
    						$thumb->save($path . '/' . $File['name']);
    						break;
    	
    					case 'large':
    						$thumb->resizePercent(75);
    						$thumb->save($path . '/' . $File['name']);
    						break;
    				}
    	
    			}
    			// Store to content_nodes table
    			$this->getContentNodesTable()->saveContentNode($id, $node, $File['name'], $language = 'en');
    		}
    	} else {
    		$dataError = $adapter->getMessages();
    		$error = array();
    		foreach($dataError as $key => $row) {
    			$error[] = $row;
    		}
    		$this->form->setMessages(array($node => $error));
    	}
    }
    
public function cloneAction ()
    {
    	$config = $this->getServiceLocator()->get('Config');
    	 
    	// Clone params
    	$id = ( int ) $this->getEvent ()->getRouteMatch ()->getParam ( 'id' );
    	$this->namespace = $this->getEvent()->getRouteMatch()->getParam('namespace');
    	
    	if (! $id) {
    		return $this->redirect ()->toRoute ( 'list' );
    	}
    	
    	$page = $this->getPagesTable()->getPage($id);
    	$nodes = $this->getContentNodesTable()->fetchAll($id);
    	$nodes = $nodes->toArray();
    	$nodesArray = array();
    	foreach($nodes as $n) {
    		$nodesArray[$n['node']] = $n['content'];
    	}
    	
    	// New namespace
    	$itemClass = "ElmContent\\Model\\".ucfirst($this->namespace);
    	$item = new $itemClass();
    	
    	if(is_object($page)) {
    
    		$clone = clone $page;
    		$clone->name = 'Copy of '.$page->name;
    		
    		$clone->id = null;
    		$new_id = $this->getPagesTable()->savePage($clone);
    		
    		// Save content nodes
    		foreach($item->getFields() as $node) {
    			// Media File Types

    			// Other file types
    			$this->getContentNodesTable()->saveContentNode($new_id, $node, $nodesArray[$node], $language = 'en');
    		}
    		
    		// Reroute back to list
    		return $this->redirect()->toRoute('page-list');

    	} else {
    		return new ViewModel();
    	}
    }
    
    public function confirmDeleteAction()
    {
    	$id = ( int ) $this->getEvent ()->getRouteMatch ()->getParam ( 'id' );
    	$this->namespace = $this->getEvent()->getRouteMatch()->getParam('namespace');
    	if (! $id) {
    		return $this->redirect ()->toRoute ( 'list' );
    	}
    	
    	// Find the record
    	$page = $this->getPagesTable()->getPage($id);
    	
    	return new ViewModel(array('namespace'=> $this->namespace, 'id' => $id, 'pageName' => $page->name));
    }
    
    public function deleteAction()
    {
    	$id = ( int ) $this->getEvent ()->getRouteMatch ()->getParam ( 'id' );
    	$this->namespace = $this->getEvent()->getRouteMatch()->getParam('namespace');
    	if (! $id) {
    		return $this->redirect ()->toRoute ( 'list' );
    	}
    	
    	// remove all associations
    	$sql = "delete from category_associations where pages_id = $id";
    	$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	$result = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
    	
    	// Content nodes
    	$sql = "delete from content_nodes where page_id = $id";
    	$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	$result = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
    	
    	// Delete the page
		$this->getPagesTable()->deletePage($id);    	
    	
    	return $this->redirect()->toRoute('page-list');
    }

    /**
     * ============== CONTROLLER FUNCTIONS ============== *
     */
    
    /**
     * update the form fields based on settings in namspace.local.php
     * 
     * @param unknown_type $config            
     */
    public function bespokeFields ($form)
    {
        $service = $this->getConfigAdminService();
        
        foreach($form->getElements() as $element) {
            if(!in_array($element->getAttribute('type'),array('hidden','submit'))) {
                if($service->getConfigValue($element->getName(),$element->getName().'Active') == true) {
                    $fieldObject = $form->get($element->getName());
                    $options = $fieldObject->getOptions();
                    if(is_array($options)) {
                        $options['label'] = $service->getConfigValue($element->getName(),$element->getName().'Label');
                        $options['description'] = $service->getConfigValue($element->getName(),$element->getName().'Description');
                        $fieldObject->setOptions($options);
                    }
                } else {
                    $form->remove($element->getName());
                }
            }
        }
    }

    public function setPagePosition (&$page)
    {
        $result = $this->em->getRepository('Entities\Pages')->findLastPositionByNamespace(
                $this->namespace);
        if (count($result) > 0)
            $position = $result[0]->getPosition() + 1;
        
        $page->setPosition($position);
        $this->em->persist($page);
        $this->em->flush();
    }

    public function setContentItemParams ($contentItem, &$page)
    {
        $vars = get_class_vars($contentItem);
        
        foreach ($vars as $key => $val) {
            if (! in_array($key, $this->excludeFieldsArray) &&
                     ! in_array($key, $this->mediaFieldsArray)) {
                // We want to keep the value from the inputFilter unless
                // overwriting
                $value = null;
                
                // Odd balls
                if ($key == 'prettyurl') {
                    $utility = new Text();
                    $value = $utility->prettyUrl(
                            $this->inputFilter->getValue('name'));
                }
                // Longitude and Latitude
                if (in_array($key, array(
                        'longitude',
                        'latitude'
                ))) {
                    // We need a postcode to find the co-ords
                    if ($this->form->getValue('postcode') != '') {
                        // Check we haven't already found the co-ords
                        if (count($this->longLatArray) == 0) {
                            while ($this->longLatArray['Latitude'] == '') {
                                $this->longLatArray = $this->GetLatLong(
                                        $this->form->getValue('postcode'));
                            }
                        }
                        
                        $value = $this->longLatArray[ucfirst($key)];
                    }
                }
                
                $this->updateContentNode($page, $key, $value);
            }
        }
    }

    public function updateContentNode (&$page, $field = null, $value = null)
    {
        if ($field === null) {
            $e = new Zend_Exception();
            die($e);
        }
        
        // Update or replace
        /*
         * @todo translated version
         */
        // $contentNode =
        // $this->em->getRepository('Entities\ContentNodes')->findTranslatedNode($this->form->getValue('id'),$field,$this->localeLanguage);
        
        // START FROM HERE - NEED TO GRAB THE CONTENT NODE FROM DB, REQUIRES THE
        // PAGE ID
        // BELOW SHOWS THAT AS HIDDEN IN FORM? USE $page->getId()????
        
        $contentNode = $this->getEntityManager()
            ->getRepository('\ElmContent\Entity\ContentNodes')
            ->findOneBy(array(
                'page' => $page->getId(),
                'node' => $field
        ));
        
        if (count($contentNode) > 0) {
            // $cn =
            // $this->em->getRepository('Entities\ContentNodes')->findOneById($contentNode[0]->getId());
            $cn = $contentNode;
        } else {
            $cn = new ContentNodes();
        }
        
        $cn->setNode($field);
        
        if ($value === null) {
            // Check for Date fields
            if (strstr(strtolower($field), 'date')) {
                // Create timestamps
                $startDateBits = explode("/", 
                        $this->inputFilter->getValue($field));
                $fieldValue = gmmktime(0, 0, 0, $startDateBits[1], 
                        $startDateBits[0], $startDateBits[2]);
            } else {
                $fieldValue = $this->inputFilter->getValue($field);
            }
        } else {
            $fieldValue = $value;
        }
        $cn->setContent($fieldValue);
        $cn->setLanguage($this->localeLanguage);
        $cn->setPage($page);
        $this->em->persist($cn);
        $this->em->flush();
    }

    public function setPageMedia (&$page)
    {
        /**
         *
         * @todo can't quite decide on this--do I have an action helper that
         *       looks for all media and if one is found in current form, then
         *       action on it?
         *      
         *       if(in_array($this->namespace,array('webpage','product'))) {
         *      
         *       $image = new Zend_Image(
         *       APPLICATION_PATH.'/../public/images/upload/'.$filename,
         *       new Zend_Image_Driver_Gd());
         *       $transform = new Zend_Image_Transform($image);
         *       if($image->getWidth() > $image->getHeight()){
         *      
         *       $transform->fitToWidth($this->mediumwidth)
         *      
         *       ->save(APPLICATION_PATH.'/../public/images/upload/medium/'.$filename);
         *      
         *       $transform->fitToWidth($this->smallwidth)
         *      
         *       ->save(APPLICATION_PATH.'/../public/images/upload/small/'.$filename);
         *       //$transform->center()->middle()->crop(50,50)
         *       $transform->fitToWidth($this->tinywidth)
         *      
         *       ->save(APPLICATION_PATH.'/../public/images/upload/tiny/'.$filename);
         *      
         *       }else{
         *      
         *       $transform->fitToHeight($this->mediumheight)
         *      
         *       ->save(APPLICATION_PATH.'/../public/images/upload/medium/'.$filename);
         *       $transform->fitToWidth($this->smallwidth)
         *      
         *       ->save(APPLICATION_PATH.'/../public/images/upload/small/'.$filename);
         *       //$transform->center()->middle()->crop(50,50)
         *       $transform->fitToWidth($this->tinywidth)
         *      
         *       ->save(APPLICATION_PATH.'/../public/images/upload/tiny/'.$filename);
         *       }
         *       }
         */
        
        // Delete media
        foreach ($this->mediaFieldsArray as $mediaField) {
            if ($this->inputFilter->getValue('delete_' . $mediaField) == 1) {
                $this->updateContentNode($page, $mediaField);
            } else {
                // $size = new Size(array('min'=>2000000)); //minimum bytes
                // filesize
                $File = $this->params()->fromFiles($mediaField);
                
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                
                $adapter->setDestination(
                        dirname(__DIR__) . '/../../../../public/images/upload/' .
                                 $this->namespace);
                $adapter->receive($File['name']);
            }
        }
        
        /*
         * if(is_object($this->form->getElement('media'))) { // upload the media
         * file if ($this->form->media->isUploaded()) {
         * $this->form->media->receive(); // Set the content node
         * $this->updateContentNode($page, 'media', '/uploads/' .
         * basename($this->form->media->getFileName())); } } for($i = 1; $i < 4;
         * $i++) { if(is_object($this->form->getElement('document'.$i))) { //
         * upload the media file $path = APPLICATION_PATH .
         * '/../public/media/upload/' . $page->getId(); if (! is_dir($path)) {
         * umask(0); mkdir($path, 0777); }
         * $this->form->{'document'.$i}->setDestination(APPLICATION_PATH .
         * '/../public/media/upload/' . $page->getId()); if
         * ($this->form->{'document'.$i}->isUploaded()) {
         * $this->form->{'document'.$i}->receive(); $fileInfo =
         * $this->form->{'document'.$i}->getTransferAdapter()->getFileInfo();
         * $source = $fileInfo['document'.$i]['tmp_name']; $destination =
         * $fileInfo['document'.$i]['name']; $saved =
         * move_uploaded_file($source, APPLICATION_PATH .
         * '/../public/media/upload/' . $page->getId().'/'.$destination); } } }
         */
    }

    public function setPageMediaPreviews (&$contentItem)
    {
        if (isset($contentItem->image) && $contentItem->image != "") {
            $this->form->getElement('image_preview')->setImage(
                    $contentItem->image);
            if (is_object($this->form->getElement('main_image_value')))
                $this->form->getElement('main_image_value')->setValue(
                        $contentItem->image);
                // Reset the delete main image marker
            if (is_object($this->form->getElement('delete_main_image')))
                $this->form->getElement('delete_main_image')->setValue(0);
        } else {
            $this->form->removeElement('image_preview');
            $this->form->removeElement('delete_main_image');
        }
        
        if (isset($contentItem->header_image) && $contentItem->header_image != "") {
            $this->form->getElement('header_preview')->setImage(
                    $contentItem->header_image);
            $this->form->getElement('header_image_value')->setValue(
                    $contentItem->header_image);
            // Reset the delete main image marker
            $this->form->getElement('delete_header_image')->setValue(0);
        } else {
            $this->form->removeElement('header_preview');
            $this->form->removeElement('delete_header_image');
        }
        
        if (isset($contentItem->background_image) &&
                 $contentItem->background_image != "") {
            $this->form->getElement('background_preview')->setImage(
                    $contentItem->background_image);
            $this->form->getElement('background_image_value')->setValue(
                    $contentItem->background_image);
            // Reset the delete main image marker
            $this->form->getElement('delete_background_image')->setValue(0);
        } else {
            $this->form->removeElement('background_preview');
            $this->form->removeElement('delete_background_image');
        }
        
        if (isset($contentItem->featured_image) &&
                 $contentItem->featured_image != "") {
            $this->form->getElement('featured_image_preview')->setImage(
                    $contentItem->featured_image);
        } else {
            $this->form->removeElement('featured_image_preview');
        }
        
        if (isset($contentItem->list_image) && $contentItem->list_image != "") {
            $this->form->getElement('list_image_preview')->setImage(
                    $contentItem->list_image);
            $this->form->getElement('list_image_value')->setValue(
                    $contentItem->list_image);
            // Reset the delete main image marker
            $this->form->getElement('delete_list_image')->setValue(0);
        } else {
            $this->form->removeElement('list_image_preview');
            $this->form->removeElement('delete_list_image');
        }
    }
}
