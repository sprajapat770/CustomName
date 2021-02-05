<?php
namespace Magento360\CustomeName\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

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

    /**
     * @param \Magento360\CustomeName\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento360\CustomeName\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession

    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $ExtrafeeConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
        //$minimumOrderAmount = $this->dataHelper->getMinimumOrderAmount();
        //$ExtrafeeConfig['fee_label'] = $this->dataHelper->getFeeLabel();
        $quote = $this->checkoutSession->getQuote();
        $subtotal = $quote->getSubtotal();
        $ExtrafeeConfig['adminfee'] = $this->dataHelper->getAdminFee();
        $ExtrafeeConfig['adminqty'] = $this->dataHelper->getAdminQty();
        $ExtrafeeConfig['adminofferqty'] = $this->dataHelper->getAdminOfferQty();
        $ExtrafeeConfig['show_hide_Adminfee_block'] = ($enabled && $this->dataHelper->getSession()->isLoggedIn()) ? true : false;
        //$ExtrafeeConfig['show_hide_Extrafee_block'] =
        //$ExtrafeeConfig['show_hide_Extrafee_shipblock'] = ($enabled && ($minimumOrderAmount <= $subtotal)) ? true : false;
        return $ExtrafeeConfig;
    }
}
