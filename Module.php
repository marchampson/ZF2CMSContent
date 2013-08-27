<?php

namespace ElmContent;



use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use ElmContent\View\Helper\ElmListFilter;
use ElmContent\View\Helper\News\JsonNewsList;
use ElmContent\Form\View\Helper\FormElement;
use ElmContent\Form\View\Helper\FormCategory;
use ElmContent\Model\Category;
use ElmContent\Model\CategoryTable;
use ElmContent\Model\CategoryAssociationsTable;
use ElmContent\Model\PagesTable;
use ElmContent\Model\Pages;
use ElmContent\Model\Webpage;
use ElmContent\Model\UsersTable;
use ElmContent\Model\User;
use ElmContent\Model\ContentNodesTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container;
use ElmContent\Model\Acl;
use ElmContent\Utilities\Image;
use ElmContent\Utilities\PageUtils;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
                'Zend\Loader\StandardAutoloader' => array(
                        'namespaces' => array(
                                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                        ),
                ),
        );
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('route', array($this, 'onRouteFinish'), -100);
    }
    
    public function onRouteFinish($e)
    {
        $matches = $e->getRouteMatch();
        $controller = $matches->getParam('controller');
        $action = $matches->getParam('action');
        $redirect = FALSE;
        
        // retrieve session info
        $sessionContainer = new Container('login');
        if($sessionContainer->offsetExists('role')) {
            $role = $sessionContainer->offsetGet('role');
        } else {
            $role = 'guest';
        }
        
        
       
        $acl = new Acl();
                if($acl->hasRole($role) && $acl->hasResource($controller)) {
            // We have accounted for this in acl
            if($acl->isAllowed($role, $controller, $action)) {
                //echo 'allowed - no redirect';
            } else {
                //echo 'denied - redirect to login';
                $redirect = TRUE;
            }
        } else {
            // No acl route defined go to 404
            $url = '/page-not-found';
            $response = new \Zend\Http\PhpEnvironment\Response();        
            $response->getHeaders()->addHeaderLine('Location', $url);        
            $response->setStatusCode(302);
            return $response;
            
        }
        // Redirect to the login page if authentication failed. 
        if($redirect) {
            $url = '/elements/login';
            $response = new \Zend\Http\PhpEnvironment\Response();        
            $response->getHeaders()->addHeaderLine('Location', $url);        
            $response->setStatusCode(302);        
            return $response;
        } 
        
    }
    
    public function getViewHelperConfig()
    {
    	return array(
    			'factories' => array(
    					// the array key here is the name you will call the view helper by in your view scripts
    					'elmListFilter' => function($sm) {
    						$locator = $sm->getServiceLocator(); // $sm is the view helper manager, so we need to fetch the main service manager
    						return new ElmListFilter($locator->get('Request'));
    					},
    					'JsonNewsList' => function($sm) {
    					    $helper = new JsonNewsList();
    					    $helper->setPagesTable($sm->getServiceLocator(), 'ElmContent\Model\PagesTable');
    					    $helper->setContentNodesTable($sm->getServiceLocator(), 'ElmContent\Model\ContentNodesTable');
    					    return $helper;
    					},
    			),
    			'invokables' => array(
    			        'formElement' => 'ElmContent\Form\View\Helper\FormElement',
    			        'ElementsNavigation' => 'ElmContent\View\Helper\ElementsNavigation',
    			        'form_category' => 'ElmContent\Form\View\Helper\FormCategory',
    			        'form_page' => 'ElmContent\Form\View\Helper\FormPage',
                        'form_imagelist' => 'ElmContent\Form\View\Helper\FormImageList',
    			        'gravatar' => 'ElmContent\View\Helper\Gravatar',
    			),
    	);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
        		'factories' => array(
                    'ElmContent\Form\WebpageForm' => function ($sm) {
                        $pageService = $sm->get('parentPagesService');
                        $categoryService = $sm->get('categoryService');
                        $request = $sm->get('Request');
                        $form    = new Form\WebpageForm;
                        $form->setRequest($request);
                        $form->setPageService($pageService);
                        $form->setCategoryService($categoryService);
                        
        
                        return $form;
                    },
                    
                    'ElmContent\Model\Webpage' => function ($sm) {
                        $model    = new Model\Webpage;
                        return $model;
                    },

                    'ElmContent\Form\NewsForm' => function ($sm) {
                        $categoryService = $sm->get('categoryService');
                        $pageService = $sm->get('pagePickerService');
                        $request = $sm->get('Request');
                        $form    = new Form\NewsForm;
                        $form->setRequest($request);
                        $form->setPageService($pageService);
                        $form->setCategoryService($categoryService);
                    
                    
                        return $form;

                    },

                    'ElmContent\Form\EventForm' => function ($sm) {
                        $categoryService = $sm->get('categoryService');
                        $pageService = $sm->get('pagePickerService');
                        $request = $sm->get('Request');
                        $form    = new Form\EventForm;
                        $form->setRequest($request);
                        $form->setPageService($pageService);
                        $form->setCategoryService($categoryService);
                    
                    
                        return $form;

                    },
                    
                    'ElmContent\Form\BannerForm' => function ($sm) {
                        $categoryService = $sm->get('categoryService');
                        $request = $sm->get('Request');
                        $form    = new Form\BannerForm;
                        $form->setRequest($request);
                        $form->setCategoryService($categoryService);
                    
                    
                        return $form;
                    
                    },
                    
                    'ElmContent\Form\CategoryForm' => function ($sm) {
                        $service = $sm->get('categoryService');
                        $form    = new Form\CategoryForm;
                        $form->setService($service);
                    
                        return $form;
                    },
                    
                    'ElmContent\Model\PagesTable' => function($sm) {
                        $tableGateway = $sm->get('PagesTableGateway');
                        $table = new PagesTable($tableGateway);
                        return $table;
                    },
                    'PagesTableGateway' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Pages());
                        return new TableGateway('pages',$dbAdapter,null,$resultSetPrototype);
                    },
                    
                    'ElmContent\Model\WebpagesTable' => function($sm) {
                        $tableGateway = $sm->get('WebpagesTableGateway');
                        $table = new PagesTable($tableGateway);
                        return $table;
                    },
                    'WebpagesTableGateway' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Webpage());
                        return new TableGateway('content_nodes',$dbAdapter,null,$resultSetPrototype);
                    },
                    
                    'ElmContent\Model\ContentNodesTable' => function($sm) {
                        $tableGateway = $sm->get('ContentNodesTableGateway');
                        $table = new ContentNodesTable($tableGateway);
                        return $table;
                    },
                    'ContentNodesTableGateway' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new TableGateway('content_nodes',$dbAdapter);
                    },
                    
                    'ElmContent\Model\CategoryTable' => function($sm) {
                        $tableGateway = $sm->get('CategoryTableGateway');
                        $table = new \ElmContent\Model\CategoryTable($tableGateway);
                        return $table;
                    },
                    
                    'CategoryTableGateway' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new \ElmContent\Model\Category());
                        return new TableGateway('categories',$dbAdapter,null,$resultSetPrototype);
                    },
                    
                    'ElmContent\Model\CategoryAssociationsTable' => function($sm) {
                        $tableGateway = $sm->get('CategoryAssociationsTableGateway');
                        $table = new \ElmContent\Model\CategoryAssociationsTable($tableGateway);
                        return $table;
                    },
                    
                    'CategoryAssociationsTableGateway' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new TableGateway('category_associations',$dbAdapter);
                    },
                    
                    'ElmContent\Model\WebpageAssociationsTable' => function($sm) {
                        $tableGateway = $sm->get('WebpageAssociationsTableGateway');
                        $table = new \ElmContent\Model\WebpageAssociationsTable($tableGateway);
                        return $table;
                    },
                    
                    'WebpageAssociationsTableGateway' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        return new TableGateway('webpage_associations',$dbAdapter);
                    },
                    
                    'ElmContent\Model\UsersTable' =>  function($sm) {
                    	$tableGateway = $sm->get('UsersTableGateway');
                    	$table = new UsersTable($tableGateway);
                    	return $table;
                    },
                    'UsersTableGateway' => function ($sm) {
                    	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    	$resultSetPrototype = new ResultSet();
                    	$resultSetPrototype->setArrayObjectPrototype(new User());
                    	return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                    },
                    'image_utility' => function ($sm) {
                        $imageUtility = new Image();
                        $imageUtility->setServiceLocator($sm);
                        return $imageUtility;
                    },
                    
                    'page_utility' => function ($sm) {
                        $pageUtility = new PageUtils();
                        $pageUtility->setServiceLocator($sm);
                        return $pageUtility;
                    },
                    
                    
        		),
        		
        		'invokables' => array(
        				//'ElmContent\Form\ProductForm' => 'ElmContent\Form\ProductForm'
        		)
        );
    }
    
    
}