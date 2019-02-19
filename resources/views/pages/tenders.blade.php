@extends('layouts.app')

@section('content')

<div class="c-b">
    <div class="container">
        @if($customer)
        <div class="tender_customer">
            <div class="inline-layout">
                <div class="img-holder mobile">
                    @if($customer && $customer['image'])
                    <img src="{{ $customer['image'] }}" alt="{{ $customer['name'] }}">
                    @endif
                </div>
                <div class="info_tender_customer">
                    <h3>
                        @if($customer)
                            {{ $customer['name'] }}
                        @else
                            {{ t('tenders.text_info_customer') }}
                        @endif
                    </h3>
                    <div class="info_customer">
                        <div class="item inline-layout">
                            <div class="name">{{ t('tenders.tenders_count') }}</div>
                            <div class="value">
                                @if($customer_total)
                                    {{ $customer_total->tenders_count }}
                                @else
                                    0
                                @endif
                            </div>
                        </div>
                        <div class="item inline-layout">
                            <div class="name">{{ t('tenders.tenders_sum') }}</div>
                            <div class="value">
                                @if($customer_total)
                                    {{ number_format($customer_total->tenders_sum, 0, '', ' ') . ' ' . t('tenders.currency')}}
                                @else
                                    0
                                @endif
                            </div>
                        </div>
                        <div class="item inline-layout">
                            <div class="name">{{ t('tenders.tenders_reviews') }}</div>
                            <div class="value">
                                @if($customer_total)
                                    {{ $customer_total->tenders_reviews }}
                                @else
                                    0
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="img-holder">
                    @if($customer && $customer['image'])
                        <img src="{{ $customer['image'] }}" alt="{{ $customer['name'] }}">
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="filter_tender">
            <h4 class="js-filter-tender"><span>{{ t('tenders.form.search_title') }}</span></h4>
            <h4 class="js-filter-tender mobile"><span>{{ t('tenders.form.search_title') }}</span></h4>

            <form class="inline-layout" action="{{ route('page.tenders') }}" method="get" id="form_tenders_search">
                <input value="{{ app('request')->input('edrpous') }}" type="hidden" name="edrpous">
                <div class="form-group">
                    <label for="number_tender">{{ t('tenders.form.number') }}</label>
                    <div class="input_number_tender"><input value="{{ app('request')->input('tid') }}" type="text" id="number_tender" name="tid" placeholder="{{ t('tenders.form.number_placeholder') }}"></div>
                </div>
                <!--
                <div class="form-group">
                    <label for="">{{ t('tenders.form.sum') }}</label>
                    <div class="inline-layout">
                        <div class="input_price_from"><input value="{{ app('request')->input('price_from') }}" type="text" id="price_from" name="price_from" placeholder=""></div>
                        <span>—</span>
                        <div class="input_price_before"><input value="{{ app('request')->input('price_to') }}" type="text" id="price_before" name="price_to" placeholder=""></div>
                    </div>
                </div>
                -->
                <div class="form-group">
                    <label for="tender-customer">{{ t('tenders.form.customer') }}</label>
                    <input value="{{ app('request')->input('edrpou') }}" id="tender-customer" type="text" name="edrpou" class="jsGetInputVal" autocomplete="off" placeholder="{{ t('tenders.form.customer_placeholder') }}" data-js="customer_search">
                </div>
                <!--<div class="form-group">
                    <label for="">@lang('tenders.form.violation')</label>
                    <select name="">
                        <option value="">@lang('tenders.form.violation_choose')</option>
                        <option value="1">Варіант 1</option>
                        <option value="2">Варіант 2</option>
                    </select>
                </div>-->
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
                </div>

                <!--<div class="form-group">
                    <label for="object_tender">@lang('tenders.form.subject')</label>
                    <input value="{{ app('request')->input('cpv') }}" type="text" id="object_tender" name="cpv" placeholder="">
                </div>-->
                <div class="form-group inline-layout">
                    <button>{{ t('tenders.form.submit') }}</button>
                    <a class="reset_filters" href="{{ route('page.tenders') }}">{{t('tender.remove_filters')}}</a>
                </div>

            </form>
        </div>

        <!--<div class="filter_go">
            <h4 class="js-filter-go">ТЕНДРИ В РОБОТІ ГО</h4>
            <form>
                <div class="list_radio inline-layout">
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="radio1">
                        <label for="radio1">В роботі</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="radio2">
                        <label for="radio2">Дії</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="radio3">
                        <label for="radio3">Результати</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="radio4">
                        <label for="radio4">Архів</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="radio5">
                        <label for="radio5">Дії/Результати</label>
                    </div>
                </div>

            </form>
        </div>-->

        @include('partials._tenders_table', ['showReactionFilter'=>false])

    </div>

    @if($tenders->isEmpty())
        <div class="link_pagination" onclick="javascript:;" style="background-image: none;cursor:default;">{{ t('tenders.no_data') }}</div>
    @elseif($tenders->currentPage() < $tenders->lastPage())
        <div id="for-spinner" class="link_pagination" data-current-page="{{ $tenders->currentPage() }}" data-last-page="{{ $tenders->lastPage() }}">{{ t('tenders.show_more') }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function(){

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
        var target = document.getElementById('for-spinner')
        var spinner = new Spinner(opts).spin(target);
        $('.spinner').hide();

        if($('#tender-customer').val() != '') {
            $('.selectize-control .item').html('{{ $customer['name'] }}');
            $('.selectize-control .item').attr('data-value', '{{ $customer['code'] }}');
        }

        $('#tender-customer').on('change', function() {
            if($(this).val() != '') {
               $('#form_tenders_search').submit();
            }
        });

        $('.link_pagination').on('click', function() {
            var page = parseInt($(this).attr('data-current-page'));
            var last_page = parseInt($(this).attr('data-last-page'));
            var page = page + 1;
            $('.spinner').show();

            $.get('{{ route('page.tenders') }}?page=' + page + '&' + $('#form_tenders_search').serialize(),
                function(data, textStatus, xhr)
                {
                    $('.overflow-table tbody').append(data['desktop']);
                    $('.list_tender_company.mobile table').append(data['mobile']);
                    $('.link_pagination').attr('data-current-page', page);

                    if(page >= last_page) {
                        $('.link_pagination').hide();
                    }

                    $('.spinner').hide();
            });
        });
    });
</script>
@endpush