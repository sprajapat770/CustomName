<?php


namespace Magento360\CustomeName\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CustomName
 * Magento360\CustomeName\Model
 */
class CustomName extends AbstractModel implements IdentityInterface
{

    const CACHE_TAG = 'magento360_custom_name';

    protected $_cacheTag = 'magento360_custom_name';

    protected $_eventPrefix = 'magento360_custom_name';

    protected function _construct()
    {
        $this->_init('Magento360\CustomeName\Model\ResourceModel\CustomName');
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
