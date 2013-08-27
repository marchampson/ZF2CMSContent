<?php
namespace ElmContent\Service;
use ElmContent\Model\UsersTable;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;
class UsersTableFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $services)
	{
		$config = $services->get('config');
		$adapter = new Adapter($config['db']);
		return new UsersTable($adapter);
	}
}