<?php
namespace ElmContent\Service;
use ElmContent\Controller\LoginController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;
class LoginControllerFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $services)
	{
		$allServices = $services->getServiceLocator();
		$tableUsers = $allServices->get('users-table');
		$controller = new LoginController();
		$controller->setUsersTable($tableUsers);
		
		return $controller;
	}
}