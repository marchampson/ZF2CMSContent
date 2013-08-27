<?php
namespace ElmContent\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;
use Zend\Filter;
use Zend\Filter\StringTrim;
use Zend\Filter\StringToUpper;
use Zend\Validator\Regex;
class ProductFilter
{
	public function prepareFilters()
	{
		$text = new Input('name');
		$text->getValidatorChain()->addValidator(new Validator\StringLength(array(1,128)));
		$text->getFilterChain()->attachByName('StripTags');
		$inputFilter = new InputFilter();
		$inputFilter->add($text);
		
		
							
		return $inputFilter;
	}
}
