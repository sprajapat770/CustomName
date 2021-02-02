<?php

namespace Magento360\CustomeName\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;
use Magento360\CustomeName\Api\Data\CustomNameInterface;
use Magento360\CustomeName\Model\CustomNameFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class CustomOptionPrice implements ObserverInterface
{

    private $customNameRepository;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    protected $searchCriteriaBuilder;

    protected $scopeConfig;

    public function __construct(SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeConfigInterface $scopeConfig,
        CustomNameRepositoryInterface $customNameRepository){

		$this->scopeConfig = $scopeConfig;
        $this->customNameRepository = $customNameRepository;
        $this->sessionFactory = $sessionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		if ($this->sessionFactory->create()->isLoggedIn()) {
			
			$customer_id = $this->sessionFactory->create()->getCustomer()->getId();
	    	$searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customer_id, 'eq')->create();
	        $collection = $this->customNameRepository->getList($searchCriteria);
	       	$qty = 0;
            $dataValues = [];
            $dataProducts = [];
            $dataQty = [];

            foreach ($collection->getItems() as $key => $val) {
                $dataValues[$val->getId()] = $val->getValue();
                $dataProducts[$val->getId()] = $val->getProductId();
                $dataQty[$val->getId()] = $val->getQty();
            }
            $item = $observer->getEvent()->getData('quote_item');
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
    	}
    }
}