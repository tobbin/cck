<?php
/**
 * Bonus form
 * @author Nisanth Kumar
 * @since 22-11-2011
 * @package Form
 *
 */
class Admin_Form_BonusEdit extends Zend_Form
{

        protected $_bonus;
                
        public function getBonus()
	{
            return $this->_bonus;
	}
	
	public function setBonus($bonus)
	{
            $this->_bonus = $bonus;
	}
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formBonus');
            
            $bonus = new Zend_Form_Element_Hidden('id');
            $bonus->setValue($this->getBonus());
            $this->addElement($bonus);

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