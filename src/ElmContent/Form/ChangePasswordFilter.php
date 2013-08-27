<?php
namespace ElmContent\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;
use Zend\Filter;
use Zend\Filter\StringTrim;

class ChangePasswordFilter
{
    private $passwordToken; 
    
    public function setPasswordToken($token)
    {
        $this->passwordToken = $token;
    }
    
	public function prepareFilters()
	{
		$password = new Input('old_password');
		$password->getFilterChain()->attachByName('StripTags');
		$password->setAllowEmpty(FALSE);
		$password->setRequired(TRUE);
		
		$new_password1 = new Input('new_password1');
		$new_password1->getFilterChain()->attachByName('StripTags');
		$new_password1->setAllowEmpty(FALSE);
		$new_password1->setRequired(TRUE);
		
		$new_password2 = new Input('new_password2');
		$new_password2->getFilterChain()->attachByName('StripTags');
		$new_password2->getValidatorChain()->addValidator(new Validator\Identical('new_password1'));
		$new_password2->setAllowEmpty(FALSE);
		$new_password2->setRequired(TRUE);
		
		$inputFilter = new InputFilter();
		$inputFilter->add($password)
		            ->add($new_password1)
		            ->add($new_password2);
		            
		            
        
							
		return $inputFilter;
	}
}
