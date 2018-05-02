<?php
/**
 * @category    Graphic Sourcecode
 * @package     Rofra_Salesorderitemgrid
 * @license     http://opensource.org/licenses/OSL-3.0
 * @author      Rodolphe Franceschi <rodolphe.franceschi@gmail.com>
 */
class Rofra_Salesorderitemgrid_Block_Adminhtml_Order_Items_Grid_Renderer_InputInline
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {	
		$css_class = null;

		if ($this->getColumn()->getIndex() == 'qty_packed') 
		{

			if ($row->getData('qty_invoiced') == $row->getData('qty_packed')) 
			{
				$css_class = 'qty_packed_ok';
			} // if sonu
			elseif ($row->getData('qty_packed') == 0) 
			{
				$css_class = 'qty_packed_empty';
			} // elseif sonu
			elseif ($row->getData('qty_packed') != $row->getData('qty_invoiced')) 
			{
				$css_class = 'qty_packed_half';
			} // else sonu
		} // if sonu

		$html = '<input type="text" ';
        $html .= 'id="'.$this->getColumn()->getId().'_'.$row->getId().'" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $this->htmlEscape($row->getData($this->getColumn()->getIndex())) . '" ';
        $html .= 'class="input-text ' . $this->getColumn()->getId() . ' ' . $this->getColumn()->getInlineCss() . ' '.$css_class.' " onchange="updateTextualField(this, '. $row->getId() .'); return false;"/></div>';

		//$html = parent::render($row);
		//$html .= '<button onclick="updateField(this, '. $row->getId() .'); return false">' . Mage::helper('salesorderitemgrid')->__('UPD') . '</button>';

        return $html;
    }
}