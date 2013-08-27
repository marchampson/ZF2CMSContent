<?php
namespace ElmContentTest\Controller;

use ElmContentTest\Bootstrap;
use ElmContent\Controller\CategoryController;
use ElmContent\Utilities\Text;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use PHPUnit_Framework_TestCase;

class CategoryControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new CategoryController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }
    
    public function testGetCategoryTableReturnsAnInstanceOfCategoryTable()
    {
        $this->assertInstanceOf('ElmContent\Model\CategoryTable', $this->controller->getCategoryTable());
    }
    
    public function testAddActionCanBeAccessed()
    {
        $this->routeMatch->setParam('action', 'add');
        $this->routeMatch->setParam('id', '1');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testEditActionCanBeAccessed()
    {
        $this->routeMatch->setParam('action', 'edit');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->routeMatch->setParam('action', 'index');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testEditActionRedirect()
    {
        $this->routeMatch->setParam('action', 'edit');
        
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertEquals(302, $response->getStatusCode());
    }
    /*
    public function testDeleteActionCanBeAccessed()
    {
        $this->routeMatch->setParam('action', 'delete');
        $this->routeMatch->setParam('id', '1');
    
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
    
        $this->assertEquals(200, $response->getStatusCode());
    }
    */
    public function testDeleteActionRedirect()
    {
        $this->routeMatch->setParam('action', 'delete');
    
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
    
        $this->assertEquals(302, $response->getStatusCode());
    }
    
}