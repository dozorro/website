@if(isset($lot->id) && $type == 'lot')
    <h2 style="font-size:18px;">{{$lot->title}}</h2>
@elseif(isset($lot->id) && $type == 'change')
    <h2 style="font-size:18px;">{{'#'.$loopIndex .' - '. date('d.m.Y', strtotime($lot->date))}}</h2>
@endif
<div class="overflow-table" data-type="{{ $type }}" data-lot="{{isset($lot->id) ? $lot->id : 0}}">
    <table class="monitoring-table">
        <thead>
        <th>{{ t('monitoring.item.name') }}</th>
        <th>{{ t('monitoring.item.form') }}</th>
        <th>{{ t('monitoring.item.measure') }}</th>
        <th>{{ t('monitoring.item.quantity') }}</th>
        <th>{{ t('monitoring.item.price') }}<br>
            {{ t('monitoring.item.without_tax') }}</th>
        <th>{{ t('monitoring.item.price_tax') }}<br>
            {{ t('monitoring.item.with_tax') }}</th>
        <th>{{ t('monitoring.item.tax') }}</th>
        <th>{{ t('monitoring.item.sum') }}<br>
            {{ t('monitoring.item.with_tax') }}</th>
        <th></th>
        </thead>
        <tbody>

        @foreach($items AS $_item)
            @if(isset($lot->id) && $_item->lot_id == $lot->id || (!isset($lot->id)))
            <tr>
                <td>
                    <span class="item-name">{{$_item->name}}</span>
                    <input data-item value="{{$_item->name}}" id="monitoring-names-edit-{{$_item->id}}" type="text" name="name" class="jsGetInputVal hide" autocomplete="off" placeholder="{{ t('monitoring.names_placeholder') }}" data-js="monitoring_names">
                </td>
                <td>
                    <span class="item-form">{{$_item->form}}</span>
                    <input data-item value="{{$_item->form}}" id="monitoring-forms-edit-{{$_item->id}}" type="text" name="form" class="jsGetInputVal hide" autocomplete="off" placeholder="{{ t('monitoring.forms_placeholder') }}" data-js="monitoring_forms">
                </td>
                <td>
                    <span class="item-measure">{{$_item->measure}}</span>
                    <input data-item value="{{$_item->measure}}" id="monitoring-measures-{{$_item->id}}" type="text" name="measure" class="jsGetInputVal hide" autocomplete="off" placeholder="{{ t('monitoring.measures_placeholder') }}" data-js="monitoring_measures">
                </td>
                <td>
                    <span class="item-quantity">{{$_item->quantity}}</span>
                    <input data-item type="text" class="hide" name="quantity" value="{{$_item->quantity}}" onkeypress="checkData()" placeholder="{{t('monitoring.quantity.only_numeric')}}">
                </td>
                <td>
                    <price class="item-price">{{$_item->formatPrice($_item->price)}}</price>
                    <input data-item type="text" class="hide" name="price" value="{{$_item->price}}" onkeypress="checkData(true)" placeholder="{{t('monitoring.price.only_with_dot')}}">
                </td>
                <td>
                    <span class="item-price_tax">{{$_item->price_tax > 0 ? $_item->formatPrice($_item->price_tax) : ''}}</span>
                </td>
                <td>
                    <span class="item-tax">{{t('monitoring.item.tax_'.$_item->tax)}}</span>
                    <select data-item name="tax" class="hide">
                        <option value="0" @if($_item->tax == 0){{'selected'}}@endif>{{t('monitoring.item.tax_0')}}</option>
                        <option value="7" @if($_item->tax == 7){{'selected'}}@endif>{{t('monitoring.item.tax_7')}}</option>
                        <option value="20" @if($_item->tax == 20){{'selected'}}@endif>{{t('monitoring.item.tax_20')}}</option>
                    </select>
                </td>
                <td>
                    <price class="item-sum">{{$_item->formatPrice($_item->sum)}}</price>
                </td>
                <td>
                    @if(!$tender->is_ready)
                    <button class="edit-row js-edit-row"></button>
                    <a href="#" class="delete-row" data-id="{{$_item->id}}"></a>
                    <input data-item type="hidden" name="id" value="{{$_item->id}}">
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
        </tbody>
        @if(!$tender->is_ready)
        <tfoot>
        <tr>
            <td>
                <input data-item value="" id="monitoring-names" type="text" name="name" class="no-clear jsGetInputVal" autocomplete="off" placeholder="{{ t('monitoring.names_placeholder') }}" data-js="monitoring_names">
            </td>
            <td>
                <input data-item value="" id="monitoring-forms" type="text" name="form" class="no-clear jsGetInputVal" autocomplete="off" placeholder="{{ t('monitoring.forms_placeholder') }}" data-js="monitoring_forms">
            </td>
            <td>
                <input data-item value="" id="monitoring-measures" type="text" name="measure" class="no-clear jsGetInputVal" autocomplete="off" placeholder="{{ t('monitoring.measures_placeholder') }}" data-js="monitoring_measures">
            </td>
            <td>
                <input name="quantity" data-item type="text" value="" onkeypress="checkData()" placeholder="{{t('monitoring.quantity.only_numeric')}}">
            </td>
            <td>
                <input name="price" data-item type="text" value="" onkeypress="checkData(true)" placeholder="{{t('monitoring.price.only_with_dot')}}">
            </td>
            <td>
            </td>
            <td>
                <select data-item name="tax" class="">
                    <option value="0">{{t('monitoring.item.tax_0')}}</option>
                    <option value="7">{{t('monitoring.item.tax_7')}}</option>
                    <option value="20">{{t('monitoring.item.tax_20')}}</option>
                </select>
            </td>
            <td>
                <input type="hidden" value="{{isset($lot->id) ? $lot->id : 0}}" name="lotId" data-item>
                <input type="hidden" value="{{isset($lot->id) ? $type : 'tender'}}" name="type" data-item>
            </td>
            <td></td>
        </tr>
        </tfoot>
        @endif
    </table>
    @if(!$tender->is_ready)
    <div class="list_button_add_row">
        <a href="#" class="add_item" data-lot="{{isset($lot->id) ? $lot->id : 0}}">{{ t('monitoring.add_item') }}</a>
        <a href="#" class="save_item hide" data-lot="{{isset($lot->id) ? $lot->id : 0}}">{{ t('monitoring.save_item') }}</a>
        <a href="#" class="reset hide">{{ t('monitoring.cancel_item') }}</a>
    </div>
    @endif
</div>