<?php

class TLSoft_BarionPayment_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = "tlbarion";

    protected $_config;

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $this->_config = $this->getMethodCode();
        $locale = Mage::app()->getLocale();
        $this->setTemplate('tlsoft/tlbarion/form.phtml');
        return parent::_construct();
    }

    /**
     * Payment method code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
}
