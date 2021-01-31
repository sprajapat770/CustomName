define(['uiElement','underscore','Magento_Customer/js/customer-data'],function(Component,_,customerData){
	'use strict';
	
	const customName = customerData.get('custom_name');
	return Component.extend({
		
		getItems: function(){
			console.log(customName());
			//return customName().values;
	/*	
			return _.toArray(this.items);
	*/	
		}
	});
});