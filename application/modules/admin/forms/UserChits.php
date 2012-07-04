<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_UserChits extends Zend_Form
{
	protected $_request;
	protected $_chit;
        
	public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
	public function getChit()
	{
            return $this->_chit;
	}
	
	public function setChit($chit)
	{
            $this->_chit = $chit;
	}
        
        public function getCollectionTypes() 
        {
            return array('M' => 'Monthly', 'D' => 'Daily', 'W' => 'Weekly');
        }


        function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $temp       = new Nisanth_Model_User();
            $emp        = new Nisanth_Model_Employee();
            $com        = new Nisanth_Model_ChitCommission();
            
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formChit');
            
            $chitId = new Zend_Form_Element_Hidden('chit_id');
            
            $chitCode = new Zend_Form_Element_Text('chit_code');
            $chitCode->setLabel('Chit Code');
            $chitCode->setRequired()->setAttrib('class', 'inp-form')->setValue($this->getChit()->chit_code);
            $chitCode->setDecorators($decorators->elementDecorators);

            $token = new Zend_Form_Element_Text('token');
            $token->setLabel('Token')->setAttrib('class', 'inp-form');
            $token->setRequired()
                    ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                    'chits_customer', 
                                    'token', 
                                    $this->_initExclude()));;
            $token->setDecorators($decorators->elementDecorators);

            $collectionTypeElement = new Zend_Form_Element_Select('collection_type');
            foreach ($this->getCollectionTypes() as $key => $collectionType) {
                $collectionTypeElement->addMultiOption($key, $collectionType);
            }
            $collectionTypeElement->setLabel('Collection type')->setAttrib('class', 'styledselect');
            $collectionTypeElement->setDecorators($decorators->elementDecorators);

            $customerId = new Zend_Form_Element_Select('customer_id');
            foreach ($temp->getAllUsers() as $user) {
                $customerId->addMultiOption($user->id, $user->first_name .'-'. $user->last_name);
            }
            $customerId->setLabel('Customer')->setAttrib('class', 'styledselect');
            $customerId->setRequired();
            $customerId->setDecorators($decorators->elementDecorators);
            
            $agentElement = new Zend_Form_Element_Select('agent_id');
            $agentElement->addMultiOption('0', '--Select one--');
            foreach ($emp->getAll(7) as  $agent) {
                $agentElement->addMultiOption($agent->employee_id, $agent->first_name .' '. $agent->last_name);
            }
            $agentElement->setLabel('Agent')->setAttrib('class', 'styledselect');
            $agentElement->setRequired();
            $agentElement->setDecorators($decorators->elementDecorators);
            
            $collectionAgent = new Zend_Form_Element_Select('collection_agent');
            $collectionAgent->addMultiOption('0', '--Select one--');
            foreach ($emp->getAll(6) as  $agent) {
                $collectionAgent->addMultiOption($agent->employee_id, $agent->first_name .' '. $agent->last_name);
            }
            $collectionAgent->setLabel('Collection Agent')->setAttrib('class', 'styledselect');
            $collectionAgent->setRequired();
            $collectionAgent->setDecorators($decorators->elementDecorators);
            
            $commissionElement = new Zend_Form_Element_Select('agent_commission');
            //$commissionElement->addMultiOption('0', '--Select one--');
            foreach ($com->getAll($this->_chit['id']) as  $commission) {
                $commissionElement->addMultiOption($commission->id, $commission->amount);
            }
            $commissionElement->setLabel('Agent Commission')->setAttrib('class', 'styledselect');
            $commissionElement->setRequired();
            $commissionElement->setDecorators($decorators->elementDecorators);
            
            $paid_commission = new Zend_Form_Element_Text('paid_commission');
            $paid_commission->setLabel('Paid Commission')->setValue(0)
                    ->setAttrib('class', 'inp-form')
                    ->setRequired()->addValidator(new Zend_Validate_Int());
            $paid_commission->setDecorators($decorators->elementDecorators);
            
            $join_date = new Zend_Form_Element_Text('join_date');
            $join_date->setLabel('Join date')->setAttrib('class', 'inp-form datepicker dateEdit');
            $join_date->setRequired();
            $join_date->setValue($this->getChit()->start_date);
            $join_date->addValidator(new Zend_Validate_Date());
            $join_date->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($chitId, $chitCode, $token,$collectionTypeElement, $customerId, $agentElement, $collectionAgent, $commissionElement, $paid_commission, $join_date, $submit));

	}
    
    public function _initExclude()
    {
        $exclude = "chit_id = {$this->getChit()->id}";
        // Change validation in edit case
        if ($this->getRequest()->getParam('id') !== null) {
            $exclude = " AND id != {$this->getRequest()->getParam('id')}";
        }
        return $exclude;
    }
}