(function(){
	'use strict';

	var BLOCK = function(){
		var _input,
			_value;
		
		var query_types={
			order: 0,
			prefix: 'query',
			pattern_search: /^(.*?)$/,
			template: $('#block-query'),
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

				INPUT.focus();
				APP.utils.query();

				return this;	
			},
			result: function(){
				return _value;
			}
		};

		return query_types;
	}
	
	window.query_types=window.query_types||[];
	window.query_types.push(BLOCK);
})();