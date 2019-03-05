<?php
class TLSoft_BarionPayment_Block_Adminhtml_Barionpayment_Grid2 extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('adminhtml_tlbarion');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {
		$model = Mage::getModel('tlbarion/paymentmethod');
		$collection=$model->getTransModel()->getCollection()->joinOrder();
        $this->setCollection($collection);
        //return $this;
		return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumn('real_orderid', array(
            'header'=> Mage::helper('tlbarion')->__('Transaction Id'),
            'width' => '',
            'type'  => 'text',
            'index' => 'real_orderid',
        ));
		
		$this->addColumn('billingname', array(
            'header'=> Mage::helper('tlbarion')->__('Billing Name'),
            'width' => '',
            'type'  => 'text',
            'index' => 'billingname',
        ));
		
		$this->addColumn('amount', array(
            'header'=> Mage::helper('tlbarion')->__('Amount'),
            'type'  => 'text',
            'index' => 'amount',
        ));
		
		$this->addColumn('ccy', array(
            'header'=> Mage::helper('tlbarion')->__('Currency'),
            'type'  => 'text',
            'index' => 'ccy',
        ));
		
		if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
				'filter_index' => 'main_table.store_id',
                'display_deleted' => true,
            ));
        }
		
		$this->addColumn('created_at', array(
            'header' => Mage::helper('tlbarion')->__('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
			'filter_index' => 'main_table.created_at',
            'width' => '',
        ));
			
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
		
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
