<?php
namespace ElmContent\Utilities;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter as Adapter;

/**
 *
 * @author marchampson
 *        
 */
class FormUtils implements ServiceLocatorAwareInterface
{

    protected $formsTable;
    protected $formSettingsTable;
    protected $serviceLocator;

    public function getServiceLocator ()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __construct (){}

    
    public function getFormsTable()
    {
        if(!$this->formsTable) {
            $sm = $this->getServiceLocator();
            $this->formsTable = $sm->get('ElmAdmin\Model\FormsTable');
        }
        return $this->formsTable;
    }
    
    public function getFormSettingsTable()
    {
        if(!$this->formSettingsTable) {
            $sm = $this->getServiceLocator();
            $this->formSettingsTable = $sm->get('ElmAdmin\Model\FormSettingsTable');
        }
        return $this->formSettingsTable;
    }

    /**
     * update the form fields based on settings in namspace.local.php
     * 
     * @param unknown_type $config            
     */
    public function bespokeFields ($form, $alias)
    {
        $aliasForm = $this->getFormsTable()->getFormByAlias($alias);
        
        if(count($aliasForm) > 0) {

            $formSettings = $this->getFormSettingsTable()->fetchAll($aliasForm->id);
            foreach($formSettings as $setting) {
                if(strtolower($setting->status) == 'live') {
                    $fieldObject = $form->get($setting->field);
                    if(is_object($fieldObject)) {
                        $options = $fieldObject->getOptions();
                        if(is_array($options)) {
                            if($setting->label != '' && $setting->label != null) {
                                $options['label'] = $setting->label;    
                            }
                            if($setting->description != '' && $setting->description != null) {
                                $options['description'] = $setting->description;    
                            }
                            $fieldObject->setOptions($options);
                        } 
                    }
                } else {
                    $form->remove($setting->field);
                }
            } 
        }

    }
    
}