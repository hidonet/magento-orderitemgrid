<?php
/**
 * @category    Graphic Sourcecode
 * @package     Rofra_Salesorderitemgrid
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 * @author      Rodolphe Franceschi <rodolphe.franceschi@gmail.com>
 */
class Rofra_Salesorderitemgrid_Adminhtml_Order_ItemsController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/sales/order_items');
	}

	public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('sales/order_items');
        $this->_addContent($this->getLayout()->createBlock('salesorderitemgrid/adminhtml_order_items'));
        $this->getLayout()->getBlock('head')->setTitle($this->__('Order Items'));
        $this->renderLayout();
    }

    public function ajaxUpdateFieldAction()
    {
        $fieldId = (int) $this->getRequest()->getParam('id');
        $title = $this->getRequest()->getParam('title');
        $attributeName = $this->getRequest()->getParam('attr');

		$log = array();
		$log[] = __LINE__." - ".$title;
        if ($fieldId && $attributeName) {
            $model = Mage::getModel('sales/order_item')->load($fieldId);
			
			if ($attributeName == 'qty_packed') 
			{
				$is_qty_ok = 0;
			
				$log[] = __LINE__." - ".$title;
				$log[] = __LINE__." - qi:".intval($model->getData('qty_invoiced'));
				if (intval($title) > 0 and intval($model->getData('qty_invoiced')) < intval($title)) 
				{
					$log[] = __LINE__." - ".$title;
					$title = intval($model->getData('qty_invoiced'));
					$log[] = __LINE__." - ".$title;
				} // if sonu
				
			}

			$log[] = __LINE__." - ".$title;
			$model->setData($attributeName, $title);
            $model->save();
			$log[] = __LINE__." - qi:".intval($model->getData('qty_invoiced'));

			if ($attributeName == 'qty_packed') 
			{
				$status_changed = 0;

				if (intval($model->getData('qty_invoiced') )== intval($model->getData('qty_packed'))) 
				{
					$is_qty_ok = 1;

					$auto_status_active = Mage::getStoreConfig('salesorderitemgrid/auto_status_change/active');
					$auto_status_target = trim(Mage::getStoreConfig('salesorderitemgrid/auto_status_change/target_status'));
					
					if ($auto_status_active == 1 and $auto_status_target != '') 
					{
						$res	= Mage::getSingleton('core/resource');
						$db		= $res->getConnection('core_write');
						$table	= $res->getTableName('sales_flat_order_item');

						$totals = $db->query("SELECT IFNULL(SUM(qty_packed),0) AS qty_packed_total, IFNULL(SUM(qty_invoiced),0) AS qty_invoiced_total FROM ".$table." WHERE order_id = ".$model->getOrderId()." AND parent_item_id IS NULL;")->fetch();

						if ($totals['qty_packed_total'] > 0 and $totals['qty_packed_total'] == $totals['qty_invoiced_total']) 
						{
							$order = Mage::getModel('sales/order')->load($model->getOrderId());
							if ($order->getId() > 0) 
							{
								$order->setStatus($auto_status_target)->save();
								$order = Mage::getModel('sales/order')->load($order->getId());
								if ($order->getId() > 0 and $order->getStatus() == $auto_status_target) 
								{
									$status_changed = 1;
								} // if sonu
							} // if sonu
						} // if sonu
						
					} // if sonu
					
				} // if sonu

				$log[] = __LINE__." - ".$title;
				header('Content-Type: application/json');
				echo json_encode(array(
							'field_name' => $attributeName,
							'qty_invoiced' => $model->getData('qty_invoiced'),
							'saved_qty_packed' => $title,
							'is_qty_ok' => $is_qty_ok,
							'log' => join(' // ',$log),
							'order_status_changed' => $status_changed,
							)
						);
			} // if sonu
		}
    }


    /**
     * Export product grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'sales_order_items.csv';
        $content    = $this->getLayout()->createBlock('salesorderitemgrid/adminhtml_order_items_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export product grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'sales_order_items.xml';
        $content    = $this->getLayout()->createBlock('salesorderitemgrid/adminhtml_order_items_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }


    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');

        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);

        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
    }
}