<?php


namespace Magento360\CustomeName\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento360\CustomeName\Api\Data\CustomNameInterface;

/**
 * Class CustomName
 * Magento360\CustomeName\Model
 */
class CustomName extends AbstractModel implements CustomNameInterface, IdentityInterface
{

    const CACHE_TAG = 'magento360_custom_name';

    protected $_cacheTag = 'magento360_custom_name';

    protected $_eventPrefix = 'magento360_custom_name';

    protected function _construct()
    {
        $this->_init('Magento360\CustomeName\Model\ResourceModel\CustomName');
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }
    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    /**
     * @param string $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData('customer_id', $customer_id);
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->getData('product_id');
    }

    /**
     * @param string $product_id
     * @return $this
     */
    public function setProductId($product_id)
    {
        return $this->setData('product_id', $product_id);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getData('value');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setValue($value)
    {
        return $this->setData('value', $value);
    }

     /**
     * @return mixed
     */
    public function getQty(){
      return $this->getData('qty');  
    }

    /**
     * @param $qty
     * @return mixed
     */
    public function setQty($qty){
        return $this->setData('qty', $qty);
    }
}
