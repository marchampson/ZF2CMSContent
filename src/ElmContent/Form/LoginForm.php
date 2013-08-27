<?php
namespace ElmContent\Form;
use Zend\Form\Form as Form;
use Zend\Form\Element as Element;
class LoginForm extends Form{
    
    public function __construct($name = null)
    {
        
        parent::__construct('login');
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/elements/login');
    
	
		$email = new Element\Text('email');
		$email->setLabel('Email')
				 ->setAttributes(array('maxlength' => 64, 'class' => 'text'));
				 
		$password = new Element\Password('password');
		$password->setLabel('Password')
				 ->setAttributes(array('maxlength' => 64, 'class' => 'text'));
		
		$submit = new Element\Submit('submit');
		$submit->setAttribute('value', 'Login');
		$submit->setAttribute('class', 'btn btn-inverse');
		
		
		$this->add($email)
			->add($password)
			->add($submit);
    }
}