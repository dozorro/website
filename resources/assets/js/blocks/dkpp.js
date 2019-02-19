(function(){
	'use strict';

	var json;

	var BLOCK = function(){
		var _block,
			pattern=/^\d{1,9}$/;
	
		var query_types={
			order: 200,
			prefix: 'dkpp',
			pattern_search: pattern,
			template: $('#block-dkpp'),
			json: {
				check: '/form/check/dkpp'
			},
			load: function(){
				if(!json){
					$.ajax({
						method: 'POST',
						url: LANG+'/form/data/dkpp',
						dataType: 'json',
						headers: APP.utils.csrf(),
						success: function(response){
							json=response;
						}
					});
				}
			},
			remove: function(){
				//clearInterval(interval);
			},
			init: function(input_query, block){
				var input=block.find('select'),
					button=block.find('button'),
					preselected_value=block.data('preselected_value'),
					multi_value=block.data('multi_value'),
					checked_options;

				var dropdown,
					offset,
					interval,
					is_select=false;

				_block=block;
				
				input.selectize({
					options: json,
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
					render:{
						option: function(item, escape) {
							return '<div><input type="checkbox" data-selectable value="'+item.id+'"><span class="ch"></span>'+item.name+' #'+item.id+'</div>';
						},
						item: function(item, escape) {
							return '<div>'+item.id+' — '+item.name.trunc(40)+'</div>';
						}
					},
					onInitialize: function(){
						if(multi_value){
							this.setValue(multi_value);
						}else if(preselected_value){
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
							this.open();
		
							this.$control_input.val(input_query);
							this.$control_input.trigger('update');
							
							if(input_query==''){
								this.$dropdown.hide();
							}
						}
					},
					onChange: function(value){
						INPUT.focus();
						APP.utils.query();
					},
					onType: function(text){
						this.$dropdown.show();
						_block[!this.currentResults.items.length?'addClass':'removeClass']('no-results');
					},
      				onDropdownOpen: function(){
						interval=setInterval(function(){
							var checked_options=_block.find('input[type="checkbox"]:checked');
			
							button[checked_options.length?'show':'hide']();
							$('.selectize-dropdown')[checked_options.length?'addClass':'removeClass']('checked');

							if(checked_options.length){
								offset=dropdown.offset();

								button.css({
									top: dropdown.height()+button.outerHeight()-5,
									left: 40,
									position: 'absolute'
								});
								
								button.find('span').html(checked_options.length);
							}
						}, 200);
					},
					onDropdownClose: function(){
						if(is_select){
							checked_options=_block.find('input[type="checkbox"]:checked');
		
							if(checked_options[0]){
								this.setValue(checked_options[0].value);
		
								checked_options=checked_options.slice(1);
							}

							$.each(checked_options, function(i){
								var self=$(this);
			
								self.data('input_query', '');
								self.data('block_type', query_types.prefix);

								self.data('multi_value', self.val());
			
								APP.utils.block.add(self);
							});
	
							setTimeout(function(){
								INPUT.focus();
							}, 300);
						}
	
						clearInterval(interval);
					},
					onBlur: function(){
						_block.removeClass('no-results');
						button.hide();
					}
				});
				
				dropdown=_block.find('.selectize-dropdown');
	
				button.mousedown(function(e){
					is_select=true;
				});

				return this;	
			},
			result: function(){
				var value=_block.find('[data-value]').data('value');
				/*
				value=value.split('-')[0].replace(/0+$/, '');
				value=_block.find('[data-value]').data('value');
				*/

				return value!='' ? value : false;
			},
			validate: function(query){
				var valid='00.00.00-00.00';

				return pattern.test(query+valid.substr(query.length));
			}			
		};

		return query_types;
	}
		
	window.query_types=window.query_types||[];
	window.query_types.push(BLOCK);
})();