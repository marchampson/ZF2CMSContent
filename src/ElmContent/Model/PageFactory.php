<?php
namespace ElmContent\Model;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ElmContent\Model\WebpageTable;

class PageFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $webpageTable = new WebpageTable();
        $webpageTable->setEntityManager($serviceLocator->get('doctrine.entitymanager.orm_default'));

        return $webpageTable;
    } 
}