<?php
namespace ElmContent\Utilities;

use Zend\Validator\File\Size;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 
 * @author marchampson
 *
 */
class Video implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    
    public function getServiceLocator ()
    {
        return $this->serviceLocator;
    }
    
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function __construct()
    {
    }
    
    /**
     * 
     * @param string $field
     * @param string $videoDir
     * @param array $File
     * @return boolean
     * 
     * @desc thumbDir = array(array('dir' => '<relative path>', 'resize' => <int>)
     */
    public function uploadVideoFile($field, $videoDir, $File)
    {
        // Size validation
        //$size = new Size(array('max'=>5000000)); // max bytes filesize
        
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        
        //$adapter->setValidators(array($size), $File['name']);
        
        //if($adapter->isValid()) {
            $path = dirname(__DIR__).'/../../../../public'. $videoDir;
            
            $originalPath = $path;
            
            if (! is_dir($path))
            {
                umask(0);
                if(!@mkdir($path, 0777)) {
                    $error = error_get_last();
                    echo $error['message'] . '<br />' . $path;
                    echo '<br />';
                    die('end');
                }
        
            }
             
            $adapter->setDestination($path);
             
            if($adapter->receive($File['name'])) {
                
                return true;
            }
    }
}