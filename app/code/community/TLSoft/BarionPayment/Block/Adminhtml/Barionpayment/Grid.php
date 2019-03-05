<?php
class TLSoft_BarionPayment_Block_Adminhtml_Barionpayment_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		
		$this->addColumn('order_id', array(
            'header'=> Mage::helper('tlbarion')->__('Order #'),
            'width' => '',
			'type' => 'text',
            'renderer' => 'TLSoft_BarionPayment_Block_Adminhtml_Widget_Grid_Column_Renderer_Action2',
            'index' => 'increment_id',
			'actions' => array(
                array(
                    'url' => array('base' => '*/sales_order/view'),
                            'field' => 'order_id'
                        )
                    ),
        ));
		
		$this->addColumn('bariontransactionid', array(
            'header'=> Mage::helper('tlbarion')->__('Transaction ID in Barion'),
            'width' => '',
            'type'  => 'text',
            'index' => 'bariontransactionid',
        ));
		
		/*$this->addColumn('billingname', array(
            'header'=> Mage::helper('tlbarion')->__('Billing Name'),
            'width' => '',
            'type'  => 'text',
            'index' => 'billing_name',
        ));
		*/
		$this->addColumn('amount', array(
            'header'=> Mage::helper('tlbarion')->__('Amount'),
            'type'  => 'currency',
            'index' => 'amount',
			'currency' => 'ccy',
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
		$statuses = Mage::getModel('tlbarion/paymentstatus')->getStatuses();
		$this->addColumn('status', array(
            'header'=> Mage::helper('tlbarion')->__('Transaction Status'),
            'width' => '',
            'type'  => 'options',
            'index' => 'payment_status',
			'options' => $statuses,
        ));
		
		$this->addColumn('created_at', array(
            'header' => Mage::helper('tlbarion')->__('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
			'filter_index' => 'main_table.created_at',
            'width' => '',
        ));
		
		$this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/barionpayment/history'),
                            'field'   => 'real_orderid'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
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
