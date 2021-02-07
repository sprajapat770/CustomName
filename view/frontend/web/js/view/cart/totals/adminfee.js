

define([
    'ko',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'
], function (ko,Component, quote,customerData,priceUtils,totals) {
    'use strict';

    const customName = customerData.get('custom_name');
    var show_hide_Adminfee_blockConfig = window.checkoutConfig.show_hide_Adminfee_block;
    var fee_label = 'Custom Name Fee';//window.checkoutConfig.fee_label;
    var adminqty = window.checkoutConfig.adminqty;
    var adminfee = window.checkoutConfig.adminfee;
    var adminofferqty = window.checkoutConfig.adminofferqty;
    //var custom_in_fee_amount = window.checkoutConfig.custom_fee_amount_inc;

    return Component.extend({
        defaults: {
            template: 'Magento360_CustomeName/cart/total/admin-fee'
        },
        totals: quote.getTotals(),
        canVisibleAdminfeeBlock: ko.observable(show_hide_Adminfee_blockConfig),
        getFeeLabel:ko.observable(fee_label),
        getInFeeLabel:ko.observable(window.checkoutConfig.inclTaxPostfix),
        getExFeeLabel:ko.observable(window.checkoutConfig.exclTaxPostfix),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() != 0 && this.canVisibleAdminfeeBlock ;
        },
        isDisplayBoth: function () {
            return window.checkoutConfig.displayBoth;
        },
        displayExclTax: function () {
            return window.checkoutConfig.displayExclTax;
        },
        displayInclTax: function () {
            return window.checkoutConfig.displayInclTax;
        },
        isTaxEnabled: function () {
            return window.checkoutConfig.TaxEnabled;
        },

        /**
         * Get surcharge title
         *
         * @returns {null|String}
         */
        getTitle: function () {
            if (!this.totals()) {
                return null;
            }
            return fee_label;
        },
        /*getconfigValue: function () {
            var serviceUrl = url.build('modulename/custom/storeconfig');

            storage.get(serviceUrl).done(
                function (response) {
                    if (response.success) {
                        return response.value
                    }
                }
            ).fail(
                function (response) {
                    return response.value
                }
            );
            return false;
        },*/

        limit:function (){
            var qty = 0;
            if(_.isEmpty(customName().qty)){
                return qty;
            }
            var custom = customName().qty;
            for(var i=0;i < custom.length;i++){
                qty = qty + parseInt(custom[i]);
            }
            return qty;
        },

        getAdminFee:function (){
            return parseFloat(adminfee);
        },
        getAdminQty:function (){
            return  parseFloat(adminqty);
        },
        getAdminOfferQty:function (){
        return  parseFloat(adminofferqty);
        },

        /**
         * @return {Number}
         */
        getPureValue: function () {
            var price = 0;
            var qty = 0;

            for (var i=0; i < window.checkoutConfig.quoteItemData.length; i++) {
                for( var j=0;j < window.checkoutConfig.quoteItemData[i].options.length;j++){
                    if (window.checkoutConfig.quoteItemData[i].options[j].label == "Purchaged Name"){
                        qty += parseInt(window.checkoutConfig.quoteItemData[i].qty);
                    }
                }
            }

            if (this.getAdminOfferQty() > (this.limit() + qty)) {
                price = 0;
            } else if(qty <= this.getAdminQty() && qty >= 0) {
                    price = this.getAdminFee();
            } else {
                    price += (qty * 0.60);
            }
            return price;
        },
                
        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        },
        getExFormattedPrice: function() {
            return this.getFormattedPrice(this.getPureValue());
            //return priceUtils.formatPrice(price, quote.getPriceFormat());
        },
        getInFormattedPrice: function() {
            var price = 0;
            console.log(totals);
            if (this.totals() && totals.getSegment('custom-admin-fee')) {
                price = totals.getSegment('custom-admin-fee').value;
            }

            return this.getFormattedPrice(price);

        },
    });
});
