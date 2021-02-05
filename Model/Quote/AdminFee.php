<?php


namespace Magento360\CustomeName\Model\Quote;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;

class AdminFee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    const COLLECTOR_TYPE_CODE = 'custom-admin-fee';


    private $customNameRepository;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    protected $searchCriteriaBuilder;

    protected $scopeConfig;
    protected $dataHelper;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeConfigInterface $scopeConfig,
        SessionFactory $sessionFactory,
        CustomNameRepositoryInterface $customNameRepository,
        \Magento360\CustomeName\Helper\Data $dataHelper
    ){
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $scopeConfig;
        $this->customNameRepository = $customNameRepository;
        $this->sessionFactory = $sessionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->setCode(self::COLLECTOR_TYPE_CODE);
    }

    /**
     * Collect address discount amount
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        $amount = 0;
       /* foreach($quote->getItemsCollection() as $_quoteItem){
            $amount += $_quoteItem->getQty() * \Magento360\CustomeName\Pricing\Adjustment::ADJUSTMENT_VALUE;
        }*/
        if ($this->sessionFactory->create()->isLoggedIn()) {
            $amount = $this->addAdminFee($quote,$amount);
        }

        $total->setTotalAmount(self::COLLECTOR_TYPE_CODE, $amount);
        $total->setBaseTotalAmount(self::COLLECTOR_TYPE_CODE, $amount);
        $total->setCustomAmount($amount);
        $total->setBaseCustomAmount($amount);
        $total->setGrandTotal($total->getGrandTotal() + $amount);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() + $amount);
        return $this;
    }

    /**
     * @param Total $total
     */
    protected function clearValues(Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount(self::COLLECTOR_TYPE_CODE, 0);
        $total->setBaseTotalAmount(self::COLLECTOR_TYPE_CODE, 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(
        Quote $quote,
        Total $total
    ) {

        $amount = 0;

       /* foreach ($quote->getItemsCollection() as $_quoteItem) {
            $amount += $_quoteItem->getQty() * \Magento360\CustomeName\Pricing\Adjustment::ADJUSTMENT_VALUE;
        }*/

        if ($this->sessionFactory->create()->isLoggedIn()) {
            $amount = $this->addAdminFee($quote,$amount);
        }
        return [
            'code' => $this->getCode(),
            'title' => __('Custom Total'),
            'value' => $amount
        ];
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Custom Name Fee');
    }

    public function addAdminFee($quote,$amount = 0){
        $iscustomname = false;
        $qty = 0;
        foreach ($quote->getItemsCollection() as $item) {
            /* @var $item Mage_Sales_Model_Quote_Item */
            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $customOptions = (!empty($options['options'])) ? $options['options'] : '';
            if (!empty($customOptions)) {
                foreach ($customOptions as $option) {
                    $optionTitle = $option['label'];
                    if ($optionTitle == 'Purchaged Name') {
                        $qty = $qty + $item->getQty();
                        $iscustomname = true;
                    }
                }
            }
        }

        if ($iscustomname) {

            if ($this->getPurchagedQuantity() > $this->dataHelper->getAdminOfferQty()) {
                if ($qty < $this->dataHelper->getAdminQty()) {
                    $amount = 1 * $this->dataHelper->getAdminFee();
                } else {
                    $amount += $qty * 0.60;
                }
            }else{
                $amount = 0;
            }
        }
        return $amount;
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
