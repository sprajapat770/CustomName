<?php
namespace Magento360\CustomeName\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Quote\Model\Quote;

class CustomConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento360\CustomeName\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    protected $taxHelper;
   
    public function __construct(
        \Magento360\CustomeName\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magecomp\Extrafee\Helper\Tax $helperTax

    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->taxHelper = $helperTax;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $ExtrafeeConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
        
        $quote = $this->checkoutSession->getQuote();
        $subtotal = $quote->getSubtotal();
        $ExtrafeeConfig['adminfee'] = $this->dataHelper->getAdminFee();
        $ExtrafeeConfig['adminqty'] = $this->dataHelper->getAdminQty();
        $ExtrafeeConfig['adminofferqty'] = $this->dataHelper->getAdminOfferQty();


        if ($this->taxHelper->isTaxEnabled() && $this->taxHelper->displayInclTax()) {
            $address = $this->getAddressFromQuote($quote);
            $ExtrafeeConfig['adminfee'] = $this->dataHelper->getAdminFee() + $address->getAdminFeeTax();
        }
        if ($this->taxHelper->isTaxEnabled() && $this->taxHelper->displayBothTax()) {

            $address = $this->getAddressFromQuote($quote);
            $ExtrafeeConfig['adminfee'] = $this->dataHelper->getAdminFee();
            $ExtrafeeConfig['custom_fee_amount_inc'] = $this->dataHelper->getAdminFee() + $address->getAdminFeeTax();

        }
        $ExtrafeeConfig['displayInclTax'] = $this->taxHelper->displayInclTax();
        $ExtrafeeConfig['displayExclTax'] = $this->taxHelper->displayExclTax();
        $ExtrafeeConfig['displayBoth'] = $this->taxHelper->displayBothTax();
        $ExtrafeeConfig['exclTaxPostfix'] = __('Excl. Tax');
        $ExtrafeeConfig['inclTaxPostfix'] = __('Incl. Tax');
        $ExtrafeeConfig['TaxEnabled'] = $this->taxHelper->isTaxEnabled();
        $ExtrafeeConfig['show_hide_Adminfee_block'] = ($enabled && $this->dataHelper->getSession()->isLoggedIn()) ? true : false;
        //$ExtrafeeConfig['show_hide_Extrafee_block'] =
        //$ExtrafeeConfig['show_hide_Extrafee_shipblock'] = ($enabled && ($minimumOrderAmount <= $subtotal)) ? true : false;
        return $ExtrafeeConfig;
    }

    protected function getAddressFromQuote(Quote $quote)
    {
        return $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
    }
}
