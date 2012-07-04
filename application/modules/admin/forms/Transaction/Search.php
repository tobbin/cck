<?php
/**
 * bill search form
 * @author Tobin
 * @since 20-12-2011
 * @package Form
 *
 */
class Admin_Form_Transaction_Search extends Zend_Form
{
	protected $_request;
        
        protected $_category;       
        
	public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
        public function getCategory()
	{
            return $this->_category;
	}
	
	public function setCategory($category)
	{
            $this->_category = $category;
	}
        
	function init()
	{           
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('transSearch');
            $this->addAttribs(array("id"=>"transSearch")); 

            $fromDate = new Zend_Form_Element_Text('fromDate');
            $fromDate->setLabel('From Date')->setAttrib('class', 'inp-form datepicker1');            
            $fromDate->addValidator(new Zend_Validate_Date());
            $fromDate->setDecorators($decorators->elementDecorators);

            $toDate = new Zend_Form_Element_Text('toDate');
            $toDate->setLabel('To Date')->setAttrib('class', 'inp-form datepicker1');            
            $toDate->addValidator(new Zend_Validate_Date());
            $toDate->setDecorators($decorators->elementDecorators);                                   
            
            $category = new Zend_Form_Element_Select('category');
            $category->addMultiOption('', '--Select one--');
            foreach ($this->getCategory() as $data) {
                $category->addMultiOption($data->id, $data->name);
            }
            $category->setLabel('Payment For')
                    ->setAttrib('class', 'styledselect');           
            $category->setRequired();
            $category->setDecorators($decorators->elementDecorators);
                        
            $transactionType = new Zend_Form_Element_Select('transactionType');
            $transactionType->addMultiOption('', '--Select one--');
            $transactionType->addMultiOption('C','credit'); 
            $transactionType->addMultiOption('D','debit');
            $transactionType->setLabel('Payment Type')->setAttrib('class', 'styledselect');
            $transactionType->setDecorators($decorators->elementDecorators);
            
            $status = new Zend_Form_Element_Select('status');
            $status->addMultiOption('', '--Select one--');
            $status->addMultiOption('1','Complete'); 
            $status->addMultiOption('2','Pending');
            $status->setLabel('Transaction Status')->setAttrib('class', 'styledselect');
            $status->setDecorators($decorators->elementDecorators);
                  
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($fromDate, $toDate, $category, $transactionType, $status, $submit));
	}
}
