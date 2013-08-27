<?php
namespace ElmContent\Controller;
use ElmContent\Form\LoginForm;
use ElmContent\Form\LoginFilter;
use ElmContent\Form\ChangePasswordForm;
use ElmContent\Form\ChangePasswordFilter;
use Zend\Db\Adapter\Adapter as Adapter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as AuthStorage;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use ElmContent\Model\User;
use Zend\Session\Container;
use ElmContent\Model\Acl;
use Zend\Mail\Message as Message;
use Zend\Mail\Transport\Sendmail;

class LoginController extends AbstractActionController
{
	protected $usersTable;
	protected $_sessionContainer;
	protected $_maxInvalidAttempts = 3;
	
	// create "getSessionContainer()"
	protected function _getSessionContainer()
	{
		if(!$this->_sessionContainer) {
			$this->_sessionContainer = new Container('login');
		}
		return $this->_sessionContainer;
	}
	
	protected function _setInvalidLoginCounter($reset = FALSE)
	{
		$sessionContainer = $this->_getSessionContainer();
		if($sessionContainer->offsetExists('counter')) {
			if($reset) {
				$sessionContainer->offsetSet('counter', 0);
			}
			$count = $sessionContainer->offsetGet('counter');
		} else {
			$count = 0;
		}
		$sessionContainer->offsetSet('counter', ++$count);
		return $count;
	}
	
	public function formauthenticationAction()
	{
	    $request = $this->getRequest();
	    if($request->isPost()) {
	        
	        $email = $this->params()->fromPost('email');
	        $password = $this->params()->fromPost('password');    
	        
	        if($email != '' and $password != '') {
	    
	            $authService = $this->getAuthService();
	            $authAdapter = $authService->getAdapter();
	    
	            $authAdapter->setIdentity($email);
	            $authAdapter->setCredential(md5($password));
	    
	            $attempt = $authService->authenticate();
	    
	            if($attempt->isValid()) {
	                	
	                $userRow = $authAdapter->getResultRowObject();
	                $firstName = ($userRow->first_name != '') ? $userRow->first_name : '';
	                $lastName = ($userRow->last_name != '') ? $userRow->last_name : '';
	                $jsonArray = array('contact_name'=>$firstName . ' ' . $lastName);
	                $jsonArray['contact_email'] = $email;
	                $jsonArray['phone'] = $userRow->phone;
	                $jsonArray['ext'] = $userRow->extension;
	                if($userRow->group_id > 0) {
	                    $sql = "select * from groups where id = ".$userRow->group_id;
	                    $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
	                    $group = $dbAdapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
	                    if(count($group) > 0) {
	                        $groupArr = $group->toArray();
	                        $group = $groupArr[0];
	                        foreach($group as $key => $val) {
	                            $userRow->{$key} = $val;
	                            $jsonArray[$key] = $val;
	                        }
	                    }
	                }
	                $authStorage = $authService->getStorage();
	                $authStorage->write($userRow);
	                // Write additional group information to user row
	                	
	                $sessionContainer = $this->_getSessionContainer();
	                $sessionContainer->offsetSet('role', Acl::getRoleFromStatusCode($userRow->role));
	                
                    $result = new JsonModel(array('valid' => true, 'data' => $jsonArray));
                    
                    
	            } else {
	                $result = new JsonModel(array('valid' => false));
	            }
	            $result->setTerminal(true);
                return $result;
	        }
	    }
	}
	
	public function indexAction()
	{
		$data = '';
		$request = $this->getRequest();
		$form = new LoginForm();
		if($request->isPost()) {
			$filter = new LoginFilter();
			$inputFilter = $filter->prepareFilters();
            $inputFilter->setData($request->getPost());
            if ($inputFilter->isValid()) {
				
				$authService = $this->getAuthService();
				$authAdapter = $authService->getAdapter();
				
				$authAdapter->setIdentity($inputFilter->getValue('email'));
				$authAdapter->setCredential(md5($inputFilter->getValue('password')));
				
				$attempt = $authService->authenticate();
				
				if($attempt->isValid()) {
					
					$message = 'Successful Login ' . $attempt->getIdentity();
					$userRow = $authAdapter->getResultRowObject();
				    if($userRow->group_id > 0) {
					    $sql = "select * from groups where id = ".$userRow->group_id;
					    $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
					    $group = $dbAdapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
					    if(count($group) > 0) {
					        $groupArr = $group->toArray();
					        $group = $groupArr[0];
					        foreach($group as $key => $val) {
					            $userRow->{$key} = $val;
					        }
					    }
					}
					$authStorage = $authService->getStorage();
					$authStorage->write($userRow);
					// Write additional group information to user row
					
					$sessionContainer = $this->_getSessionContainer();
					$sessionContainer->offsetSet('role', Acl::getRoleFromStatusCode($userRow->role));
					//return $this->redirect()->toRoute('page-list');
					return $this->redirect()->toUrl('/elements/page');
				} else {
					/*
					
					*/
					if($this->_setInvalidLoginCounter() > $this->_maxInvalidAttempts) {
						$this->_setInvalidLoginCounter(TRUE); // reset counter
						return $this->redirect()->toRoute('login-invalid');
					} else {
						$message = 'Unsuccessful Login';
						$message .= '<pre>';
						$message .= implode('<br />', $attempt->getMessages());
						$message .= '</pre>';
					}
				}
			} else {
				// code if not valid
				if($this->_setInvalidLoginCounter() > $this->_maxInvalidAttempts) {
					return $this->redirect()->toRoute('login-invalid');
				}
			}
		}
		return new ViewModel(array('form' => $form, 'flashMessages' => $this->flashMessenger()->getMessages()));
		
	}
	
