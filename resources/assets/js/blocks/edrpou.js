(function(){
	'use strict';

	var BLOCK = function(){
		var _block;
	
		var query_types={
			order: 500,
			prefix: 'edrpou',
			pattern_search: /^(.*?)$/,
			pattern_exact: /^\d{1,9}$/,
			template: $('#block-edrpou'),
			json: {
				check: '/form/check/edrpou'
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
					labelField: 'name',
					valueField: 'id',
					searchField: [
						'name',
						'id'
					],
					load: function(query, callback) {
						$.ajax({
							url: LANG+'/form/autocomplete/edrpou',
							type: 'POST',
							dataType: 'json',
							headers: APP.utils.csrf(),
							data: {
								query: query
							},
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
							return '<div>'+item.name+' #'+item.id+'</div>';
						},
						item: function(item, escape) {
							return '<div>'+item.id+' — '+item.name.trunc(40)+'</div>';
						}
					},
					onBlur: function(){
						_block.removeClass('no-results');
					},
					onLoad: function(data){
						_block[data && !data.length?'addClass':'removeClass']('no-results');
					},
					onInitialize: function(){
						if(preselected_value){
							var preselected=INPUT.data('preselected');

							if(preselected[query_types.prefix] && preselected[query_types.prefix][preselected_value]) {
								this.addOption({
									id: preselected_value,
									name: preselected[query_types.prefix][preselected_value]
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