<?php

namespace Magento360\CustomeName\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Tax extends AbstractHelper
{
    const XML_PATH_TAX_ENABLED = 'magento360/tax/enable';
    const XML_PATH_TAX_CLASS   = 'magento360/tax/tax_class';
    const XML_PATH_TAX_DISPLAY = 'magento360/tax/display';

    public function isTaxEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_TAX_ENABLED, 'store');
    }

    public function getTaxClassId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TAX_CLASS, 'store');
    }

    public function getTaxDisplay()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TAX_DISPLAY, 'store');
    }

    public function displayInclTax()
    {
        return in_array($this->getTaxDisplay(), [2]);
    }

    public function displayExclTax()
    {
        return in_array($this->getTaxDisplay(), [1]);
    }

    public function displayBothTax()
    {
        return in_array($this->getTaxDisplay(), [3]);
    }

    public function displaySuffix()
    {
        return ($this->getTaxDisplay() == 3);
    }
}
