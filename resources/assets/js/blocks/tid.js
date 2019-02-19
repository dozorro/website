(function(){
	'use strict';
    //return;
 
	var BLOCK = function(){
		var _input,
			pattern=/^UA\-20\d{2}\-\d{2}\-\d{2}\-\d{6}(\-([a-z]))?$/,
			_value;
	
		var query_types={
			order: 300,
			prefix: 'tid',
			pattern_search: pattern,
			template: $('#block-tid'),
			init: function(input_query, block){
				var preselected_value=block.data('preselected_value');

				_input=block.find('input');
				_value=input_query;

				_input.keyup(function(e){
					if(e.keyCode==KEY_RETURN){
						INPUT.focus();
						APP.utils.query();
					}else{
						_value=_input.val();
	
						APP.utils.query();
					}
				});

				if(preselected_value){
					_input.val(decodeURI(preselected_value));

					_value=_input.val();
					_input.keyup();
				}

				_input.autoGrowInput({
					minWidth: 20,
					comfortZone: 0
				});

				if(_value){
					INPUT.focus();
				}else{
					_input.focus();
				}

				APP.utils.query();

				return this;	
			},
			result: function(){
				return _value;
			},
			validate: function(query){
				var valid='UA-2015-01-01-000001';

				return pattern.test(query+valid.substr(query.length));
			}			
		};
		
		return query_types;
	}

	window.query_types=window.query_types||[];	
	window.query_types.push(BLOCK);
})();