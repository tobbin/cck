<?php

class Nisanth_View_Helper_Decorator {
   
   public $elementDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'th', 'escape' => false)),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
	public $elementDecorators1 = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'span', 'escape' => false)),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
   public $buttonDecorators = array(
		'ViewHelper',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array(array('label' => 'HtmlTag'), array('tag' => 'th', 'placement' => 'prepend')),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
	public $fileDecorators = array(
		'File',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'th')),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);

    public $checkBoxDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'tdLeft')),
		array('Label', array('tag' => 'th')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);

    public $formDecorator = array(
                'FormElements',
                    array('HtmlTag', array('tag' => 'table', 'id' => 'id-form')),
                            'Form',
            );
}

