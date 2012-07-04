<?php
/**
 * Description of Pincode
 *
 * @author Nisanth
 */
class Admin_Form_Bonustype  extends Zend_Form
{
	protected $_request;
        
	public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
	
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formBonustype');
            
            $name = new Zend_Form_Element_Text('name');
            $name->setLabel('Name')->setAttrib('class', 'inp-form');
            $name->setRequired();
            $name->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($name, $submit));
        }
    }
    

?>
