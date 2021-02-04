

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data'
], function (Component, quote,customerData) {
    'use strict';

    const customName = customerData.get('custom_name');
    
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

        limit:function (){
            var qty = 0;
            var custom = customName().qty;
            for(var i=0;i < custom.length;i++){
                qty = qty + parseInt(custom[i]);
            } 
            return qty;
        },

        getAdminFee:function (){
            return parseInt(window.adminfee);
        },
        getAdminQty:function (){
            return  parseInt(window.adminqty);
        },
        getAdminOfferQty:function (){
        return  parseInt(window.adminofferqty);
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
