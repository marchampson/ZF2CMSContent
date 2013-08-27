<?php
namespace HootAndHoller\Model;

use HootAndHoller\Model\Message;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MessageFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $services)
	{
		$config = $services->get('config');
		$message = new Message($config['recipient'], $config['sender'], $config['text']);
		return $message;
	}
}