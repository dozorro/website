(function(){
	'use strict';

	var BLOCK = function(){
		var _block;
	
		var query_types={
			order: 500,
			prefix: 'product_name',
			pattern_search: /^(.*?)$/,
			pattern_exact: /^\d{1,9}$/,
			template: $('#block-product_name'),
			json: {
				check: '/form/check/product'
			},
			init: function(input_query, block){
				var input=block.find('select'),
					preselected_value=block.data('preselected_value');

				_block=block;
	
				input.selectize({
					options: [],
					openOnFocus: true,
					closeAfterSelect: true,
					maxItems: 1,
					maxOptions: 50,
					labelField: 'value',
					valueField: 'key',
					searchField: [
						'value',
                        'key'
					],

					load: function(query, callback) {
						$.ajax({
							url: LANG+'/monitoring/names/search/products/?query=' + encodeURIComponent(query),
							type: 'GET',
							dataType: 'json',
							headers: APP.utils.csrf(),
							error: function() {
								callback();
							},
							success: function(res) {
								callback(res);
							}
						});
					},
					render:{
						option: function(item, escape) {
							return '<div>'+escape(item.value)+'</div>';
						},
						item: function(item, escape) {
							return '<div>'+escape(item.value)+'</div>';
						}
					},
					onBlur: function(){
						_block.removeClass('no-results');
					},
					onLoad: function(data){
						_block[data && !data.length?'addClass':'removeClass']('no-results');
					},
					onInitialize: function(){
                        //console.log(preselected_value);
						if(preselected_value){
							var preselected=INPUT.data('preselected');

							if(preselected[query_types.prefix] && preselected[query_types.prefix][preselected_value]) {
								this.addOption({
									key: preselected_value,
									value: preselected[query_types.prefix][preselected_value]
								});

								this.setValue(preselected_value);
								this.blur();
							}
						}else{
							this.$control_input.val(input_query);
							this.$control_input.keyup();
		
							this.open();
							this.focus();
						}
					},
					onChange: function(value){
						INPUT.focus();
						APP.utils.query();
					}
				});
				
				return this;
			},
			result: function(){
				var value=_block.find('[data-value]').data('value');

				return value!='' ? value : false;
			}
		}
		
		return query_types;
	}

	window.query_types=window.query_types||[];	
	window.query_types.push(BLOCK);
})();