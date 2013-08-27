<?php
namespace ElmContent\Model;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Permissions\Acl\Role\GenericRole as Role;
class Acl extends ZendAcl
{
	// maps roles to "users" table status codes
	protected static $_roles = array('guest' => 'guest', 'account' => 'account', 'user' => 'user', 'admin' => 'admin', 'hal9000' => 'hal9000');
	
	public static function getRoleFromStatusCode($statusCode)
	{
		if(isset(self::$_roles[$statusCode])) {
			$role = self::$_roles[$statusCode];
		} else {
			$role = 'guest';
		}
		return $role;
	}
	
	public function __construct()
	{
		$this->addRole('guest')
		    ->addRole('account', 'guest')
			->addRole('user', 'account')
			->addRole('admin', 'user')
		    ->addRole('hal9000', 'admin');
		
		// resources = controllers or controller route mappings
		$this->addResource('page-not-found')
		    ->addResource('admin-user')
		    ->addResource('admin-group')
		    ->addResource('admin-form')
			->addResource('login-index')
			->addResource('Client\Controller\Index')
			->addResource('book')
			->addResource('Client\Controller\Author')
			->addResource('Client\Controller\Collection')
			->addResource('Client\Controller\Supplier')
			->addResource('Client\Controller\BookList')
			->addResource('Client\Controller\Product')
			->addResource('Client\Controller\Directory')
			->addResource('ElmContent\Controller\Webpage')
			->addResource('ElmContent\Controller\Category')
			->addResource('ElmContent\Controller\News')
			->addResource('ElmContent\Controller\Event')
			->addResource('ElmContent\Controller\Banner')
			->addResource('ElmOrderForm\Controller\FormItem')
			->addResource('Client\Controller\Application')
			->addResource('ElmDirectory\Controller\Directory')
			->addResource('Client\Controller\Feed')
			->addResource('Client\Controller\Offer')
			->addResource('Client\Controller\Event')
			->addResource('ElmPortfolio\Controller\Project')
			->addResource('ElmCommerce\Controller\Product')
			->addResource('ElmTrade\Controller\Stock')
			->addResource('Client\Controller\Search')
			->addResource('Client\Controller\AccountDiscount')
			->addResource('Client\Controller\Shipper')
			->addResource('Client\Controller\User')
			->addResource('ElmCommerce\Controller\Basket')
		    ->addResource('home');
		    
		// rights = actions
		$this->allow('guest', 'page-not-found', 'pageopen')
		      ->allow('guest', 'Client\Controller\Search', array('query'))
		      ->allow('guest', 'Client\Controller\Feed')
		      ->allow('guest', 'ElmTrade\Controller\Stock', array('jsonorderlist','index'))
		      ->allow('guest', 'login-index')
		      ->allow('guest', 'Client\Controller\Index')
		      ->allow('guest', 'book', 'title')
		      ->allow('guest', 'Client\Controller\Author', array('list','json'))
		      ->allow('guest', 'ElmOrderForm\Controller\FormItem', array('add-to-order','remove-from-order'))
		      ->allow('guest', 'Client\Controller\Collection', array('index','json','book-list','digital'))
		      ->allow('guest', 'Client\Controller\Event', array('index','open'))
		      ->allow('guest', 'Client\Controller\Directory', array('index'))
		      ->allow('guest', 'Client\Controller\Offer', array('index','open','redeem','print'))
		      ->allow('guest', 'home')
		      ->allow('account', 'ElmTrade\Controller\Stock')
		      ->allow('user', 'ElmContent\Controller\Webpage', array('setpagestatusfromlist','setpagepositionsfromlist', 'setimagepositionsfromlist'))
		      ->allow('user', 'ElmContent\Controller\Webpage')
		      ->allow('user', 'ElmContent\Controller\Category')
		      ->allow('user', 'ElmContent\Controller\News')
		      ->allow('user', 'ElmContent\Controller\Event')
		      ->allow('user', 'ElmContent\Controller\Banner')
		      ->allow('user', 'Client\Controller\Author')
		      ->allow('user', 'Client\Controller\Supplier')
		      ->allow('user', 'Client\Controller\Collection')
		      ->allow('user', 'Client\Controller\BookList')
		      ->allow('user', 'Client\Controller\Product')
		      ->allow('user', 'Client\Controller\Application')
		      ->allow('user', 'Client\Controller\Offer')
		      ->allow('user', 'ElmDirectory\Controller\Directory')
		      ->allow('user', 'ElmPortfolio\Controller\Project')
		      ->allow('user', 'ElmCommerce\Controller\Product')
		      ->allow('admin', 'Client\Controller\Search')
		      ->allow('admin', 'Client\Controller\AccountDiscount')
		      ->allow('admin', 'Client\Controller\Shipper')
		      ->allow('admin', 'Client\Controller\User')
		      ->allow('admin', 'Client\Controller\Event',array('migrate'))
		      ->allow('admin', 'Client\Controller\Offer',array('migrate'))
		      ->allow('user', 'book')
		      ->allow('user', 'admin-user')
		      ->allow('user', 'admin-group')
		      ->allow('hal9000', 'admin-form')
		      ->allow('guest', 'ElmCommerce\Controller\Basket', array('add','view', 'checkout','payment','thankyou','delete', 'quantity'));
		     
		
		
		/**
		 * Change password will need to be user upwards
		 */
		//$this->allow('admin');
	}
}
