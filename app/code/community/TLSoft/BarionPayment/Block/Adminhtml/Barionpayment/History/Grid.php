<?php
class TLSoft_BarionPayment_Block_Adminhtml_Barionpayment_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('adminhtml_barionpaymenthistorygrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {
		$transid = $this->getRequest()->getParam('real_orderid');
		$collection = Mage::getResourceModel('tlbarion/transactions_history_collection')->addFieldToFilter('transaction_id',$transid);
        $this->setCollection($collection);
		return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumn('entity_id', array(
            'header'=> Mage::helper('tlbarion')->__('History ID'),
            'width' => '',
            'type'  => 'text',
            'index' => 'entity_id',
        ));
		
		$this->addColumn('error_message', array(
            'header'=> Mage::helper('tlbarion')->__('Error Message'),
            'width' => '',
            'type'  => 'text',
            'index' => 'error_message',
        ));
		
		$this->addColumn('error_number', array(
            'header'=> Mage::helper('tlbarion')->__('Error Number'),
            'width' => '',
            'type'  => 'text',
            'index' => 'error_number',
        ));
		
		$this->addColumn('created_at', array(
            'header' => Mage::helper('tlbarion')->__('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '',
        ));
		
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/barionpayment/historygrid', array('_current'=>true));
    }

}
