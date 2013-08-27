<?php
namespace ElmContent\Form;
use Zend\Form\Form as Form;
use Zend\Form\Element as Element;
class ChangePasswordForm extends Form{
    
    public function __construct($name = null)
    {
        
        parent::__construct('change-password');
        $this->setAttribute('method', 'post');
	
		$password = new Element\Text('old_password');
		$password->setLabel('Old password')
				 ->setAttributes(array('maxlength' => 64, 'class' => 'text'));
		
		$new_password1 = new Element\Text('new_password1');
		$new_password1->setLabel('New password')
		->setAttributes(array('maxlength' => 64, 'class' => 'text'));
		
		$new_password2 = new Element\Text('new_password2');
		$new_password2->setLabel('Confirm password')
		->setAttributes(array('maxlength' => 64, 'class' => 'text'));
		
		
		$submit = new Element\Submit('submit');
		$submit->setAttribute('value', 'Change password');
		
		
		$this->add($password)
			->add($new_password1)
			->add($new_password2)
			->add($submit);
    }
}