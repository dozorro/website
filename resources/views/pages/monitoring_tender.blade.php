@extends('layouts/app')

@section('head')
    @if ($item && !$error)
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{t('facebook.site_name')}}">
        @if(!isset($seo->og_title) || !$seo->og_title)
        <meta property="og:title" content="{{htmlentities($item->procuringEntity->name, ENT_QUOTES)}}">
        @endif
        @if(!isset($seo->og_url) || !$seo->og_url)
        <meta property="og:url" content="{{env('ROOT_URL')}}/{{Request::path()}}">
        @endif
        @if(!isset($seo->og_description) || !$seo->og_description)
        <meta property="og:description" content="{{!empty($item->title) ? htmlentities($item->title, ENT_QUOTES) : t('facebook.tender_no_name')}}">
        @endif
    @endif
@endsection

@section('content')

    @if ($item && !$error)
        <div class="bg_grey page-post">
            <div class="container">
                @include('partials/blocks/tender/header', ['tender_page' => false])
                <div class="row">
                    <div class="col-md-9">
                        <div class="page-post__price ">
                            <div class="row">
                                <div class="item col-sm-6">
                                    <p>{{ t('monitoring.customer.contract_sum') }}</p>
                                    <price>{{ str_replace('.00', '', number_format($item->__contracts_price, 2, '.', ' ')) . ' ' . $item->contracts[0]->value->currency }}</price>
                                </div>
                                <div class="item col-sm-6">
                                    <p>{{ t('monitoring.customer.new_contract_sum') }}</p>
                                    <price class="color_main">
                                        @if($items_sum)
                                            {{ str_replace('.00', '', number_format($item->__contracts_price-$items_sum, 2, '.', ' ')) . ' ' . $item->contracts[0]->value->currency }}
                                        @else
                                            0 {{ $item->contracts[0]->value->currency }}
                                        @endif
                                    </price>
                                </div>
                                @if(!$mviolations->isEmpty())
                                <div style="position: absolute;right: 30px;top: 3px;">
                                    <a data-form="violations" data-formjs="ngo_open_modal" id="show-violations">{{ t('monitoring.show_violations') }}</a>
                                </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="page-post__link_doc">
                            <a href="#" class="link_post_doc" data-form="contract-docs" data-formjs="ngo_open_modal">{{ t('monitoring.contract.docs') }}</a>
                            @include('partials.monitoring._monitoring_modal')
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form class="form_post" style="margin-bottom: 10px;">
                            @foreach($monitoringItems as $k => $_item)
                                @include('partials.monitoring.items', ['type'=>$monitoringType, 'lot'=>$_item, 'loopIndex'=>$k+1])
                            @endforeach
                            {{--
                            @if(!empty($changes))
                                @foreach($changes as $k => $change)
                                    @include('partials.monitoring.items', ['type'=>'change', 'lot'=>$change, 'loopIndex'=>$k+1])
                                @endforeach
                            @else
                                @foreach($lots as $k => $lot)
                                    @include('partials.monitoring.items', ['type'=>'lot', 'loopIndex'=>$k+1])
                                @endforeach
                            @endif
                            --}}
                            <div class="list_button_form inline-layout">

                                <div class="list_button_form__button">
                                    @if(!$tender->is_ready)
                                    <button id="submit-form" data-state="1">{{ t('monitoring.submit') }}</button>
                                    @elseif($tender->is_ready)
                                    <a href="#" id="submit-form" data-state="0" class="reset">{{ t('monitoring.cancel') }}</a>
                                    @endif
                                </div>

                                <div class="page-post__price ">
                                    <div class="item">
                                        <p>{{ t('monitoring.customer.new_contract_sum') }}</p>
                                        <price class="color_main">
                                            @if($items_sum)
                                                {{ str_replace('.00', '', number_format($item->__contracts_price-$items_sum, 2, '.', ' ')) . ' ' . $item->contracts[0]->value->currency }}
                                            @else
                                                0 {{ $item->contracts[0]->value->currency }}
                                            @endif
                                        </price>
                                    </div>

                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <form class="form_post" style="padding-top: 15px;" action="{{ route('monitoring_violation.save') }}" method="post">
                            @if(!$violations->isEmpty())
                                <h2 style="padding-left: 30px;" class="tender-header__h1  maxheight">{{ t('monitoring.violation_title') }}</h2>
                                {{ csrf_field() }}
                                <input type="hidden" name="tid" value="{{ $item->tenderID }}">
                                <input type="hidden" name="monitor" value="{{ $tender->monitoring_id }}">
                                <div class="list_button_form inline-layout">
                                    <select name="violation">
                                        @foreach($violations as $violation)
                                        <option value="{{ $violation->id }}">{{ $violation->text }}</option>
                                        @endforeach
                                    </select>
                                    <br><br>
                                    <textarea name="comment" style="width: 400px;height: 100px;"></textarea>
                                    <br><br>
                                    <div class="list_button_form__button">
                                        <button type="submit">{{ t('monitoring.submit_violation') }}</button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($error)
        <div style="padding:20px 20px 40px 10px;text-align:center">
            {!!$error!!}
        </div>
    @else
        <div style="padding:20px 20px 40px 10px;text-align:center">
            {{t('tender.tender_not_found')}}
        </div>
    @endif

    @if(!$mviolations->isEmpty())
    <div data-form-modal="violations" class="none">
        <div id="overlay" class="overlay2" data-form-modal="violations"></div>
        <div class="modal_div show welcome-modal" data-form-modal="violations" style="overflow: scroll;">
            <div class="modal_close"></div>
            <div class="content-holder">
                <h3>{{t('monitoring.violation.modal_window.title')}}</h3>
                <div class="desc-modal">
                    {{t('monitoring.violation.modal_window.description')}}
                </div>
                <div style="text-align: left;">
                    <h4>{{t('monitoring.violation.modal_window.comments_block')}}</h4>
                    <ul>
                        @foreach($mviolations as $v)
                        <li>{{ $v->violation->text }}</li>
                        @endforeach
                    </ul>
                </div>
                <br><br>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    var route = '{{ route('page.monitoring_tender', ['id' => $item->tenderID]) }}';

    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var table = $('.form_post');

        $('#show-violations').on('click', function() {

        });

        table.on('click', '.reset', function() {
            var _this = $(this);
            var table = _this.closest('.overflow-table');
            table.find('tbody').find('input, select').addClass('hide');
            table.find('tbody').find('label').addClass('hide');
            table.find('tbody').find('input').next('.selectize-control').addClass('hide');
            table.find('tbody').find('input').next('.selectize-control').find('.selectize-dropdown').addClass('hide');
            table.find('tbody').find('tr').removeClass('edit');
            table.find('.reset').addClass('hide');
            table.find('.save_item').addClass('hide');
            table.find('.add_item').removeClass('hide');

            return false;
        });

        table.on('click', '.js-edit-row', function() {
            var _this = $(this);
            var item = _this.closest('tr');
            var table = _this.closest('.overflow-table');
            item.find('input, select').removeClass('hide');
            item.find('label').removeClass('hide');
            item.find('input').next('.selectize-control').removeClass('hide');
            item.find('input').next('.selectize-control').find('.selectize-dropdown').removeClass('hide');
            item.addClass('edit');
            table.find('.reset').removeClass('hide');
            table.find('.save_item').removeClass('hide');
            table.find('.add_item').addClass('hide');

            return false;
        });

        table.on('click', '#submit-form', function() {
            var _this = $(this);

            $.post(route + '/active', {ready:_this.data('state')},
                function (data, textStatus, xhr) {
                    if (data.status == 'ok') {
                        window.location.reload(true);
                    }
            });

            return false;
        });

        table.on('click', '.delete-row', function() {
            if(confirm('{{t('monitoring.confirm_delete')}}')) {
                var _this = $(this);
                if (_this.data('id')) {
                    $.post(route + '/delete', {id: _this.data('id')},
                            function (data, textStatus, xhr) {
                                if (data.status == 'ok') {
                                    _this.closest('tr').remove();
                                }
                            });
                }
            }
            return false;
        });

        table.on('click', '.save_item', function() {
            var _this = $(this);
            var table = _this.closest('.overflow-table');
            var trs =  table.find('tbody tr.edit');

            trs.each(function(){
                var valid = true;
                var tr = $(this);
                var item = $(this).find('[data-item]');

                item.each(function(){
                    if($(this).val() == '') {
                        console.log($(this));
                        valid = false;
                        return false;
                    }
                });

                if(valid) {
                    $.post(route+'/update', item.serializeArray(),
                        function (data, textStatus, xhr) {
                            if(data.status == 'ok') {
                                var _item = item.serializeArray();
                                var q;
                                var p;
                                var tax;

                                for(var i = 0; i < _item.length; i++) {
                                    tr.find('.item-' + _item[i].name).html(_item[i].value);

                                    if(_item[i].name.indexOf('tax') == 0) {
                                        tax = parseInt(_item[i].value);

                                        if(tax == 0) {
                                            tr.find('.item-' + _item[i].name).html('{{t('monitoring.item.tax_0')}}');
                                        } else if(tax == 7) {
                                            tr.find('.item-' + _item[i].name).html('{{t('monitoring.item.tax_7')}}');
                                        } else if(tax == 20) {
                                            tr.find('.item-' + _item[i].name).html('{{t('monitoring.item.tax_20')}}');
                                        }
                                    }
                                    else if(_item[i].name == 'price') {
                                        p = parseFloat(_item[i].value);
                                    } else if(_item[i].name == 'quantity') {
                                        q = parseInt(_item[i].value);
                                    }
                                }

                                if(tax > 0) {
                                    p = p + (p * (tax / 100));
                                    tr.find('.item-price_tax').html(Number(p).toFixed(2));
                                } else {
                                    tr.find('.item-price_tax').html('');
                                }

                                tr.find('.item-sum').html(Number((p*q).toFixed(2)));
                                _this.next().trigger('click');
                            }
                        });
                } else {
                    alert('{{t('monitoring.enter_all_data')}}');
                }
            });

            return false;
        });

        table.on('click', '.add_item', function() {
            var _this = $(this);
            var lot_id = _this.data('lot');
            var type = _this.data('type');
            var table = _this.closest('.overflow-table');
            var items =  table.find('tfoot [data-item]');
            var valid = true;

            items.each(function(){
                console.log($(this).attr('name')+'-'+$(this).val());
                if($(this).val() == '') {
                    valid = false;
                    return false;
                }
            });

            if(valid) {
                $.post(route+'/create', items.serializeArray(),
                    function (data, textStatus, xhr) {
                        if(data.status == 'ok') {
                            var _items = items.serializeArray();
                            var tax_text = '';
                            var checked_0 = '';
                            var checked_7 = '';
                            var checked_20 = '';

                            if(_items[5].value == 0) {
                                tax_text = '{{t('monitoring.item.tax_0')}}';
                                checked_0 = 'selected';
                            } else if(_items[5].value == 7) {
                                tax_text = '{{t('monitoring.item.tax_7')}}';
                                checked_7 = 'selected';
                            } else if(_items[5].value == 20) {
                                tax_text = '{{t('monitoring.item.tax_20')}}';
                                checked_20 = 'selected';
                            }

                            var price_tax = _items[5].value > 0 ? Number(parseInt(_items[4].value) + (parseInt(_items[4].value) * (parseFloat(_items[5].value) / 100))).toFixed(2) : 0;
                            var sum = price_tax ? Number((parseInt(_items[3].value)*parseFloat(price_tax)).toFixed(2)) : Number((parseInt(_items[3].value)*parseFloat(_items[4].value)).toFixed(2));

                            var data = $('#new-item-tpl').tmpl({
                                name: _items[0].value,
                                form: _items[1].value,
                                measure: _items[2].value,
                                quantity: _items[3].value,
                                price: _items[4].value,
                                price_tax: price_tax,
                                tax: _items[5].value,
                                sum: sum,
                                id: data.response.id,
                                lot_id: lot_id,
                                tax_text: tax_text,
                                checked_0:checked_0,
                                checked_7:checked_7,
                                checked_20:checked_20,
                            });

                            _this.closest('.overflow-table').find('tbody').append(data);

                            items.each(function(){
                                if($(this).hasClass('no-clear')) {
                                    var $select = $(this).selectize();
                                    var control = $select[0].selectize;
                                    control.clear();
                                } else {
                                    if($(this).attr('name') != 'type' && $(this).attr('name') != 'lotId' && $(this).attr('name') != 'lot_id' && $(this).attr('name') != 'tax') {
                                        $(this).val('');
                                    }
                                }
                            });

                            $('[data-js]').each(function(){
                                var self = $(this);

                                if (typeof APP.js[self.data('js')] === 'function'){
                                    APP.js[self.data('js')](self, self.data());
                                } else {
                                    console.log('No `' + self.data('js') + '` function in app.js');
                                }
                            });
                        }
                });
            } else {
                alert('{{t('monitoring.enter_all_data')}}');
            }

            return false;
        });

        $('.edit_index').on('click', function() {
            $(this).prev().removeAttr('disabled');
            $(this).hide();
            $(this).next().removeClass('hide');
            return false;
        });

        $('#is_other').on('click', function() {
            var _this = $(this);

            $.post(route + '?is_other=' + _this.is(':checked'),
                function (data, textStatus, xhr) {
            });
        });

        $('#is_ok').on('click', function() {
            var _this = $(this);

            $.post(route + '?is_ok=' + _this.is(':checked'),
                function (data, textStatus, xhr) {
            });
        });

        $('.submit_index').on('click', function() {
            var _this = $(this);

            $.post(route + '?new_index=' + _this.prev().prev().val(),
                function (data, textStatus, xhr) {
                    if(data.status == 'ok') {
                        _this.prev().prev().attr('disabled','disabled');
                        _this.addClass('hide');
                        _this.prev().show();
                    }
            });
            return false;
        });
    });

    function checkData(point) {
        if((event.charCode >= 48 && event.charCode <= 57) || (point == true && event.charCode == 46)) {
            return;
        }
        event.preventDefault();
    }
