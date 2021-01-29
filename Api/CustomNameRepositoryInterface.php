<?php


namespace Magento360\CustomeName\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento360\CustomeName\Api\Data\CustomNameInterface;

/**
 * Interface CustomNameRepositoryInterface
 * Magento360\CustomeName\Api
 */
interface CustomNameRepositoryInterface
{
    /**
     * @param CustomNameInterface $object
     * @return CustomNameInterface
     */
    public function save(CustomNameInterface $object);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param $customer_id
     * @return mixed
     */
    public function getByCustomerId($customer_id);

    /**
     * @param $customer_id
     * @return mixed
     */
    public function getValuesByCustomerId($customer_id);
    /**
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param CustomNameInterface $object
     * @return mixed
     */
    public function delete(CustomNameInterface $object);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteById($id);
}
