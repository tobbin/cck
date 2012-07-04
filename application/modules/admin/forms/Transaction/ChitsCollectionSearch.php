<?php
/**
 * bill search form
 * @author Tobin
 * @since 20-12-2011
 * @package Form 
 */
class Admin_Form_Transaction_ChitsCollectionSearch extends Zend_Form
{
    protected $_request;
    protected $_chits;

    public function getRequest()
    {
        return $this->_request;
    }

    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;
    }

    public function getChits()
    {
        return $this->_chits;
    }

    public function setChits($chits)
    {
        $this->_chits = $chits;
    }

    function init()
    {           
        $decorators = new Nisanth_View_Helper_Decorator();
        $this->setDecorators($decorators->formDecorator);
        $this->setName('collectionSearch');
        $this->addAttribs(array("id"=>"collectionSearch")); 

        $fromDate = new Zend_Form_Element_Text('fromDate');
        $fromDate->setLabel('From Date')->setAttrib('class', 'inp-form datepicker dateEdit');            
        $fromDate->addValidator(new Zend_Validate_Date());
        $fromDate->setDecorators($decorators->elementDecorators);

        $toDate = new Zend_Form_Element_Text('toDate');
        $toDate->setLabel('To Date')->setAttrib('class', 'inp-form datepicker dateEdit');            
        $toDate->addValidator(new Zend_Validate_Date());
        $toDate->setDecorators($decorators->elementDecorators);

        $chitsElement = new Zend_Form_Element_Select('chits');
        $chitsElement->addMultiOption('', '--Select one--');
        foreach ($this->getChits() as $data) {
            $chitsElement->addMultiOption($data->id, $data->chit_code);
        }
        $chitsElement->setLabel('Chits')->setAttrib('class', 'styledselect')
                ->setAttrib('onchange', 'getToken()');                      
        $chitsElement->setRequired();
        $chitsElement->setDecorators($decorators->elementDecorators);

        $chitalElement = new Zend_Form_Element_Select('customer_id');            
        $chitalElement->addMultiOption(1, 16);            
        $chitalElement->setLabel('Chits customer')
                ->setAttrib('class', 'styledselect')
                ->setAttrib('onchange', 'getUser()');           
        $chitalElement->setRequired();
        $chitalElement->setDecorators($decorators->elementDecorators);

        $agentTypeElement = new Zend_Form_Element_Select('agent_type');  
        $agentTypeElement->addMultiOption('', 'Select One');
        $agentTypeElement->addMultiOption(6, 'collection agent');
        $agentTypeElement->addMultiOption(7, 'Agent');
        $agentTypeElement->addMultiOption(6, 'collection agent');
        $agentTypeElement->setLabel('Agent Type')
                ->setAttrib('class', 'styledselect')
                ->setAttrib('onchange', 'getUser()');           
        $agentTypeElement->setRequired();
        $agentTypeElement->setDecorators($decorators->elementDecorators);

        $agentElement = new Zend_Form_Element_Select('agent_id');                                  
        $agentElement->setLabel('Agent')
                ->setAttrib('class', 'styledselect')
                ->setAttrib('onchange', 'getUser()');           
        $agentElement->setRequired();
        $agentElement->setDecorators($decorators->elementDecorators);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');
        $submit->setDecorators($decorators->buttonDecorators);

        $this->addElements(array($fromDate, $toDate, $chitsElement, $chitalElement, $agentTypeElement, $agentElement, $submit));
    }
}
