(function(){
	'use strict';
	return;

	var BLOCK = function(){
		var _block;
	
		var query_types={
			order: 300,
			prefix: 'tid',
			pattern_search: /^(.*?)$/,
			pattern_exact: /^\d{1,9}$/,
			template: $('#block-tid'),
			json: {
				check: '/form/check/tid'
			},
			init: function(input_query, block){
				var input=block.find('select');
	
				_block=block;
	
				input.selectize({
					options: [],
					openOnFocus: true,
					closeAfterSelect: true,
					maxItems: 1,
					maxOptions: 50,
					labelField: 'id',
					valueField: 'id',
					searchField: [
						'id'
					],
					load: function(query, callback) {
						if (!query.length){
							return callback();
						}
	
						$.ajax({
							url: LANG+'/form/autocomplete/tid',
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
							return '<div>№'+item.id+'</div>';
						},
						item: function(item, escape) {
							return '<div>'+item.id+'</div>';
						}
					},
					onBlur: function(){
						_block.removeClass('no-results');
					},
					onLoad: function(data){
						_block[data && !data.length?'addClass':'removeClass']('no-results');
					},
					onInitialize: function(){
						this.$control_input.val(input_query);
						this.$control_input.keyup();
	
						this.open();
						this.focus();
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
		};
		
		return query_types;
	}

	window.query_types=window.query_types||[];	
	window.query_types.push(BLOCK);
})();