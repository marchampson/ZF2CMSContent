<?php
namespace ElmContent\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;
use Zend\Filter;
class LoginFilter
{
	public function prepareFilters()
	{
		$email = new Input('email');
        $email->getValidatorChain()
              ->addValidator(new Validator\EmailAddress());
		
		$password = new Input('password');
		$password->getFilterChain()->attachByName('StripTags');
		$password->setRequired(TRUE);
		
		$inputFilter = new InputFilter();
		$inputFilter->add($email)
					->add($password);
		
		return $inputFilter;
	}
}