	public function forgotpasswordAction()
	{
	    $request = $this->getRequest();
	    $form = new LoginForm();
	    // If posted then check if email exists in db
	    // if it does, reset the password and send user
	    // and email with new password.
	    if($request->isPost()) {
	            $email = $this->params()->fromPost('email');
	            $sql = "select id, email from users where email = '".$email."'";
	            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
	            $result = $dbAdapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
	            if(count($result) > 0) {
	                // Reset the password
	                $resultArray = $result->toArray();
	                $randomPassword = $this->getRandomString();
	                $md5Password = md5($randomPassword);
	                $sql = "update users set password = '".$md5Password."' where id = ".$resultArray[0]['id'];
	                $dbAdapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
	                $message = new Message();
	                $link = 'http://'.$_SERVER['HTTP_HOST'] . '/elements/login';
                    $message->setBody("You are receiving this email because a password reset was recently requested via your Elements login.\n\nYour new password is: {$randomPassword}\n\nPlease go to:\n\n $link\n\n to log in\n\n");
                    $message->setFrom('noreply@elements-cms.com', 'Elements CMS');
                    $message->addTo($email);
                    $message->setSubject('Request to reset your Elements password');
                    $transport = new Sendmail();
                    $transport->send($message);
        
                    $this->flashMessenger()->addMessage("A password reset email has been sent. Please check your inbox.");

	                return $this->redirect()->toRoute('login-index');
	            } 
	    }
	    // Remove all fields except email and Submit
	    foreach($form->getElements() as $element)
	    {
	        if(!in_array($element->getName(), array('email', 'submit'))) {
	            $form->remove($element->getName());
	        }
	    }
	    return new ViewModel(array('form' => $form));
	}
	
	public function changepasswordAction()
	{
	    $request = $this->getRequest();
	    $form = new ChangePasswordForm();
	    $data = '';
	 
	    if($request->isPost()) {
	        
	        // Set InputFilters
	        $filter = new ChangePasswordFilter();
	        $filter->setPasswordToken($this->params()->fromPost('new_password1'));
			$inputFilter = $filter->prepareFilters();
			$form->setInputFilter($inputFilter);
			$form->setData($request->getPost());
			if ($form->isValid()) {
			    // Get current authenticated user
			    $authService = $this->getAuthService()->getIdentity();
			    if((int) $authService->id) {
			        $md5Password = md5($this->params()->fromPost('new_password1'));
			        $sql = "update users set password = '".$md5Password."' where id = ".$authService->id;
			        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
			        $dbAdapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
			        $message = new Message();
			        $link = 'http://'.$_SERVER['HTTP_HOST'] . '/elements/login';
			        $message->setBody("Your password for Elements has been reset.\n\nYour new password is: {$this->params()->fromPost('new_password1')}\n\nPlease go to:\n\n $link\n\n to log in\n\n");
			        $message->setFrom('noreply@elements-cms.com', 'Elements CMS');
			        $message->addTo($authService->email);
			        $message->setSubject('Your Elements password has been reset');
			        $transport = new Sendmail();
			        $transport->send($message);
			         
			        $msg="A password reset email has been sent. Please check your inbox.";
			         
			        return $this->redirect()->toRoute('logout');
			    }
			} 
			$data = $form->getData();
	    }
	    
	    return new ViewModel(array('form' => $form, 'data' => $data));
	}
	
	public function invalidAction()
	{
		return new ViewModel();
	}
	
	public function logoutAction()
	{
		$sessionContainer = $this->_getSessionContainer();
		$sessionContainer->offsetUnset('counter');
		$sessionContainer->offsetUnset('role');
		$authService = $this->getAuthService();
		$authService->clearIdentity();
		return $this->redirect()->toUrl($this->url()->fromRoute('login-index'));
	}
	
	public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('ElmContent\Model\UsersTable');
        }
        return $this->usersTable;
    }
	
	public function getAuthService()
	{
		$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$identityCol = 'email';
		$credentialCol = 'password';
		$authAdapter = new AuthAdapter($dbAdapter, 'users', $identityCol, $credentialCol);
		$authStorage = new AuthStorage();
		$authService = new AuthenticationService($authStorage, $authAdapter);
		return $authService;
	}
	
	/**
	 * functions
	 */
	function getRandomString($length = 6) {
	    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ+-*#&@!?";
	    $validCharNumber = strlen($validCharacters);
	
	    $result = "";
	
	    for ($i = 0; $i < $length; $i++) {
	        $index = mt_rand(0, $validCharNumber - 1);
	        $result .= $validCharacters[$index];
	    }
	
	    return $result;
	}
	
}