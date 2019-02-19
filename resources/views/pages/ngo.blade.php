@extends('layouts.app')

@section('content')

<div class="c-b">
    <div class="container">

        <div class="block_header_go_short" data-js="ngo_header_reload">
            @include('partials.ngo_info', ['profile_link' => false, 'showFilter' => true])
        </div>

        <div class="filter_tender">
            <h4 class="js-filter-tender"><span>{{ t('tenders.form.search_title') }}</span></h4>
            <h4 class="js-filter-tender mobile"><span>{{ t('tenders.form.search_title') }}</span></h4>
            <form class="inline-layout" method="get" id="form_tenders_search" onsubmit="return false;">
                <div class="form-group">
                    <label for="number_tender">{{ t('tenders.form.number') }}</label>
                    <div class="input_number_tender"><input value="{{ app('request')->input('tid') }}" type="text" id="number_tender" name="tid" placeholder="{{ t('tenders.form.number_placeholder') }}"></div>
                </div>
                <div class="form-group">
                    <label for="tender-customer">{{ t('tenders.form.customer') }}</label>
                    <input value="{{ app('request')->input('edrpou') }}" id="tender-customer" type="text" name="edrpou" class="jsGetInputVal" autocomplete="off" placeholder="{{ t('tenders.form.customer_placeholder') }}" data-js="customer_search">
                </div>
                <div class="form-group">
                    <label for="status_tender">{{ t('tenders.form.status') }}</label>
                    <select id="status_tender" name="status">
                        <option value="">{{ t('tenders.form.status_choose') }}</option>
                        @foreach($dataStatus as $id => $status)
                            <option @if(app('request')->input('status') == $id) selected @endif value="{{ $id }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="region_tender">{{ t('tenders.form.region') }}</label>
                    <select id="region_tender" name="region">
                        <option value="">{{ t('tenders.form.region_choose') }}</option>
                        @foreach($regions as $id => $region)
                            <option @if(app('request')->input('region') == $id) selected @endif value="{{ $id }}">{{ $region }}</option>
                        @endforeach
                    </select>
                </div><br>
                <div class="form-group">
                    <div class="input-group date">
                        <input placeholder="{{ t('tenders.form.date_from') }}" name="date_from" type="text" class="form-control datepicker" value="{{ app('request')->input('date_from') }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <input placeholder="{{ t('tenders.form.date_to') }}" name="date_to" type="text" class="form-control datepicker" value="{{ app('request')->input('date_to') }}">
                    </div>
                </div>
                <div class="form-group inline-layout">
                    <button onclick="loadData(true)">{{ t('tenders.form.submit') }}</button>
                    <a class="reset_filters" href="" onclick="clearData();loadData(false);return false;">{{t('tender.remove_filters')}}</a>
                </div>

            </form>
        </div>

        <div class="filter_go">
            <h4 class="js-filter-go">{{t('tenders.ngo.title')}}</h4>
            <h4 class="js-filter-go mobile">{{t('tenders.ngo.title')}}</h4>
            <form>
                <div class="list_radio inline-layout">
                    {{--<div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="region" data-form="region" @if($formType == 'region'){{'checked'}}@endif >
                        <label for="region">{{ t('tenders.ngo.tab_by_region') }}</label>
                    </div>--}}
                    @if(!empty($user->__moder_forms))
                        <div class="form-holder radio">
                            <input type="radio" value="" name="radio" id="moderation" data-form="moderation" @if($formType == 'moderation'){{'checked'}}@endif >
                            <label for="moderation">{{t('tenders.ngo.moderation')}}</label>
                        </div>
                    @endif
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="F201" data-form="F201" @if($formType == 'F201'){{'checked'}}@endif >
                        <label for="F201">{{t('tenders.ngo.tab_in_work')}}</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="F202" data-form="F202" @if($formType == 'F202'){{'checked'}}@endif >
                        <label for="F202">{{t('tenders.ngo.tab_action')}}</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="F203" data-form="F203" @if($formType == 'F203'){{'checked'}}@endif >
                        <label for="F203">{{t('tenders.ngo.tab_results')}}</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="F204" data-form="F204" @if($formType == 'F204'){{'checked'}}@endif >
                        <label for="F204">{{t('tenders.ngo.tab_archive')}}</label>
                    </div>
                </div>
            </form>
        </div>

        <div class="list_tender_company" id="for-spinner2">
            <div class="overflow-table">
                <table>
                    <tr>
                        <th width="10%">{{t('tenders.result.last_review')}}</th>
                        <th width="25%"><a class="order_up" href="#">{{t('tenders.result.id')}}</a></th>
                        <th width="15%">{{t('tenders.result.customer')}}</th>
                        <th width="12%">{{t('tenders.result.sum')}}</th>
                        <th data-F201>{{t('tenders.result.f201')}}</th>
                        <th data-F202 class="none">{{t('tenders.result.f202')}}</th>
                        <th data-F203 class="none">{{t('tenders.result.f203')}}</th>
                        <th data-F204 class="none">{{t('tenders.result.f204')}}</th>
                        <th data-status class="none">{{t('tenders.result.status')}}</th>
                        <th data-region class="none">{{t('tenders.result.region')}}</th>
                        <th data-moderation class="none">{{t('tenders.result.form_status')}}</th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="list_tender_company mobile">
            <table>
                <tr>
                    <th>{{t('tenders.result.last_review')}}</th>
                    <th width="80%"><a class="order_up" href="#">{{t('tenders.result.id')}}</a></th>
                </tr>
            </table>
        </div>

        <div no-reviews style="text-align: center;">{{t('tenders.ngo.no_results')}}</div>
        <div id="for-spinner1" class="link_pagination none" data-current-page="" data-last-page="">{{ t('ngo.show_more') }}</div>

        @include('partials.donors')

    </div>
