(function(){
    'use strict';

    var BLOCK = function(){
        var _input,
            _value,
            value_value_from,
            value_value_to,
            value_from,
            value_to,
            mask={
                'alias': 'numeric',
                'groupSeparator': ' ',
                'autoGroup': true
            };

        var query_types={
            order: 0,
            prefix: 'value',
            pattern_search: /^(.*?)$/,
            template: $('#block-value'),
            init: function(input_query, block){
                var preselected_value=block.data('preselected_value');

                _input=block.find('input');

                _value=input_query;

                value_from = $(_input[0]);
                value_to = $(_input[1]);


                value_from.inputmask(mask);
                value_to.inputmask(mask);

                _input.keyup(function(e){
                    value_value_from=parseInt(value_from.val().replace(/ /g, ''));
                    value_value_to=parseInt(value_to.val().replace(/ /g, ''));
                    
                    if(value_value_from>=0){
                        if(e.keyCode==KEY_RETURN){
                            INPUT.focus();
                            APP.utils.query();
                        }else{
                            _value=value_value_from + (value_value_to ? '-' + value_value_to : '');

                            APP.utils.query();
                        }
                    }
                });

                if(preselected_value){
                    preselected_value=preselected_value.split('-');

                    if(preselected_value[0]){
                        value_from.val(decodeURI(preselected_value[0]));
                    }

                    if(preselected_value[1]){
                        value_to.val(decodeURI(preselected_value[1]));
                    }

                    _input.keyup();
                }

                _input.autoGrowInput({
                    minWidth: 20,
                    comfortZone: 0
                });

                if(_value){
					INPUT.focus();
				}else{
					value_from.focus();
				}
				
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