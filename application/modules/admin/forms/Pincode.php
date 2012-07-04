<?php
/**
 * Description of Pincode
 *
 * @author Nisanth
 */
class Admin_Form_Pincode  extends Zend_Form
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
            $this->setName('formPincode');
            
            $objCity = new Nisanth_Model_City();
            
            $cityElement = new Zend_Form_Element_Select('city_id');
            $cityElement->setLabel('City');
            $cityElement->addMultiOption('', '--Select one--');
            foreach ($objCity->fetchAll() as $type) {
                $cityElement->addMultiOption($type['id'], $type['name']);
            }
            $cityElement->setAttrib('class', 'styledselect');
            $cityElement->setDecorators($decorators->elementDecorators);
            
            $street = new Zend_Form_Element_Text('street');
            $street->setLabel('Street')->setAttrib('class', 'inp-form');
            $street->setRequired();
            $street->setDecorators($decorators->elementDecorators);
            
            $pin = new Zend_Form_Element_Text('pin');
            $pin->setLabel('Pincode')->setAttrib('class', 'inp-form');
            $pin->setRequired();
            $pin->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($cityElement, $street, $pin, $submit));
        }
    }
    

?>
