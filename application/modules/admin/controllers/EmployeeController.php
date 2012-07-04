<?php

class Admin_EmployeeController extends Zend_Controller_Action
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
       $objDesignation = new Nisanth_Model_Designation();
       $this->view->form = new Admin_Form_Employee_Search(array('designation'=> $objDesignation->getAll()));
    }
    
    
    public function addAction()
    {
        $objDesignation = new Nisanth_Model_Designation();
        $objUsers       = new Nisanth_Model_User();
        
        $form   = new Admin_Form_Employee_Add(array('request' => $this->_request,
                                                    'designation' => $objDesignation->getAll()));

        $this->view->userForm = $form;

        if (!$this->_request->isPost()) {
        	return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            $objEmployee = new Nisanth_Model_Employee();
            $employeeId  = $objEmployee->saveAll($this->_getAllParams());
            $this->_redirect('/admin/employee');
        }
    }
    
    public function editAction()
    {
         $objDesignation = new Nisanth_Model_Designation();
        $objUsers       = new Nisanth_Model_User();
        $form          = new Admin_Form_Employee_Add(array('request' => $this->_request,
                                                    'designation' => $objDesignation->getAll()));
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
    
    public function gridAction()
    {                         
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $request    =   $this->getRequest()->getParams();
         
        $designation = null;
        if($request['designation'] != ''){
            $designation = $request['designation'];           
        }
        
        $status = null;
        if($request['status'] != ''){
            $status = $request['status'];           
        }
        $branch =null;
        
        $objEmployee = new Nisanth_Model_Employee();
                            
        $employees = $objEmployee->getEmployees($branch, $designation, $status, $request['page'], $request['rp']);
        $employees_details = $objEmployee->getEmployeesCount($designation, $status);
       
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        $data1['total'] = $employees_details->total_rows;
        foreach($employees as $employee){
             ++$i;           
            $options = '<a href="employee/edit/id/' . $employee['employee_id'] . '">Edit</a>';
                       
            $data1['rows'][] = array(
                       'id' => $employee['employee_id'],
                       'cell' => array($i,
                        $employee['first_name'].' '.$employee['last_name'],
                        $employee['phone'],
                        $employee['designation'],   
                        $options)
                       );
           }
       $users = json_encode($data1);
        echo $users;
        //exit;
    }

}

