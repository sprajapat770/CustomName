<?php


namespace Magento360\CustomeName\Model\ResourceModel\CustomName;

/**
 * Class Collection
 * Magento360\CustomeName\Model\ResourceModel\CustomName
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'magento360_custom_name_collection';
    protected $_eventObject = 'magento360_custom_name_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento360\CustomeName\Model\CustomName', 'Magento360\CustomeName\Model\ResourceModel\CustomName');
    }

}
