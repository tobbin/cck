<?php

class Admin_UsersController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance();
        if(!$this->auth->hasIdentity()){
            $this->_redirect('admin/');
        }
    }

    public function indexAction()
    {
              
    }
    
    public function gridAction()
    {                         
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $objUserDetails = new Nisanth_Model_UserDetails();
      
        $users = $objUserDetails->getUserDetails();       
        $i = 0;
        foreach($users as $user){
             ++$i;           
            $options = '<a href="users/edit/id/' . $user['user_id'] . '">Edit</a>';
            //$options .= '  <a href="users/delete/id/' . $user['user_id'] . '">Delete</a>';                 
             
            $phone = ($user['mobile'] !=  '')? $user['mobile'] : $user['landphone'];
            $data1['rows'][] = array(
                       'id' => $user['user_id'],
                       'cell' => array($i,
                        $user['first_name'].' '.$user['last_name'],
                        $user['house_name'],
                        $phone,
                        $options)
                       );
           }
       $users = json_encode($data1);
        echo $users;
        //exit;
    }
    
    public function addAction()
    {
        $pincode = new Nisanth_Model_Pincode();
        $form = new Admin_Form_User(array('request' => $this->_request, 'pincode' => $pincode));

        $this->view->userForm = $form;

        if (!$this->_request->isPost()) {
        	return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
        	$users = new Nisanth_Model_User();
            echo $userId = $users->saveAll($this->_getAllParams());
            $this->_redirect('/admin/users');
        }
    }
    
    public function editAction()
    {
        $pincode       = new Nisanth_Model_Pincode();
        $form          = new Admin_Form_User(array('request' => $this->_request, 'pincode' => $pincode));
        $users         = new Nisanth_Model_User();
        $userDetail    = new Nisanth_Model_UserDetails();
        $address       = new Nisanth_Model_Address();
        $populateArray = array();

        $form->populate($users->fetchRow("id = {$this->_getParam('id')}")->toArray());
        $form->populate($userDetail->fetchRow("user_id = {$this->_getParam('id')}")->toArray());
        $form->populate($address->fetchRow("user_id = {$this->_getParam('id')}")->toArray());
        
        $this->view->userForm = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $objUserDetails = new Nisanth_Model_UserDetails();
            $objUserDetails->updateAll($this->_getAllParams());           
            $this->_redirect('/admin/users');
        }
    }
    
    public function deleteAction()
    {
        $users = new Nisanth_Model_User();
        $id = $this->_getParam('id');
        $users->deletUser($id);
        $this->_redirect('/admin/users');
    }
    
    function checkuserAction()
    {
        $objUser = new Nisanth_Model_User();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $firstName = $this->_request->getParam('first_name');
        $houseName = $this->_request->getParam('house_name');
        $user = $objUser->getUserByDetails($firstName, $houseName);
        //echo'<pre>';print_r($user);echo'</pre>';
        //echo $user->first_name .' '. $user->last_name;        
    }

}

