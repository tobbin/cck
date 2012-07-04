<?php

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * Compares two form fields
 *
 * @category Okto
 * @package Nisanth_Validate
 * @since 05-12-2011
 * @author Nisanth
 * @version $Id$
 */
class Nisanth_Validate_GreaterThanField extends Zend_Validate_Abstract
{
    const GREATER = 'greaterThan';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::GREATER => "'%value%' greater than '%fieldValue%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'fieldValue' => '_fieldValue'
    );

    /**
     * Field value to compare with
     *
     * @var string
     */
    protected $_fieldValue;

    /**
     * Field name to compare with
     *
     * @var string
     */
    protected $_fieldName;

    /**
     * Sets validator options
     *
     * @param  mixed $min
     * @return void
     */
    public function __construct($fieldName)
    {
        $this->setFieldName($fieldName);
    }

    /**
     * Set name of field to compare with
     *
     * @param $fieldName
     * @return void
     */
    public function setFieldName($fieldName)
    {
        $this->_fieldName = $fieldName;
    }

    /**
     * Get name of field to compare with
     *
     * @return string Field name
     */
    public function getFieldName()
    {
        return $this->_fieldName;
    }

    /**
     * Returns the value of the field to compare with
     *
     * @return mixed
     */
    public function getFieldValue()
    {
        return $this->_fieldValue;
    }

    /**
     * Sets the value of the field to compare with
     *
     * @param  mixed $min
     * @return Okto_Validate_DateGreaterThanField Provides a fluent interface
     */
    public function setFieldValue($fieldValue)
    {
        $this->_fieldValue = $fieldValue;
        return $this;
    }


    public function isValid($value, $context = null)
    {
        if (!isset($context[$this->getFieldName()])) {
            throw new Zend_Validate_Exception(
                sprintf('Field name %s not found in request', $this->getFieldName())
            );
        }

        $this->setFieldValue($context[$this->getFieldName()]);

        $this->_setValue($value);
        
        // check if the value of field of comparation is NOT empty
        if(isset($context[$this->getFieldName()]) && $context[$this->getFieldName()] != "") {
            
            $currentField = $context[$this->getFieldName()];
            $compareField = $value;
    
            if ($compareField > $currentField) {
                $this->_error(self::GREATER);
                return false;
            }
        }

        return true;
    }

}