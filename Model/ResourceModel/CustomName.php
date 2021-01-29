<?php


namespace Magento360\CustomeName\Model\ResourceModel;

/**
 * Class CustomName
 * Magento360\CustomeName\Model\ResourceModel
 */
class CustomName extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('magento360_custom_name', 'entity_id');
    }

    public function getByCustomerId($customer_id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from('magento360_custom_name', 'entity_id')->where('customer_id = :customer_id');

        $bind = [':customer_id' => (string)$customer_id];

        return $connection->fetchOne($select, $bind);
    }

    public function getAllIdsByCustomerId($customer_id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from('magento360_custom_name', 'entity_id')->where('customer_id = :customer_id');

        $bind = [':customer_id' => (string)$customer_id];

        return $connection->fetchAll($select, $bind);
    }


}
