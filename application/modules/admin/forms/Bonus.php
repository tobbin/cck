<?php
/**
 * Bonus form
 * @author Nisanth Kumar
 * @since 22-11-2011
 * @package Form
 *
 */
class Admin_Form_Bonus extends Zend_Form
{
	protected $_chitId;
	protected $_bonusTypeId;
                
        public function getChitId()
	{
            return $this->_chitId;
	}
	
	public function setChitId($chitId)
	{
            $this->_chitId = $chitId;
	}
        public function getBonusTypeId()
	{
            return $this->_bonusTypeId;
	}
	
	public function setBonusTypeId($bonusTypeId)
	{
            $this->_bonusTypeId = $bonusTypeId;
	}
        
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formBonus');
            
            //  	 	amount
            $bonusType = new Zend_Form_Element_Hidden('bonus_type');
            $bonusType->setValue($this->getBonusTypeId());
            $this->addElement($bonusType);
            
            $chitId = new Zend_Form_Element_Hidden('chit_id');
            $chitId->setValue($this->getChitId());
            $this->addElement($chitId);
            
            $startInstallment = new Zend_Form_Element_Text('start_installment');
            $startInstallment->setRequired()->setLabel("Start installment")->setAttrib('class', 'inp-form')->setDecorators($decorators->elementDecorators);
            $this->addElement($startInstallment);
            
            $endInstallment = new Zend_Form_Element_Text('end_installment');
            $endInstallment->setRequired()->setLabel("End installment")->setAttrib('class', 'inp-form')->setDecorators($decorators->elementDecorators);
            $this->addElement($endInstallment);
            
            $amount = new Zend_Form_Element_Text('amount');
            $amount->setRequired()->setLabel("Amount")->setDecorators($decorators->elementDecorators)->setAttrib('class', 'inp-form');
            $this->addElement($amount);
            
            $create = new Zend_Form_Element_Submit('create');
            $create->setLabel('Save')->setDecorators($decorators->buttonDecorators);
            $this->addElement($create);                       
        }
        
}
?>