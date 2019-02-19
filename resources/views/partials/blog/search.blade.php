<div class="c-blog__left">
    <form action="" id="form-blog">
        <input value="{{ app('request')->input('date_from') }}" type="hidden" name="date_from">
        <input value="{{ app('request')->input('date_to') }}" type="hidden" name="date_to">
        <input value="{{ app('request')->input('region') }}" type="hidden" name="region">
        <div class="col-md-8" style="padding: 0px;">
            <div class="form-group">
                <input style="width:100%;" value="{{ app('request')->input('q') }}" type="text" name="q" placeholder="{{ t($type.'.search_by_word') }}">
            </div>
        </div>
        <div style="float:right;">
            <div class="form-group">
                <button type="submit">{{ t($type.'.search_by_word.submit') }}</button>
            </div>
        </div>
    </form>
</div>