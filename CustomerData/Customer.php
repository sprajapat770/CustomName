<?php

namespace Magento360\CustomeName\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Customer section
 */
class Customer implements SectionSourceInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    protected $searchCriteriaBuilder;

    protected $_list;

    protected $productFactory;

    public function __construct(
        CurrentCustomer $currentCustomer,
        \Magento360\CustomeName\Api\CustomNameRepositoryInterface $list,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {

        $this->productFactory = $productFactory; 
        $this->_list = $list;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        $values = [];
        $qty = [];
        $productNames = [];
        $customerId = $this->currentCustomer->getCustomerId();

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id',$customerId,'eq')->create();

        $items = $this->_list->getList($searchCriteria);

        foreach ($items->getItems() as $key => $val) {
            $values[] = $val->getValue();
            $qty[] = (int) $val->getQty();
            $productNames[] = $this->productFactory->create()->load($val->getProductId())->getName();
        }

        return [
            "values"=>$values,
            "qty"=>$qty,
            "productNames" => $productNames];
    }
}
