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
class Image implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $thumbDirs;
    
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
    
    public function setThumbDirs($thumbDirs)
    {
        $this->thumbDirs = $thumbDirs;
    }
    
    /**
     * 
     * @param string $field
     * @param string $imageDir
     * @param array $File
     * @param array $thumbDirs
     * @return boolean
     * 
     * @desc thumbDirs = array(array('dir' => '<relative path>', 'resize' => <int>)
     * 'resize' => 100 = 100%
     * 'resize' => array(800x100) px
     */
    public function uploadImageFile($field, $imageDir, $File, $thumbDirs = array())
    {
        //ini_set('memory_limit', '256M');
        // Override thumbdirs if set in Module.php
        if(count($this->thumbDirs) > 0) {
            $thumbDirs = $this->thumbDirs;
        }
        // Size validation
        $size = new Size(array('max'=>5000000)); // max bytes filesize
        
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        
        //$adapter->setValidators(array($size), $File['name']);
        
        //if($adapter->isValid()) {
            $path = dirname(__DIR__).'/../../../../public'. $imageDir;
            
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
                
                if(count($thumbDirs) > 0) {
                    
                    $thumbnailer = $this->getServiceLocator()->get('WebinoImageThumb');
                    
                    $thumb = $thumbnailer->create($path . '/' . $File['name'], $options = array());
                     
                    foreach($thumbDirs as $thumbDir) {
                        $path = $originalPath . '/' . $thumbDir['dir'];
                        if (! is_dir($path))
                        {
                            umask(0);
                            mkdir($path, 0777);
                        }
                        if(is_array($thumbDir['resize'])) {
                            $thumb->resize($thumbDir['resize'][0], $thumbDir['resize'][1]);
                        } else {
                            $thumb->resizePercent($thumbDir['resize']);
                        }
                        $thumb->save($path . '/' . $File['name']);
                    }
                }
                return true;
            }
        /*    
        } else {
            print_r($File);
            die('foo');
            //$dataError = $adapter->getMessages();
            //$error = array();
            //foreach($dataError as $key => $row) {
            //    $error[] = $row;
            //}
            return false;
            
        }
        */
    }
}