<?php


namespace Magento360\CustomeName\Api\Data;

/**
 * Interface CustomNameInterface
 * Magento360\CustomeName\Api\Data
 */
interface CustomNameInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getCustomerId();

    /**
     * @param string $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id);

    /**
     * @return string
     */
    public function getProductId();

    /**
     * @param string $product_id
     * @return $this
     */
    public function setProductId($product_id);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param $value
     * @return mixed
     */
    public function setValue($value);
}
