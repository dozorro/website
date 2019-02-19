<div class="page-post__sidebar">
    <h4>{{ t('monitoring.customer_address') }}</h4>
    <form class="list_item inline-layout">
        @if(isset($item->procuringEntity->address->postalCode) || $tender->new_index)
        <div class="item width100">
            <h5>{{ t('monitoring.customer.index') }}</h5>
            <input type="text" value="{{ $tender->new_index ? $tender->new_index : $item->procuringEntity->address->postalCode }}" disabled >
            @if(!$tender->is_ready)
            <button class="edit_index"></button>
            <button class="hide submit_index">{{ t('monitoring.customer.index_save') }}</button>
            @endif
        </div>
        @endif
        @if(isset($item->procuringEntity->address->locality))
        <div class="item width50">
            <h5>{{ t('monitoring.customer.city') }}</h5>
            <p>{{ $item->procuringEntity->address->locality }}</p>
        </div>
        @endif
        @if(isset($item->procuringEntity->address->region))
        <div class="item width50">
            <h5>{{ t('monitoring.customer.region') }}</h5>
            <p>{{ $item->procuringEntity->address->region }}</p>
        </div>
        @endif
        <div class="item width100">
            <h5>{{ t('monitoring.customer.address') }}</h5>
            <p>
                {{ $item->procuringEntity->address->countryName }},
                {{ $item->procuringEntity->address->region }},
                @if(isset($item->procuringEntity->address->locality)){{ $item->procuringEntity->address->locality }},@endif
                {{ $item->procuringEntity->address->streetAddress }}
            </p>
        </div>
        <div class="item width100 checkbox">
            <input type="checkbox" value="1" name="is_ok" id="is_ok" @if($tender->is_ok){{'checked'}}@endif @if($tender->is_ready){{'disabled'}}@endif>
            <label for="is_ok">{{ t('monitoring.customer.all_is_good') }}</label>
        </div>
        <div class="item width100 checkbox">
            <input type="checkbox" value="1" name="is_other" id="is_other" @if($tender->is_other){{'checked'}}@endif @if($tender->is_ready){{'disabled'}}@endif>
            <label for="is_other">{{ t('monitoring.other') }}</label>
        </div>
    </form>
</div>
