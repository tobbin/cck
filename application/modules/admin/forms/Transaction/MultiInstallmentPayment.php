<?php
/**
 * Client form to pay all installment of a customer one chits
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Transaction_MultiInstallmentPayment extends Zend_Form
{
	protected $_chitsInstallment;
	protected $_chitId;
	protected $_customerId;
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
        
    public function getChitId()
	{
        return $this->_chitId;
	}
	
	public function setChitId($chitId)
	{
        $this->_chitId = $chitId;
	}
        
    public function getCustomerId()
	{
        return $this->_customerId;
	}
	
	public function setCustomerId($customerId)
	{
        $this->_customerId = $customerId;
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
            $this->setName('formPayment');
            
            $customerId = new Zend_Form_Element_Hidden('customer_id');
            $customerId->setValue($this->getCustomerId())->setDecorators(array('ViewHelper')); ;
            $this->addElement($customerId);
            
            $chitId = new Zend_Form_Element_Hidden('chit_id');
            $chitId->setValue($this->getChitId())->setDecorators(array('ViewHelper')); ;
            $this->addElement($chitId);
                       
            $total = 0;
            $i = 0;
            foreach ($this->getChitsInstallment() as $installment) {
                $installmentForm = new Zend_Form_SubForm(array('elementsBelongTo' => 'installments'));
                //$installmentForm->setElementsBelongTo('installments');
                //check if this installment is payed
                if($installment['payment_status'] != 'C'){                 
                    ++$i;
                    $fine = 0;         
                    $total_amount_to_pay =  $installment['amount_to_pay'];
                    
                    //calling fine
                    $fine = '';
                    $discount_el_variable = '';
                    if($this->getFine() == "A"){
                        $discount_el_variable = '';
                        if($installment['status'] == 'P'){
                            $objFine     = new Nisanth_Model_Fine();
                            $chital_type = 'N';
                            if($installment['prized_installment'] != 0 && $installment['installment'] > $installment['prized_installment']) 
                                $chital_type = 'P'; 
                            $exceptionDays = $objFine->getFineExceptionDays($chital_type);  
                            if($installment['late_days'] > $exceptionDays){
                                $objPayment = new Nisanth_Model_Payment();    
                                $fine = $objPayment->calculateFine($chital_type, $installment['late_days'], $installment['amount_to_pay']);                        
                                $total_amount_to_pay =  $fine + $installment['amount_to_pay'];

                                $discountElement = new Zend_Form_Element_Text("discount_".$i);
                                $discountElement->setAttrib('class', 'inp-discount')->setAttrib('style', 'width:100px; height:25px');
                                $discountElement->setAttrib('id','di_'.$i)->setDecorators(array('ViewHelper'));                                         
                                $installmentForm->addElement($discountElement);
                                $discount_el_variable = 'discount_'.$i;
                            }                        
                        }
                    }
                    //end fine 
                    
                    $total             += $total_amount_to_pay;              
                    $installmentElement = new Zend_Form_Element_Hidden('installment_'.$i);                    
                    $installmentElement->setValue($installment['installment_id'])->setDecorators(array('ViewHelper'));                
                    $installmentForm->addElement($installmentElement);
                  
                    $amountElement = new Zend_Form_Element_Text("amount_".$i);
                    $amountElement->setAttrib('class', 'inp-amount');
                    $amountElement->setAttrib('style', 'width:100px; height:25px');
                    $amountElement->setAttrib('id','am_'.$i); 
                    $amountElement->setValue($total_amount_to_pay)->setDecorators(array('ViewHelper'));
                    //$amountElement->setLabel("<div class='div1'>{$installment['installment']}</div><div class='div2'>{$installment['amount_to_pay']}</div><div class='div1'>{$fine}</div>");;                    
                    $installmentForm->addElement($amountElement);

                    $hdAmountElement = new Zend_Form_Element_Hidden("hd_amount_".$i);
                    $hdAmountElement->setValue($installment['amount_to_pay']);            
                    $hdAmountElement->setAttrib('id','hd_amount_'.$i)->setDecorators(array('ViewHelper'));
                    $installmentForm->addElement($hdAmountElement);
                    
                    $fineElement = new Zend_Form_Element_Hidden("fine_".$i);
                    $fineElement->setValue($fine);            
                    $fineElement->setAttrib('id','fine_'.$i)->setDecorators(array('ViewHelper'));
                    $installmentForm->addElement($fineElement);
                    $installmentForm->setDecorators(array(
        'FormElements',
        array('HtmlTag', array('tag' => 'span')),
        ));
                    $installmentForm->addDisplayGroup(array('amount_'.$i, $discount_el_variable),'collection', array('legend' => "<div class='div1'>{$installment['installment']}</div><div class='div2' style='width:120px'>{$installment['amount_to_pay']}</div><div class='div1'>{$fine}</div>", 'escape' => false));
                    $this->addSubForm($installmentForm, $i);
                   
                }
            }           
                                              
            $print = new Zend_Form_Element_Checkbox('print');
            $print->setRequired()->setLabel("If you need recept")->setDecorators($decorators->elementDecorators);
            $this->addElement($print);  
            
            $totalAmt = new Zend_Form_Element_Text('total_amount');        
            $totalAmt->setValue($total)->setLabel("Total Amount To Pay: ")->setAttrib('class', 'total_sum inp-form')->setDecorators($decorators->elementDecorators);
            $totalAmt->setAttrib('id','total_amount')->setAttrib('readonly','true'); 
            $this->addElement($totalAmt);
            
            $create = new Zend_Form_Element_Submit('create');
            $create->setLabel('Pay')->setDecorators($decorators->buttonDecorators);
            $this->addElement($create);   
            
            
        }
        
}
?>
