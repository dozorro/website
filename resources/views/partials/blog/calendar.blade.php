@if(!empty($regions))
<div class="c-list-card">
    <div class="c-list-card__inner">
        <h3 class="c-list-card__header">{{t('blog.calendar')}}</h3>
        <form action="">
            <input value="{{ app('request')->input('q') }}" type="hidden" name="q">
            <div class="c-list-card__cards">
                <div class="form-group">
                    <select id="region_tender" name="region">
                        <option value="">{{ t('blog.search.region') }}</option>
                        @foreach($regions as $id => $region)
                            <option @if(app('request')->input('region') == $id) selected @endif value="{{ $id }}">{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group date">
                    <input placeholder="{{ t('blog.search.date_from') }}" name="date_from" type="text" class="form-control datepicker" value="{{ app('request')->input('date_from') }}">
                    <div class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </div>
                </div>
                <br>
                <div class="input-group date">
                    <input placeholder="{{ t('blog.search.date_to') }}" name="date_to" type="text" class="form-control datepicker" value="{{ app('request')->input('date_to') }}">
                    <div class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <button type="submit">{{ t('blog.search_by_date.submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif