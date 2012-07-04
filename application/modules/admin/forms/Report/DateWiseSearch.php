<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Report_DateWiseSearch extends Zend_Form
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
            $class = null;
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formDateWise');
            
            $transactionTypeElement = new Zend_Form_Element_Select('transaction_type');
            $transactionTypeElement->setLabel('Transaction Type');
            $transactionTypeElement->addMultiOption('','All');
            $transactionTypeElement->addMultiOption('C','Credit');
            $transactionTypeElement->addMultiOption('D','Debit');
            $transactionTypeElement->setAttrib('class', 'styledselect');
            $transactionTypeElement->setDecorators($decorators->elementDecorators);
            
            $categoryElement = new Zend_Form_Element_Select('category');
            $categoryElement->addMultiOption('', '--Select one--');
            foreach ($this->getCategory() as $data) {
                $categoryElement->addMultiOption($data->id, $data->name);
            }
            $categoryElement->setLabel('Transaction Category')
                    ->setAttrib('class', 'styledselect');           
            $categoryElement->setRequired();
            $categoryElement->setDecorators($decorators->elementDecorators);
        
            $startDateElement = new Zend_Form_Element_Text('start_date');
            $startDateElement->setLabel('From Date')->setAttrib("class", "inp-form datepicker $class");
            $startDateElement->setRequired();
            $startDateElement->addValidator(new Zend_Validate_Date());
            $startDateElement->setDecorators($decorators->elementDecorators);

            $endDateElement = new Zend_Form_Element_Text('end_date');
            $endDateElement->setLabel('To Date')->setAttrib("class", "inp-form datepicker $class");
            $endDateElement->setRequired();
            $endDateElement->addValidator(new Zend_Validate_Date());
            $endDateElement->setDecorators($decorators->elementDecorators);

            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array( $transactionTypeElement, $categoryElement, $startDateElement, $endDateElement, $submit));
	}
}