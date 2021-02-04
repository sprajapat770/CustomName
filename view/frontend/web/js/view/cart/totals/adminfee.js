

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento360_CustomeName/js/custom-name'
], function (Component, quote,customName) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento360_CustomeName/summary/adminfee'
        },
        totals: quote.getTotals(),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() != 0;
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

            return 'Custom Name Fee';
        },

        /**
         * @return {Number}
         */
        getPureValue: function () {
            var price = 0;
            var qty = 0;
            for (var i=0; i < window.checkoutConfig.quoteItemData.length; i++) {
                console.log(window.checkoutConfig.quoteItemData);
                for( var j=0;j < window.checkoutConfig.quoteItemData[i].options.length;j++){
                    if (window.checkoutConfig.quoteItemData[i].options[j].label == "Purchaged Name"){
                        qty += window.checkoutConfig.quoteItemData[i].qty;
                    }
                }
            }

            if (this.getAdminOfferQty() > this.limitRemained()) {

                if (qty > this.getAdminQty()) {
                    price += qty * 0.60;

                } else if (qty < this.getAdminQty && qty > 0) {
                    price = this.getAdminFee();
                } else {
                    price = 0;
                }
            }
            return price;
        },

        limitRemained:function (){
            customName.getItems().values;
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        },
        getAdminFee:function (){
            return  window.adminfee;
        },
        getAdminQty:function (){
            return  window.adminqty;
        },
        getAdminOfferQty:function (){
        return  window.adminofferqty;
    },


    });
});
