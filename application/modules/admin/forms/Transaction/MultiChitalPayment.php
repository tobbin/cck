<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Transaction_MultiChitalPayment extends Zend_Form
{
	protected $_chitsInstallment;
	protected $_collectionAgent;
	protected $_fine;
    protected $_baseUrl;
        
	public function getChitsInstallment()
	{
            return $this->_chitsInstallment;
	}
	
	public function setChitsInstallment($chitsInstallment)
	{
            $this->_chitsInstallment = $chitsInstallment;
	}
        
        
        public function getCollectionAgent()
	{
            return $this->_collectionAgent;
	}
	
	public function setCollectionAgent($collectionAgent)
	{
            $this->_collectionAgent = $collectionAgent;
	}        
        
        public function getFine()
	{
            return $this->_fine;
	}
	
	public function setFine($fine)
	{
            $this->_fine = $fine;
	}
    
    public function getBaseUrl()
	{
        return $this->_baseUrl;
	}
	
	public function setBaseUrl($baseUrl)
	{
        $this->_baseUrl = $baseUrl;
	}
	
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formDailyCollection');
            
            $customerId = new Zend_Form_Element_Hidden('collection_agent');
            $customerId->setValue($this->getCollectionAgent)->setDecorators(array('ViewHelper'));
            $this->addElement($customerId);
                      
            $i = 0;
            foreach ($this->getChitsInstallment() as $installment) {
                $installmentForm = new Zend_Form_SubForm(array('elementsBelongTo' => 'installments'));
                ++$i;
                $chitalId = new Zend_Form_Element_Hidden('chital_'.$i);
                $chitalId->setValue($installment['chital_id'])
                        ->setDecorators(array('ViewHelper'));
                $installmentForm->addElement($chitalId);
                
                //calculating fine
                $fine = '';
                if($this->getFine() == "A"){
                    $objPayment = new Nisanth_Model_Payment;
                    $fine = $objPayment->calculateTotalFine($installment['chital_id']);                
                }
                //end of fine   
                
                $amountElement = new Zend_Form_Element_Text('amount_'.$i);
                $amountElement->setAttrib('class', 'inp-amount');
                $amountElement->setAttrib('style', 'width:100px; height:25px');
                $amountElement->setAttrib('id',$i)->setDecorators($decorators->elementDecorators)
                                                ->setLabel("<div class='div1' style='width:250px'><a href='{$this->getBaseUrl()}/admin/chitscollection/installmentwise/chital/{$installment['chital_id']}'>{$installment['first_name']} {$installment['last_name']}</a></div><div class='div2'>{$installment['chit_code']}</div><div class='div1' style='width:50px'>{$installment['token']}</div><div class='div2'>{$installment['amount_to_pay']}</div><div class='div1'>{$fine}</div>");
                                                                                             
                $installmentForm->addElement($amountElement);
                $installmentForm->setDecorators(array(
        'FormElements',
        array('HtmlTag', array('tag' => 'span')),
        ));

                //$installmentForm->addDisplayGroup(array('amount_'.$i),'collection', array('legend' => $installment['first_name']. " " . $installment['last_name']."  " . $installment['chit_code'] . "-->" . $installment['token']. "  " . $installment['amount_to_pay']));
                /*$installmentForm->addDisplayGroup(array('amount_'.$i),
                      'settings',
                      array(
                        'legend' => "<div>".$installment['first_name'] . $installment['last_name']."</div> " . $installment['chit_code'] . "-->" . $installment['token']. "  " . $installment['amount_to_pay'],
                          'disableLoadDefaultDecorators' => true,
                        'decorators' => array(
                          'FormElements',
                          'Fieldset',
                          // need to alias the HtmlTag decorator so you can use it twice
                          array(array('Dashed'=>'HtmlTag'), array('tag'=>'div', 'class'=>'dashed-outline')),
                          array('HtmlTag',array('tag' => 'div',  'class' => 'settings')),  
                        )
                      )
                    );
                    */
                $this->addSubForm($installmentForm, $i);            
            }           
            
           
            $totalAmt = new Zend_Form_Element_Text('total_amount');
         
            $totalAmt->setLabel("Total Amount To Pay: ")->setAttrib('class', 'total_sum inp-form')->setDecorators($decorators->elementDecorators);
            $totalAmt->setAttrib('id','total_amount'); 
            $this->addElement($totalAmt);
            
                        
//            $print = new Zend_Form_Element_Checkbox('print');
//            $print->setRequired()->setLabel("If you need recept")->setDecorators($decorators->elementDecorators);
//            $this->addElement($print);
            
            $create = new Zend_Form_Element_Submit('create');
            $create->setLabel('Pay')->setDecorators($decorators->buttonDecorators);
            $this->addElement($create);   
            
            
        }
        
}
?>
