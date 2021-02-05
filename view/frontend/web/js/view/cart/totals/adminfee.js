

define([
    'ko',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data'
], function (ko,Component, quote,customerData) {
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
            template: 'Magento360_CustomeName/summary/adminfee'
        },
        totals: quote.getTotals(),
        canVisibleAdminfeeBlock: show_hide_Adminfee_blockConfig,
        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
           console.log(this.canVisibleAdminfeeBlock)
            return this.isFullMode() && this.getPureValue() != 0 && this.canVisibleAdminfeeBlock;
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
        getconfigValue: function () {
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
        },

        limit:function (){
            var qty = 0;
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

            if (this.getAdminOfferQty() < this.limit()) {

                if (qty > this.getAdminQty()) {
                    price += (qty * 0.60);

                } else if (qty < this.getAdminQty() && qty > 0) {
                    price = this.getAdminFee();
                } else {
                    price = 0;
                }
            }else {
                price = 0;
            }
            return price;
        },
                /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }

    });
});