</script>
<script id="new-item-tpl" type="text/x-jquery-tmpl">
    <tr>
        <td>
        <span class="item-name">${name}</span>
        <input data-item value="${name}" id="monitoring-names-edit-${id}" type="text" name="name" class="jsGetInputVal hide" autocomplete="off" placeholder="{{ t('monitoring.names_placeholder') }}" data-js="monitoring_names">
        </td>
        <td>
        <span class="item-form">${form}</span>
        <input data-item value="${form}" id="monitoring-forms-edit-${id}" type="text" name="form" class="jsGetInputVal hide" autocomplete="off" placeholder="{{ t('monitoring.forms_placeholder') }}" data-js="monitoring_forms">
        </td>
        <td>
        <span class="item-measure">${measure}</span>
        <input data-item value="${measure}" id="monitoring-measures-edit-${id}" type="text" name="measure" class="jsGetInputVal hide" autocomplete="off" placeholder="{{ t('monitoring.measures_placeholder') }}" data-js="monitoring_measures">
        </td>
        <td>
        <span class="item-quantity">${quantity}</span>
        <input data-item type="text" class="hide" value="${quantity}" name="quantity" onkeypress="checkData()" placeholder="{{t('monitoring.quantity.only_numeric')}}">
        </td>
        <td>
        <price class="item-price">${price}</price>
        <input data-item type="text" class="hide" value="${price}" name="price" onkeypress="checkData(true)" placeholder="{{t('monitoring.price.only_with_dot')}}">
        </td>
        <td>
        <span class="item-price_tax">${price_tax}</span>
        </td>
        <td>
        <span class="item-tax">${tax_text}</span>
        <select data-item name="tax" class="hide">
            <option value="0" ${checked_0}>{{t('monitoring.item.tax_0')}}</option>
            <option value="7" ${checked_7}>{{t('monitoring.item.tax_7')}}</option>
            <option value="20" ${checked_20}>{{t('monitoring.item.tax_20')}}</option>
        </select>
        </td>
        <td>
        <price class="item-sum">${sum}</price>
        </td>
        <td>
        <button class="edit-row js-edit-row"></button>
        <a href="#" class="delete-row" data-id="${id}"></a>
        <input data-item type="hidden" name="id" value="${id}">
        </td>
    </tr>
</script>
@endpush