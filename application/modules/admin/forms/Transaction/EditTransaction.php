<?php
/**
 * Client form to pay all installment of a customer one chits
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Transaction_EditTransaction extends Zend_Form
{
	protected $_transactions;
	protected $_request;
        
	public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
	public function getTransactions()
	{
            return $this->_transactions;
	}
	
	public function setTransactions($transactions)
	{
            $this->_transactions = $transactions;
	}
        
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formEditTransaction');
                
            $transactions = $this->getTransactions();
            $installmentElement = new Zend_Form_Element_Hidden('installmentTransId');                    
            $installmentElement->setValue($transactions[0]['installment_trans_id'])->setDecorators(array('ViewHelper')); ;                   
            $this->addElement($installmentElement);
            
            $total = 0;
            $i = 0;
            $discount_el_variable = null;
            $fine = null;
            foreach ($this->getTransactions() as $transaction) {
                $transactionForm = new Zend_Form_SubForm(array('elementsBelongTo' => 'transactions'));                
                ++$i;                                                
                $total             += $transaction['amount'];                             
                $paymentElement = new Zend_Form_Element_Hidden('paymentTransId_'.$i);                    
                $paymentElement->setValue($transaction['payment_trans_id'])->setDecorators(array('ViewHelper')); ;                   
                $transactionForm->addElement($paymentElement);

                $amountElement = new Zend_Form_Element_Text("amount_.$i");
                $amountElement->setAttrib('class', 'inp-amount inp-form');
                $amountElement->setAttrib('id','am_'.$i)->setDecorators(array('ViewHelper')); 
                $amountElement->setValue($transaction['amount']);                    
                $transactionForm->addElement($amountElement);

                $hdAmountElement = new Zend_Form_Element_Hidden("hd_amount_".$i);
                $hdAmountElement->setValue($transaction['amount']);            
                $hdAmountElement->setAttrib('id','hd_am_'.$i);
                $transactionForm->addElement($hdAmountElement);

                $hdDiscountElement = new Zend_Form_Element_Hidden("hd_discount_".$i);
                $hdDiscountElement->setValue($transaction['discount']);            
                $hdDiscountElement->setAttrib('id','hd_di_'.$i);
                $transactionForm->addElement($hdDiscountElement);
                
                if($transaction['fine']) {
                    $discountElement = new Zend_Form_Element_Text("discount_".$i);
                    $discountElement->setValue($transaction['discount']); 
                    $discountElement->setAttrib('class', 'inp-discount inp-form');
                    $discountElement->setAttrib('id','di_'.$i)->setDecorators(array('ViewHelper'));                                         
                    $transactionForm->addElement($discountElement);
                    $discount_el_variable = 'discount_'.$i;
                    $fine = $transaction['fine'];
                }
                
                $transactionForm->addDisplayGroup(
                        array('amount_'.$i, $discount_el_variable),
                            'collection', 
                        array(
                            'legend' => "<div class='div1'>{$transaction['chit_code']}</div><div class='div2'>{$transaction['token']}</div><div class='div1'>{$transaction['installment']}</div><div class='div2'>{$transaction['chital_name']}</div><div class='div1'>{$transaction['amount']}</div><div class='div2'>{$fine}</div>", 'escape' => false
                            )
                        );
                $this->addSubForm($transactionForm, $i);             
            }           
            
            $totalAmt = new Zend_Form_Element_Text('total_amount');        
            $totalAmt->setValue($total)->setLabel("Total Amount To Pay: ")->setAttrib('class', 'total_sum inp-form')->setDecorators($decorators->elementDecorators);
            $totalAmt->setAttrib('id','total_amount')->setAttrib('readonly','true'); 
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
