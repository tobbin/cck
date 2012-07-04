<?php
class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->auth = Zend_Auth::getInstance();
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $users          = new Nisanth_Model_Admin();
        $objRole        = new Nisanth_Model_Roles();
        $objUserDetails = new Nisanth_Model_UserDetails();
        
        $form = new Admin_Form_Login(array('request' => $this->_request));
        $this->view->userForm = $form;

	if($this->auth->hasIdentity()){
            $this->_redirect('admin/users');
        } else if ($this->_request->isPost()) {	
            if (!$form->isValid($this->_getAllParams())) {
               return $this->render();
            } else {
                $filter = new Zend_Filter_StripTags();
                // collect the data from the user
                $username 	= $filter->filter($this->_request->getPost('userid'));
                $password 	= $filter->filter($this->_request->getPost('password'));

                $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'admin');
                $authAdapter->setIdentityColumn('user_name')
                                ->setCredentialColumn('password');
                $authAdapter->setIdentity($username)
                                ->setCredential(md5($password));
                $result = $this->auth->authenticate($authAdapter);
                if($result->isValid()){
                    $storage = new Zend_Auth_Storage_Session();
                    $userInfo = $authAdapter->getResultRowObject(null, 'password');  
                    $userInfo->role = $objRole->getName($userInfo->role);
                    $userInfo->user_name = $objUserDetails->getName($userInfo->user_id);
                    $storage->write($userInfo);
                    switch ($userInfo->role){
                        case 'super admin':
                            $this->_redirect('admin/configuration');
                        break;
                        case 'admin':
                            $this->_redirect('admin/chits');
                        break;
                        case 'manager':
                            $this->_redirect('admin/report/datewise');
                        break;
                        case 'staff':
                            $this->_redirect('admin/chitscollection');
                        break;
                        default :
                           $this->_redirect('admin/index');
                        break;
                    }
                    
                } else {
                    $this->view->message = "Invalid username or password. Please try again.";
                }
            }
			
        }
    }
    
    public function testAction()
    {
        $userModel = new Nisanth_Model_User();
        $users = $userModel->getAll();
        //print_r($users);
        $form = new Admin_Form_Dynamic(array('users' => $users));
        $this->view->userForm = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        } else {
            print_r($this->_getAllParams());
        }
    }
    
    function logoutAction() 
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/admin');

    }// FUNCTION

}

