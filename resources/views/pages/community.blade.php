@extends('layouts.app')

@section('content')
    <div class="c-b">
        <div class="container">

            <div class="filter_go">
                <h4 class="js-filter-go">{{t('tenders.ngo_customers_title')}}</h4>
                <h4 class="js-filter-go mobile">{{t('tenders.ngo.title')}}</h4>
                <form>
                    <div class="list_radio inline-layout">
                        <div class="form-holder radio">
                            <input @if($activeTab == 'ngo'){{'checked'}}@endif type="radio" value="" name="radio" id="radio1" data-form="ngo">
                            <label for="radio1">{{t('tenders.ngo_tab')}}</label>
                        </div>
                        <div class="form-holder radio">
                            <input @if($activeTab == 'customers'){{'checked'}}@endif type="radio" value="" name="radio" id="radio5" data-form="customers">
                            <label for="radio5">{{ t('tenders.customers_tab') }}</label>
                        </div>
                        @if($user && isset($user->monitoring) && $user->monitoring && $user->access_read)
                            <div class="form-holder radio">
                                <input @if($activeTab == 'monitoring'){{'checked'}}@endif type="radio" value="" name="radio" id="monitoring-tab" data-form="monitoring">
                                <label for="monitoring-tab">{{ t('tenders.monitoring_tab') }}</label>
                            </div>
                        @endif
                    </div>
                    <br>
                </form>
            </div>
            <br>
            <div id="for-spinner"></div>
            <div id="community-content" data-js="ngo_header_reload">
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

        var target = document.getElementById('for-spinner');
        var spinner = new Spinner(opts).spin(target);

        $('.spinner').hide();

        var url = '{{ $currentLocale == $defaultLocale ? '' : '/'.$currentLocale }}' + '/community';
        var activeTab = '{{$activeTab}}';

        $('#community-content').on('click', '.link_pagination', function() {
            var target1 = document.getElementById('for-spinner1');
            var spinner1 = new Spinner(opts).spin(target1);
            var page = $(this).data('current-page')+1;

            $(this).attr('data-current-page', page);

            $("#community-content").append(
                $("<div>").load((url+'/'+activeTab+'?ajax=1&page='+page), function( response, status, xhr ) {
                    History.pushState({state: 1}, 'community', url+'/'+activeTab);
                    $("#community-content").find('.link_pagination:first').remove();
                    $('#community-content').find('.datepicker').datepicker({
                        language: 'ua'
                    });
                    pieChart();
                })
            );
        });

        $('[data-form]').on('click', function() {
            $('#for-spinner .spinner').show();
            activeTab = $(this).data('form');

            $("#community-content").load((url+'/'+activeTab+'?ajax=1'), function( response, status, xhr ) {
                History.pushState({state: 1}, 'community', url+'/'+activeTab);
                $('.spinner').hide();
                $('#community-content').find('.datepicker').datepicker({
                    language: 'ua'
                });
                pieChart();
            });
        });

        $('[data-form="'+activeTab+'"]').trigger('click');
    });
</script>
@endpush