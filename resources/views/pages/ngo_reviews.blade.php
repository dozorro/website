@extends('layouts.app')

@section('content')

<div class="c-b">
    <div class="container">
        <div class="filter_go">
            <h4 class="js-filter-go">{{t('ngo.reviews.title')}}</h4>
            <h4 class="js-filter-go mobile">{{t('ngo.reviews.title')}}</h4>
            <form>
                <div class="list_radio inline-layout">
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="new" data-form="new" @if($formType == 'new'){{'checked'}}@endif >
                        <label for="new">{{ t('ngo.reviews.tab_new') }}</label>
                    </div>
                    <div class="form-holder radio">
                        <input type="radio" value="" name="radio" id="complete" data-form="complete" @if($formType == 'complete'){{'checked'}}@endif >
                        <label for="complete">{{t('ngo.reviews.tab_complete')}}</label>
                    </div>
                </div>
            </form>
            <br>
            <form style="display: none;" id="submit-form" action="{{ route('ngo.reviews.submit') }}" method="post">
                {{ csrf_field()  }}
                <input name="id" value="" type="hidden">
                <select name="status" required>
                    @foreach($statuses as $id => $status)
                        <option value="{{ $id }}">{{ $status }}</option>
                    @endforeach
                </select>
                <br><br>
                <textarea name="comment" required style="width: 400px;height: 100px;"></textarea>
                <br><br>
                <button class="link_back" type="submit">{{ t('ngo.reviews.submit') }}</button>
                <button class="link_back cancel" type="submit">{{ t('ngo.reviews.cancel') }}</button>
            </form>
        </div>

        <div class="list_tender_company" id="for-spinner2">
            <div class="overflow-table">
                <table>
                    <thead>
                        <tr>
                            <th width="35%">{{t('ngo.reviews.text')}}</th>
                            <th width="20%">{{t('ngo.reviews.profile')}}</th>
                            <th width="10%">{{t('ngo.reviews.tender_id')}}</th>
                            <th width="10%">{{t('ngo.reviews.status')}}</th>
                            <th width="10%">{{t('ngo.reviews.dt')}}</th>
                            <th width="10%">{{t('ngo.reviews.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="list_tender_company mobile">
            <table>
                <thead>
                    <tr>
                        <th width="25%">{{t('ngo.reviews.text')}}</th>
                        <th width="20%">{{t('ngo.reviews.profile')}}</th>
                        <th width="10%">{{t('ngo.reviews.tender_id')}}</th>
                        <th width="10%">{{t('ngo.reviews.status')}}</th>
                        <th width="10%">{{t('ngo.reviews.dt')}}</th>
                        <th width="10%">{{t('ngo.reviews.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div no-reviews style="text-align: center;">{{t('ngo.reviews.ngo.no_results')}}</div>
        <div id="for-spinner1" class="link_pagination none" data-current-page="" data-last-page="">{{ t('ngo.show_more') }}</div>

    </div>
</div>

@endsection

@push('scripts')
<script>

    var route = '{{ route('ngo.reviews') }}';
    var defaultForm = '{{$formType}}';

    $(function () {

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('.link_back.cancel').on('click', function() {
            $(this).closest('form').hide();
            return false;
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
        loadData();

        $('.overflow-table').on('click', '.submit-review', function() {
            $('#submit-form input[name="id"]').val($(this).data('id'));
            $("#submit-form").show();
            return false;
        });

        $('.link_pagination').on('click', function() {
            var pagination = $(this);
            var page = parseInt($(this).attr('data-current-page'));
            var last_page = parseInt($(this).attr('data-last-page'));
            var page = page + 1;

            $('#for-spinner1 .spinner').show();

            $.post(route + '/' + defaultForm+'?page=' + page,
                function(data, textStatus, xhr)
                {
                    $('.overflow-table tbody').append(data['desktop']);
                    $('.list_tender_company.mobile tbody').append(data['mobile']);

                    pagination.attr('data-current-page', page);

                    if(page >= last_page) {
                        pagination.hide();
                    }

                    $('#for-spinner1 .spinner').hide();
                });
        });

        $('[data-form]').on('click', function() {

            defaultForm = $(this).data('form');

            if(defaultForm == 'new') {
                $('.overflow-table th:last-child').show();
            } else {
                $('#submit-form').hide();
                $('.overflow-table th:last-child').hide();
            }

            loadData();

            return true;
        });
    });

    function loadData() {

        $('[no-reviews]').hide();

        $('#for-spinner2 .spinner').show();
        window.History.pushState(null, document.title, route + '/' + defaultForm);

        $.post(route + '/' + defaultForm,
                function(data, textStatus, xhr)
                {
                    $('.overflow-table tbody').find('tr').remove();
                    $('.overflow-table tbody').append(data['desktop']);
                    $('.list_tender_company.mobile tbody').find('tr').remove();
                    $('.list_tender_company.mobile tbody').append(data['mobile']);
                    $('.link_pagination').attr('data-current-page', 1);
                    $('.link_pagination').attr('data-last-page', data['lastPage']);

                    if(data['lastPage'] <= 1) {
                        $('.link_pagination').hide();
                    } else {
                        $('.link_pagination').show();
                    }

                    $('#for-spinner2 .spinner').hide();

                    if(!data['lastPage']) {
                        $('[no-reviews]').show();
                    }
                });

        return true;
    }

</script>
@endpush