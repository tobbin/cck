<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Report_ChitWiseSearch extends Zend_Form
{
	protected $_currentInstallment;
        protected $_fromInstallment;
        protected $_toInstallment;
        protected $_chitId;


        public function getCurrentInstallment()
	{
            return $this->_currentInstallment;
	}
	
	public function setCurrentInstallment($currentInstallment)
	{
            $this->_currentInstallment = $currentInstallment;
	}
        public function getFromInstallment()
	{
            return $this->_fromInstallment;
	}
	
	public function setFromInstallment($fromInstallment)
	{
            $this->_fromInstallment = $fromInstallment;
	}
        
        public function getToInstallment()
	{
            return $this->_toInstallment;
	}
	
	public function setToInstallment($toInstallment)
	{
            $this->_toInstallment = $toInstallment;
	}
        
        public function getChitId()
	{
            return $this->_chitId;
	}
	
	public function setChitId($chitId)
	{
            $this->_chitId = $chitId;
	}
        
       
	
	function init()
	{
            $class = null;
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('frmChitWise');
            $this->setAttrib('id', 'frmChitWise');
                       
            $chitElement = new Zend_Form_Element_Hidden('chit');
            $chitElement->setValue($this->getChitId());
            
            $currentInstallmentElement = new Zend_Form_Element_Hidden('current_installmment');
            $currentInstallmentElement->setValue($this->getCurrentInstallment());
            //for for-loop
            $currentInstallment = $this->getCurrentInstallment();
           
            $fromInstallmentElement = new Zend_Form_Element_Select('from_installment');
            $fromInstallmentElement->setLabel('From Installment');
            for($i=1; $i <= $currentInstallment; $i++){
                $fromInstallmentElement->addMultiOption($i,$i);
            }
            $fromInstallmentElement->setValue($this->getFromInstallment());
            $fromInstallmentElement->setAttrib('class', 'styledselect');
            $fromInstallmentElement->setDecorators($decorators->elementDecorators);
            
            $toInstallmentElement = new Zend_Form_Element_Select('to_installment');
            $toInstallmentElement->setLabel('To Installment');
            for($i=1; $i <= $currentInstallment; $i++){
                $toInstallmentElement->addMultiOption($i,$i);
            }
            $toInstallmentElement->setValue($this->getToInstallment());
            $toInstallmentElement->setAttrib('class', 'styledselect');
            $toInstallmentElement->setDecorators($decorators->elementDecorators);
        
            $printButtonElement = new Zend_Form_Element_Submit('button');
            $printButtonElement->setLabel('Print');
            $printButtonElement->setAttrib('onclick', 'printPage()');
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($chitElement, $currentInstallmentElement, $fromInstallmentElement, $toInstallmentElement, $submit,$printButtonElement));
	}
}