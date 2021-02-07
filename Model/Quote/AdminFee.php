<?php


namespace Magento360\CustomeName\Model\Quote;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;
use Magento\Quote\Model\Quote\Address;

class AdminFee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    const COLLECTOR_TYPE_CODE = 'custom-admin-fee';

    private $taxCalculator;

    protected $_priceCurrency;

    protected $quoteValidator = null;

    private $customNameRepository;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    protected $searchCriteriaBuilder;

    protected $scopeConfig;
    protected $dataHelper;
    protected $taxHelper;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeConfigInterface $scopeConfig,
        SessionFactory $sessionFactory,
        CustomNameRepositoryInterface $customNameRepository,
        \Magento360\CustomeName\Helper\Data $dataHelper,
        \Magento360\CustomeName\Helper\Tax $taxHelper,
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Model\Calculation $taxCalculator
    ){
        $this->taxHelper = $taxHelper;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $scopeConfig;
        $this->customNameRepository = $customNameRepository;
        $this->sessionFactory = $sessionFactory;
        $this->quoteValidator = $quoteValidator;
        $this->_priceCurrency = $priceCurrency;
        $this->taxCalculator = $taxCalculator;
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

        /*$total->setTotalAmount(self::COLLECTOR_TYPE_CODE, $amount);
        $total->setBaseTotalAmount(self::COLLECTOR_TYPE_CODE, $amount);
        $total->setCustomAmount($amount);
        $total->setBaseCustomAmount($amount);
        $total->setGrandTotal($total->getGrandTotal() + $amount);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() + $amount);
        */

        $enabled = $this->dataHelper->isModuleEnabled();
        $subtotal = $total->getTotalAmount('subtotal');
        if ($enabled) {
            //$fee = $this->dataHelper->getAdminFee();

            $total->setTotalAmount('custom-admin-fee', $amount);
            $total->setBaseTotalAmount('custom-admin-fee', $amount);
            /*$total->setCustomAdminFee($amount);
            $quote->setCustomAdminFee($amount);*/

            $total->setGrandTotal($total->getGrandTotal() + $amount);

            if ($this->taxHelper->isTaxEnabled()) {
                $address = $this->_getAddressFromQuote($quote);
                //$address = $this->_getAddressFromQuote($quote);
                $this->_calculateTax($address, $total);

                $extraTaxables = $address->getAssociatedTaxables();
                $extraTaxables[] = [
                    'code' => 'custom-admin-fee',
                    'type' => 'custom-admin-fee',
                    'quantity' => 1,
                    'tax_class_id' => $this->taxHelper->getTaxClassId(),
                    'unit_price' => $amount,
                    'base_unit_price' => $amount,
                    'price_includes_tax' => false,
                    'associated_item_code' => false
                ];

                $address->setAssociatedTaxables($extraTaxables);
            
            $total->setGrandTotal($total->getGrandTotal() +  $address->getAdminFeeTax());
            }

        }

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

        $result = [
            'code' => self::COLLECTOR_TYPE_CODE,
            'title' => $this->getLabel(),
            'value' => $amount
        ];

        if ($this->taxHelper->isTaxEnabled() && $this->taxHelper->displayInclTax()) {
            $address = $this->_getAddressFromQuote($quote);
            $result = [
                'code' => self::COLLECTOR_TYPE_CODE,
                'value' => $amount + $address->getAdminFeeTax(),
                'title' => $this->getLabel(),
            ];
        }
    
        if ($this->taxHelper->isTaxEnabled() && $this->taxHelper->displayBothTax()) {
            $address = $this->_getAddressFromQuote($quote);
            
            $result = [
                'code' => self::COLLECTOR_TYPE_CODE,
                'value' =>  $amount + $address->getAdminFeeTax() ,
                'title' => $this->getLabel(),
            ];
        }

        return $result;
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

            if ($this->getPurchagedQuantity() < $this->dataHelper->getAdminOfferQty() && $qty < $this->dataHelper->getAdminOfferQty()) {
                 $amount = 0;
            } elseif ($qty < $this->dataHelper->getAdminQty()) {
                    $amount += 1 * $this->dataHelper->getAdminFee();
            } else {
                    $amount += $qty * 0.60;
            }
        }
        return $amount;
    }

    public function getPurchagedQuantity(){
        $qty = 0;
        $customer_id = $this->sessionFactory->create()->getCustomer()->getId();
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customer_id, 'eq')->create();
        $collection = $this->customNameRepository->getList($searchCriteria);
        if (!empty($collection)) {
            foreach ($collection->getItems() as $key => $val) {
                $qty =$qty + $val->getQty();
            }
        }
        
        return $qty;
    }

    protected function _getAddressFromQuote(Quote $quote)
    {
        return $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
    }

    protected function _calculateTax(Address $address, Total $total)
    {
        $taxClassId = $this->taxHelper->getTaxClassId();
        if (!$taxClassId) {
            return $this;
        }

        $taxRateRequest = $this->_getAddressTaxRequest($address);
        $taxRateRequest->setProductClassId($taxClassId);

        $rate = $this->taxCalculator->getRate($taxRateRequest);

        $baseTax = $this->taxCalculator->calcTaxAmount(
            $total->getBaseTotalAmount('custom-admin-fee'),
            $rate,
            false,
            true
        );
        
        $tax = $this->taxCalculator->calcTaxAmount(
            $total->getTotalAmount('custom-admin-fee'),
            $rate,
            false,
            true
        );



        //$total->setBaseMcPaymentfeeTaxAmount($baseTax);
        $total->setAdminFeeTax($tax);

        $appliedRates = $this->taxCalculator->getAppliedRates($taxRateRequest);
        $this->_saveAppliedTaxes($address, $appliedRates, $tax, $baseTax, $rate);

        $total->addBaseTotalAmount('tax', $baseTax);
        $total->addTotalAmount('tax', $tax);

        return $this;
    }

    protected function _getAddressTaxRequest($address)
    {
        $addressTaxRequest = $this->taxCalculator->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $address->getQuote()->getCustomerTaxClassId(),
            $address->getQuote()->getStore()
        );
        return $addressTaxRequest;
    }

    protected function _saveAppliedTaxes(
        Address $address,
        $applied,
        $amount,
        $baseAmount,
        $rate
    ) {
        $previouslyAppliedTaxes = $address->getAppliedTaxes();
        $process = 0;
        if(is_array($previouslyAppliedTaxes)) {
            $process = count($previouslyAppliedTaxes);
        }
        foreach ($applied as $row) {
            if ($row['percent'] == 0) {
                continue;
            }
            if (!isset($previouslyAppliedTaxes[$row['id']])) {
                $row['process'] = $process;
                $row['amount'] = 0;
                $row['base_amount'] = 0;
                $previouslyAppliedTaxes[$row['id']] = $row;
            }

            if ($row['percent'] !== null) {
                $row['percent'] = $row['percent'] ? $row['percent'] : 1;
                $rate = $rate ? $rate : 1;

                $appliedAmount = $amount / $rate * $row['percent'];
                $baseAppliedAmount = $baseAmount / $rate * $row['percent'];
            } else {
                $appliedAmount = 0;
                $baseAppliedAmount = 0;
                foreach ($row['rates'] as $rate) {
                    $appliedAmount += $rate['amount'];
                    $baseAppliedAmount += $rate['base_amount'];
                }
            }

            if ($appliedAmount || $previouslyAppliedTaxes[$row['id']]['amount']) {
                $previouslyAppliedTaxes[$row['id']]['amount'] += $appliedAmount;
                $previouslyAppliedTaxes[$row['id']]['base_amount'] += $baseAppliedAmount;
            } else {
                unset($previouslyAppliedTaxes[$row['id']]);
            }
        }
        $address->setAppliedTaxes($previouslyAppliedTaxes);
    }
}
