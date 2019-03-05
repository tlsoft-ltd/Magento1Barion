<?php 
class TLSoft_BarionPayment_Adminhtml_BarionpaymentController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function historyAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
		
	public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
	
	public function historygridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
	
	
	protected function _initAction()
    {
        $this->loadLayout()
            // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('tlsoft/tlbarion')
            ->_title($this->__('Sales'))->_title($this->__('Barion Payment Transactions'));
         
        return $this;
    }
     
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('tlsoft/tlbarion');
    }
	
	public function exportCsvAction()
    {
        $fileName   = 'transactions.csv';
        $grid       = $this->getLayout()->createBlock('tlbarion/adminhtml_barionpayment_grid2');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

}