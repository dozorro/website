<div class="feedback-overlay">
</div>
<div class="feedback-form-container">
    <div id="feedback-form">
        @if(!starts_with(\Route::currentRouteName(), 'page.indicators') && !starts_with(\Route::currentRouteName(), 'page.search'))
        <div class="img-holder">
            <img src="/assets/images/icon/icon-statistic3.svg">
        </div>
        @endif
        @if(!$user)
        <form class="filter_tender" id="feedback-spinner">
            <div class="feedback_close"></div>
            <h4>{{ t('feedback.title') }}</h4>
            <p>{{ t('feedback.description') }}</p>
            <div class="row">
                <div class="add-review-form__content">
                    <div>
                        <a class="btn btn-block btn-social btn-facebook" href="/auth/facebook?feedback">
                            <span class="fa fa-facebook"></span> {{t('tender.login.facebook')}}
                        </a>
                        <a class="btn btn-block btn-social btn-google" href="/auth/google?feedback">
                            <span class="fa fa-google"></span> {{t('tender.login.google')}}
                        </a>
                    </div>
                </div>
            </div>
        </form>
        @else
            <form action="" method="post" class="filter_tender" id="feedback-spinner">
                <div class="feedback_close"></div>
                <h4>{{ t('feedback.title') }}</h4>
                <p>{{ t('feedback.description') }}</p>
                <div class="row">
                    <div class="form-group">
                        <input required class="form-control" type="text" value="" name="subject" placeholder="{{ t('feedback.subject') }}"/>
                    </div>
                    <div class="form-group">
                        <select required id="feedback_type" name="type">
                            @foreach($feedbackTypes as $type)
                                <option value="{{ $type['feedback_type'] }}">{{ $type['feedback_type_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea required name="text" placeholder="{{ t('feedback.text') }}"></textarea>
                    </div>
                    <div class="form-group inline-layout">
                        <button type="submit">{{ t('feedback.submit') }}</button>
                    </div>
                </div>
            </form>
            <div class="filter_tender row show-feedback-row" style="display:none;">
                <div class="form-group inline-layout">
                    <button class="show-feedback">{{ t('feedback.show') }}</button>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    //var detector = new MobileDetect(window.navigator.userAgent);

    //if(!detector.mobile() || (detector.mobile() && detector.is('iPhone'))) {
        $('#feedback-form').parent().show();
    //}

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
        , color: 'red' // #rgb or #rrggbb or array of colors
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
        , position: 'absolute' // Element positioning
    }
    var target = document.getElementById('feedback-spinner');
    var spinner = new Spinner(opts).spin(target);

    $('#feedback-spinner .spinner').hide();

    $(function() {
        if(window.location.hash == '#feedback') {
            $('#feedback-form .img-holder').trigger('click');
        }
    });

    $('#feedback-form').on('submit', 'form', function(e) {
        e.preventDefault();

        var th = $(this);

        $('#feedback-spinner .spinner').show();
        th.find('button').attr('disabled', 'disabled');

        var form = $('#feedback-form form').serializeArray();
        form.push({name: 'page', value: window.location.href});

        $.ajax({
            method: 'POST',
            url: '/feedback/save',
            data: form,
            dataType: 'json',
            error: function(reposnse) {
                //grecaptcha.reset();
            },
            complete: function (response) {
                $('#feedback-spinner .spinner').hide();
                th.find('button').removeAttr('disabled');
                window.History.pushState(null, document.title, window.location.href.replace('#feedback', ''));
            },
            success: function (response) {
                $('#feedback-form h4').html('{{ t('feedback.thank_you') }}');
                $('#feedback-form .row').hide();
                $('.show-feedback-row').show();
                $('#feedback-form').find('input, textarea').val('');
            }
        });
    });

    $('#feedback-form .show-feedback').on('click', function() {
        $('#feedback-form .row').show();
        $('#feedback-form .show-feedback-row').hide();
    });

    $('#feedback-form .feedback_close, .feedback-overlay').on('click', function() {
        $('.feedback-overlay, .feedback-form-container').removeClass('active');
    });

    $('#feedback-form .img-holder').on('click', function() {
        $('.feedback-overlay, .feedback-form-container').addClass('active');
    });

    var isAndroid = navigator.userAgent.toLowerCase().indexOf("android") > -1;
    if(isAndroid) {
        $('#feedback-form').addClass('android');
    }
</script>
@endpush