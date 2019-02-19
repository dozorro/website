@extends('layouts.app')

@section('content')

<div class="c-b">
    <div class="container">

        <div class="block_header_go_short">
            @include('partials.monitoring.monitoring_info', ['profile_link' => false])
        </div>

        <div class="filter_tender">
            <h4 class="js-filter-tender"><span>{{ t('monitoring.form.search_title') }}</span></h4>
            <h4 class="js-filter-tender mobile"><span>{{ t('monitoring.form.search_title') }}</span></h4>
            <form class="inline-layout" method="get" id="form_tenders_search" onsubmit="return false;">
                <div class="form-group">
                    <label for="number_tender">{{ t('monitoring.form.number') }}</label>
                    <div class="input_number_tender"><input value="{{ app('request')->input('tid') }}" type="text" id="number_tender" name="tid" placeholder="{{ t('monitoring.form.number_placeholder') }}"></div>
                </div>
                <div class="form-group">
                    <label for="tender-customer">{{ t('tenders.form.customer') }}</label>
                    <input value="{{ app('request')->input('edrpou') }}" id="tender-customer" type="text" name="edrpou" class="jsGetInputVal" autocomplete="off" placeholder="{{ t('tenders.form.customer_placeholder') }}" data-js="customer_search">
                </div>
                <div class="form-group">
                    <label for="region_tender">{{ t('tenders.form.region') }}</label>
                    <select id="region_tender" name="region">
                        <option value="">{{ t('tenders.form.region_choose') }}</option>
                        @foreach($regions as $id => $region)
                            <option @if(app('request')->input('region') == $id) selected @endif value="{{ $id }}">{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group inline-layout">
                    <button onclick="loadData(true)">{{ t('monitoring.form.submit') }}</button>
                    <a class="reset_filters" href="" onclick="clearData(true);return false;">{{t('tender.remove_filters')}}</a>
                </div>

            </form>
        </div>

        <div class="filter_go">
            <h4 class="js-filter-go">{{t('monitoring.title')}}</h4>
            <h4 class="js-filter-go mobile">{{t('monitoring.title')}}</h4>
            <form>
                <div class="list_radio inline-layout">
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="tenders" data-form="tenders" @if($formType == 'tenders'){{'checked'}}@endif >
                        <label for="tenders">{{ t('monitoring.tab_tenders') }}</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="works" data-form="works" @if($formType == 'works'){{'checked'}}@endif >
                        <label for="works">{{t('monitoring.tab_works')}}</label>
                    </div>
                </div>
            </form>
        </div>

        <div class="list_tender_company" id="for-spinner2">
            <div class="overflow-table">
                <table>
                    <tr>
                        <th width="10%">{{t('monitoring.result.date')}}</th>
                        <th width="25%"><a class="order_up" href="#">{{t('monitoring.result.id')}}</a></th>
                        <th width="15%">{{t('monitoring.result.customer')}}</th>
                        <th width="12%">{{t('monitoring.result.sum')}}</th>
                        <th data-status>{{t('monitoring.result.status')}}</th>
                        <th data-region>{{t('monitoring.result.region')}}</th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="list_tender_company mobile">
            <table>
                <tr>
                    <th>{{t('monitoring.result.last_review')}}</th>
                    <th width="80%"><a class="order_up" href="#">{{t('monitoring.result.id')}}</a></th>
                </tr>
            </table>
        </div>

        <div no-reviews style="text-align: center;">{{t('monitoring.no_results')}}</div>
        <div id="for-spinner1" class="link_pagination none" data-current-page="" data-last-page="">{{ t('monitor.show_more') }}</div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    var defaultForm = '{{$formType}}';
    var route = '{{ route('page.monitoring', ['slug' => $monitor->slug]) }}';
    //columnsState(defaultForm);

    $(function () {

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
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

                    //columnsState(defaultForm);
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

    function clearData(reload) {

        if(reload) {
            window.location = window.location.href.split("?")[0];
            return;
        }

        $('#form_tenders_search').find('input').val('');
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

                    //columnsState(defaultForm);

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

        f201.hide();
        f202.hide();
        f203.hide();
        f204.hide();
        status.hide();
        region.hide();

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

        return true;
    }
</script>
@endpush