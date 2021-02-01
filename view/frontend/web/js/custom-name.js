define(['uiElement','underscore','Magento_Customer/js/customer-data'],function(Component,_,customerData){
	'use strict';

	const customName = customerData.get('custom_name');
	return Component.extend({

		getItems: function(){
			return customName().values;
		},

        getLastItems: function(){
            return customName().values.slice(-1)[0];
        }

	});
});
