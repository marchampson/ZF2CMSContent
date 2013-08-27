<?php
namespace ElmAdmin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface
{
	public $id;
	public $company;
	public $first_name;
	public $last_name;
	public $email;
	public $password;
	public $role;
	protected $inputFilter;
	
	public function exchangeArray($data)
	{
	    $this->id     = (isset($data['id']))     ? $data['id']     : null;
	    $this->company = (isset($data['copmpany'])) ? $data['company'] : null;
	    $this->first_name  = (isset($data['first_name']))  ? $data['first_name']  : null;
	    $this->last_name  = (isset($data['last_name']))  ? $data['last_name']  : null;
	    $this->email  = (isset($data['email']))  ? $data['email']  : null;
	    $this->password  = (isset($data['password']))  ? $data['password']  : null;
	    $this->role  = (isset($data['role']))  ? $data['role']  : null;
	}
	
	public function getArrayCopy()
	{
	    return get_object_vars($this);
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
	    throw new \Exception("Not used");
	}
	
	public function getInputFilter()
	{
	    if (!$this->inputFilter) {
	        $inputFilter = new InputFilter();
	        $factory     = new InputFactory();
	
	        $inputFilter->add($factory->createInput(array(
	                'name'     => 'id',
	                'required' => true,
	                'filters'  => array(
	                        array('name' => 'Int'),
	                ),
	        )));
	
            /*
             * @todo can we inject current email addresses and then check new email doesn't match?
             */
	        
	        $this->inputFilter = $inputFilter;
	    }
	
	    return $this->inputFilter;
	}
}
