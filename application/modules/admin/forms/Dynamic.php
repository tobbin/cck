<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 11-01-2010
 * @package Form
 *
 */
class Admin_Form_Dynamic extends Zend_Form
{
    protected $_users;
    public function getUsers()
	{
            return $this->_users;
	}
	
	public function setUsers($users)
	{
            $this->_users = $users;
	}
   function init()
   {
       $form = $this;
       $subForm = new Zend_Form_SubForm();

        foreach($this->getUsers() as $rownum => $row){
          $id = $row['id'];
          $rowForm = new Zend_Form_SubForm();
          foreach($row as $key => $value){
            if($key == 'id') continue;
            $rowForm->addElement(
              'text',
              $key,
              array(
                'value' => $value,
              )
            );
          }
          $rowForm->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array('HtmlTag', array('tag' => 'td')),
          ));
          $subForm->addSubForm($rowForm, $id);
        }

        $subForm->setSubFormDecorators(array(
          'FormElements',
          array('HtmlTag', array('tag'=>'tr')),
        ));

        $form->addSubForm($subForm, 'contacts');

        $form->setSubFormDecorators(array(
          'FormElements',
          array('HtmlTag', array('tag' => 'tbody')),
        ));

        $form->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        $form->addElement(
          'submit', 'submit', array('label' => 'Submit'));

        $form->submit->setDecorators(array(
            array(
                'decorator' => 'ViewHelper',
                'options' => array('helper' => 'formSubmit')),
            array(
                'decorator' => array('td' => 'HtmlTag'),
                'options' => array('tag' => 'td', 'colspan' => 4)
                ),
            array(
                'decorator' => array('tr' => 'HtmlTag'),
                'options' => array('tag' => 'tr')),
        ));
   }
    
    
}
