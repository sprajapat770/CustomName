define(['uiElement','underscore','Magento_Customer/js/customer-data'],function(Component,_,customerData){
	'use strict';

	const customName = customerData.get('custom_name');
	return Component.extend({

        initialize: function () {
            this._super();
           // this.customename = customerData.get('custom_name').values.slice(-1)[0];
        },

		getItems: function(){
			if(_.isEmpty(customName())){
				return {};
			}
			return customName();
		},

        getLastItem: function(){
            if(_.isEmpty(customName())){
				return '';
			}else{
            return customName().values.slice(-1)[0];
			}
        },
	});
});