</div>

@endsection

@push('scripts')

<script>

    pieChart()

    var defaultForm = '{{$formType}}';
    var route = '{{ route('page.ngo', ['slug' => $ngo->slug]) }}';
    columnsState(defaultForm);

    $(function () {

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.fn.datepicker.dates['ua'] = {
            days: ["Неділя", "Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота"],
            daysShort: ["Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            daysMin: ["Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            months: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Версень', 'Жовтень', 'Листопад', 'Грудень'],
            monthsShort: ['Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер', 'Лип', 'Сер', 'Вер', 'Жов', 'Лис', 'Гру'],
            today: "Сьогодні",
            clear: "Очистити",
            format: "dd.mm.yyyy",
            titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
            weekStart: 1
        };

        $('.datepicker').datepicker({
            language: 'ua'
        });

        var opts = {
            lines: 13 // The number of lines to draw
            , length: 28 // The length of each line
            , width: 4 // The line thickness
            , radius: 3 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#000' // #rgb or #rrggbb or array of colors
            , opacity: 0.25 // Opacity of the lines
            , rotate: 0 // The rotation offset
            , direction: 1 // 1: clockwise, -1: counterclockwise
            , speed: 1 // Rounds per second
            , trail: 60 // Afterglow percentage
            , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            , zIndex: 2e9 // The z-index (defaults to 2000000000)
            , className: 'spinner' // The CSS class to assign to the spinner
            , top: '50%' // Top position relative to parent
            , left: '40%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'relative' // Element positioning
        }
        var target = document.getElementById('for-spinner1');
        var spinner = new Spinner(opts).spin(target);

        opts.top = '10px';
        opts.left = '50%';
        var target2 = document.getElementById('for-spinner2');
        var spinner2 = new Spinner(opts).spin(target2);

        $('.spinner').hide();
        loadData({{$withParams ? true : false}});

        $('.link_pagination').on('click', function() {
            var pagination = $(this);
            var page = parseInt($(this).attr('data-current-page'));
            var last_page = parseInt($(this).attr('data-last-page'));
            var page = page + 1;

            $('#for-spinner1 .spinner').show();

            $.post(route + '/' + defaultForm+'?page=' + page + '&' + $('#form_tenders_search').serialize(),
                function(data, textStatus, xhr)
                {
                    $('.overflow-table tbody').append(data['desktop']);
                    $('.list_tender_company.mobile table').append(data['mobile']);

                    columnsState(defaultForm);
                    pagination.attr('data-current-page', page);

                    if(page >= last_page) {
                        pagination.hide();
                    }

                    $('#for-spinner1 .spinner').hide();
                });
        });

        $('[data-form]').on('click', function() {

            defaultForm = $(this).data('form');
            clearData();
            loadData();

            return true;
        });
    });

    function clearData() {

        $('#form_tenders_search').find('input').val('');
        $('#form_tenders_search').find('#status_tender option:first').prop('selected', true);
        $('#form_tenders_search').find('#region_tender option:first').prop('selected', true);
        var $select = $('#tender-customer').selectize();
        var control = $select[0].selectize;
        control.clear();

        return true;
    }

    function loadData(submit) {

        $('#for-spinner2 .spinner').show();
        window.History.pushState(null, document.title, route + '/' + defaultForm + (submit ? '?'+$('#form_tenders_search').serialize() : ''));

        $.post(route + '/' + defaultForm + (submit ? '?'+$('#form_tenders_search').serialize() : ''),
                function(data, textStatus, xhr)
                {
                    $('.overflow-table tbody').find('tr[data-schema]').remove();
                    $('.overflow-table tbody').append(data['desktop']);
                    $('.list_tender_company.mobile table').find('tr[data-schema]').remove();
                    $('.list_tender_company.mobile table').append(data['mobile']);
                    $('.link_pagination').attr('data-current-page', 1);
                    $('.link_pagination').attr('data-last-page', data['lastPage']);

                    columnsState(defaultForm);

                    if(data['lastPage'] <= 1) {
                        $('.link_pagination').hide();
                    } else {
                        $('.link_pagination').show();
                    }

                    $('#for-spinner2 .spinner').hide();

                    if(data['lastPage']) {
                        $('[no-reviews]').hide();
                    } else if(!data['lastPage']) {
                        $('[no-reviews]').show();
                    }
                });

        return true;
    }

    function columnsState(defaultForm) {
        var container = $('.container');
        var f201 = container.find('[data-F201]');
        var f202 = container.find('[data-F202]');
        var f203 = container.find('[data-F203]');
        var f204 = container.find('[data-F204]');
        var status = container.find('[data-status]');
        var region = container.find('[data-region]');
        var moderation = container.find('[data-moderation]');

        f201.hide();
        f202.hide();
        f203.hide();
        f204.hide();
        status.hide();
        region.hide();
        moderation.hide();

        if(defaultForm == 'F201') {
            f201.show();
            status.show();
            region.show();
        }else if(defaultForm== 'F202') {
            f201.show();
            f202.show();
            region.show();
        }else if(defaultForm == 'F203') {
            f201.show();
            f202.show();
            f203.show();
        }else if(defaultForm == 'F204') {
            f204.show();
        }else if(defaultForm == 'region') {
            status.show();
            region.show();
        }
        else if(defaultForm == 'moderation') {
            status.show();
            moderation.show();
        }

        return true;
    }
</script>



@endpush