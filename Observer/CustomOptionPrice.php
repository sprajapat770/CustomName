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

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeConfigInterface $scopeConfig,
        SessionFactory $sessionFactory,
        CustomNameRepositoryInterface $customNameRepository
    ){

		$this->scopeConfig = $scopeConfig;
        $this->customNameRepository = $customNameRepository;
        $this->sessionFactory = $sessionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		if ($this->sessionFactory->create()->isLoggedIn()) {
            $qty = 0;
		    $iscustomname = false;
		    /* @var $quote Mage_Sales_Model_Quote */
            $quote = $observer->getQuote();
            foreach ($quote->getAllItems() as $item) {
                /* @var $item Mage_Sales_Model_Quote_Item */
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $price = 20;
                $customOptions = (!empty($options['options']))?$options['options']:'' ;
                if (!empty($customOptions)) {
                    foreach ($customOptions as $option) {
                        $optionTitle = $option['label'];
                        if ($optionTitle == 'Purchaged Name'){
                            $qty = $qty + $item->getQty();
                            $iscustomname = true;
                        }
                    }
                }
            }
            if ($iscustomname){
                if ($this->getPurchagedQuantity() > 50){
                    if ($qty < 50){
                        $adminfee = $quote->getAdminFee();
                        if (!$adminfee) {
                            return $this;
                        }
                        $order = $observer->getOrder();
                        $order->setData('admin_fee', $adminfee);
                        return $this;
                    }else{
                        $adminfee = $quote->getAdminFee();
                        if (!$adminfee) {
                            return $this;
                        }
                        $order = $observer->getOrder();
                        $order->setData('admin_fee', $this->qty * .60);
                        return $this;
                    }
                }
            }
		}
    }

    public function getPurchagedQuantity(){
        $qty = 0;
            $customer_id = $this->sessionFactory->create()->getCustomer()->getId();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customer_id, 'eq')->create();
            $collection = $this->customNameRepository->getList($searchCriteria);
            foreach ($collection->getItems() as $key => $val) {
                $qty =$qty + $val->getQty();
        }
        return $qty;
    }
}
