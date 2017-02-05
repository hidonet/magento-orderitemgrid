<?php
/**
 * @category    Graphic Sourcecode
 * @package     Rofra_Salesorderitemgrid
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 * @author      Rodolphe Franceschi <rodolphe.franceschi@gmail.com>
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
/**
 * Add 'custom_attribute' attribute for entities
*/
$entities = array(
        'order_item'
);

// Add VARCHAR attributes
$options = array(
        'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
        'visible'  => true,
        'required' => false
);

foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'qty_packed', $options);
}


$installer->endSetup();
