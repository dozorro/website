@extends('layouts.app')

@section('content')
    <div class="c-b">
        <div class="container">

            @include('partials.customer_info')

            <div class="filter_tender">
                <h4 class="js-filter-tender"><span>{{ t('tenders.form.search_title') }}</span></h4>
                <h4 class="js-filter-tender mobile"><span>{{ t('tenders.form.search_title') }}</span></h4>

                <form class="inline-layout" action="" method="get" id="form_tenders_search">

                    <div class="form-group">
                        <label for="number_tender">{{ t('tenders.form.number') }}</label>
                        <div class="input_number_tender"><input value="{{ app('request')->input('tid') }}" type="text" id="number_tender" name="tid" placeholder="{{ t('tenders.form.number_placeholder') }}"></div>
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
                    </div>
                    <div class="form-group inline-layout">
                        <button>{{ t('tenders.form.submit') }}</button>
                        <a class="reset_filters" href="{{ route('page.tenders') }}">{{t('tender.remove_filters')}}</a>
                    </div>

                </form>
            </div>

            <div class="filter_go">
                <h4 class="js-filter-go">{{t('customers.ngo_customers_title')}}</h4>
                <h4 class="js-filter-go mobile">{{t('customers.ngo.title')}}</h4>
                <form>
                    <div class="list_radio inline-layout">
                        <div class="form-holder radio">
                            <input @if($activeTab == 'tenders'){{'checked'}}@endif type="radio" value="" name="radio" id="radio5" data-form="tenders">
                            <label for="radio5">{{ t('customers.tenders_tab') }}</label>
                        </div>
                        <div class="form-holder radio">
                            <input @if($activeTab == 'ngo'){{'checked'}}@endif type="radio" value="" name="radio" id="radio1" data-form="ngo">
                            <label for="radio1">{{t('customers.ngo_tab')}}</label>
                        </div>
                    </div>
                    <br>
                </form>
            </div>
            <br>
            <div id="for-spinner"></div>
            <div id="community-content">
                @include('partials._tenders_table', ['showReactionFilter'=>true])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
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
            , left: '50%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'relative' // Element positioning
        }

        var target = document.getElementById('for-spinner');
        var spinner = new Spinner(opts).spin(target);

        $('.spinner').hide();

        var url = '{{ route('page.customers', ['edrpou' => $edrpou]) }}';
        var activeTab = '{{$activeTab}}';

        $('#community-content').on('click', '.link_pagination', function() {
            var target1 = document.getElementById('for-spinner1');
            var spinner1 = new Spinner(opts).spin(target1);
            var page = parseInt($(this).attr('data-current-page'));
            var last_page = parseInt($(this).attr('data-last-page'));
            var page = page + 1;

            var reaction = $('input[name="reaction"]').is(':checked');
            reaction = reaction == true ? 1 : 0;

            $.get(url+'/'+activeTab+'?ajax=1&page=' + page + '&' + $('#form_tenders_search').serialize()+'&reaction='+reaction,
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

        $('[data-form]').on('click', function() {
            $('#for-spinner .spinner').show();
            activeTab = $(this).data('form');
            $('#form_tenders_search').attr('action', url+'/'+activeTab);
            $('.reset_filters').attr('href', url+'/'+activeTab);

            var reaction = $('input[name="reaction"]').is(':checked');
            reaction = reaction == true ? 1 : 0;

            $("#community-content").load((url+'/'+activeTab+'?ajax=1&' + $('#form_tenders_search').serialize()+'&reaction='+reaction), function( response, status, xhr ) {
                History.pushState({state: 1}, 'customers', url+'/'+activeTab+'?'+$('#form_tenders_search').serialize()+'&reaction='+reaction);
                $('.spinner').hide();
            });
        });

        $('#form_tenders_search').on('submit', function() {
            $('#for-spinner .spinner').show();

            var page = $('#community-content').find('.link_pagination').attr('data-current-page');
            var reaction = $('input[name="reaction"]').is(':checked');
            reaction = reaction == true ? 1 : 0;

            $("#community-content").load((url+'/'+activeTab+'?ajax=1&page=' + page + '&' + $('#form_tenders_search').serialize()+'&reaction='+reaction), function( response, status, xhr ) {
                History.pushState({state: 1}, 'customers', url+'/'+activeTab+'?'+$('#form_tenders_search').serialize()+'&reaction='+reaction);
                $('.spinner').hide();
            });

            return false;
        });

        $('[data-form="'+activeTab+'"]').trigger('click');

        $('#community-content').on('click', 'input[name="reaction"]', function() {
            $('#form_tenders_search').submit();
         });
    });
</script>
@endpush