/*!
 *
 * Prozorro v1.0.0
 *
 * Author: Lanko Andrey (lanko@perevorot.com)
 *
 * © 2015
 *
 */

var APP,
    INPUT,
    SEARCH_BUTTON,
    BLOCKS,
    INITED=false,
    LANG,
    HASH,
    SEARCH_TYPE,
    SEARCH_QUERY=[],
    SEARCH_QUERY_TIMEOUT,

    IS_MAC = /Mac/.test(navigator.userAgent),
    IS_HISTORY = (window.History ? window.History.enabled : false),

    KEY_BACKSPACE = 8,
    KEY_UP = 38,
    KEY_DOWN = 40,
    KEY_ESC = 27,
    KEY_RETURN = 13,
    KEY_CMD = IS_MAC ? 91 : 17,

    PREVIOUS_FILTER,
    PREVIOUS_PAIR,

    spin_options={
        color:'#e55166',
        lines: 15,
        width: 2
    },

    spin_options_light={
        color:'#fff',
        lines: 15,
        width: 2
    };

(function(window, undefined){

    'use strict';

    var suggest_opened,
        suggest_current;

    APP = (function(){

        var viewport = function () {
            var e = window, a = 'inner';
            if (!('innerWidth' in window )) {
                a = 'client';
                e = document.documentElement || document.body;
            }
            return {width: e[a + 'Width'], height: e[a + 'Height']};
        };

        var methods =  {
            common: function(){
                $('html').removeClass('no-js');

                $('a.registration').bind('click', function(event){
                    event.preventDefault();
                    $('.startpopup').css('display', 'block');
                });

                $('.close-startpopup').bind('click', function(event){
                    event.preventDefault();
                    $('.startpopup').css('display', 'none');
                });

                $('a.document-link2').click(function(e){
                    e.preventDefault();

                    $(this).closest('.margin-bottom-more').find('.tender--offers.documents').hide();
                    $(this).closest('.margin-bottom-more').find('.tender--offers.documents[data-id='+$(this).data('id')+']').show();

                    $(this).closest('.margin-bottom-more').find('.overlay-documents').addClass('open');
                });

                $('a.document-link').click(function(e){
                    e.preventDefault();

                    $(this).closest('.margin-bottom-more').find('.tender--offers.documents').hide();
                    $(this).closest('.margin-bottom-more').find('.tender--offers.documents[data-id='+$(this).data('id')+']').show();

                    $(this).closest('.margin-bottom-more').find('.overlay-documents').addClass('open');
                });

                $('.overlay-close').click(function(e){
                    e.preventDefault();

                    $('.overlay').removeClass('open');
                    $('.overlay2').removeClass('open');
                });

                $(document).keydown(function(e){
                    if($('.overlay').is('.open')){
                        switch (e.keyCode){
                            case KEY_ESC:
                                $('.overlay-close').click();
                                return;
                            break;
                        }
                    }
                    if($('.overlay2').is('.open')){
                        switch (e.keyCode){
                            case KEY_ESC:
                                $('.overlay-close').click();
                                return;
                                break;
                        }
                    }
                });

                $('.jsTenderTabs .tender-tabs__item').click(function() {
                    var index=$(this).index('.tender-tabs__item');

                    $('[tab-content]').hide();
                    $('[tab-content]').eq(index).show();
                    $('.jsTenderTabs .tender-tabs__item').removeClass('is-show');

                    $(this).addClass('is-show');

                    window.location.hash=$(this).data('hash');
                });

                HASH=window.location.hash.substring(1).split('-');

                if(HASH[0] && HASH[0]!=''){
                    $('.jsTenderTabs .tender-tabs__item[data-hash="'+HASH[0]+'"]').click();
                }else{
                    if(parseInt($('[data-reviews-count]').data('reviews-count')) === 0) {
                        $('[data-hash="tender"]').click();
                    }
                }

                $('.tender-header__link').click(function( event ) {
                    event.preventDefault();

                    $('.add-review-form').popup({
                        transition: 'all 0.3s'
                    });
                });

                $('.jsGetInputVal').change(function() {

                	if($(this).val().length >= 1) {
                		$(this).addClass('with-text');
                	} else {
                		$(this).removeClass('with-text');
                	}
                });

                $(document).ready(function(){
                    //$(".tender-header__review-button").sticky({topSpacing:20});
                    //$(".tender-tabs-wrapper").sticky({topSpacing:0});
                });
            },
            js: {
                openRisksTab: function(_self) {
                    _self.on('click', function(e) {
                        e.preventDefault();
                        $('[data-hash="risk_tender"]').click();
                        $('html, body').animate({
                             scrollTop: $('[data-hash="risk_tender"]').offset().top
                        }, 750);
                    });
                },
                pieChart: function(_self){
                    var datasets = _self.data('datasets');
                    var _datasets = [];

                    console.log(datasets);

                    _datasets.push({
                        //label: _item.label,
                        backgroundColor: _self.data('colors'),
                        data: _self.data('values'),
                    });
                    
                    var data = {
                        datasets: _datasets,
                        labels: _self.data('labels')
                    };

                    console.log(data);

                    var myPieChart = new Chart(_self,{
                        type: 'pie',
                        data: data,
                        options: {
                            responsive: true,
                            legend: {
                                display: false
                            }
                        }
                    });
                },
                barChart: function(_self){
                    var datasets = _self.data('datasets');
                    var _datasets = [];
                    var axes = [];

                    console.log(datasets);

                    for(var item in datasets) {
                        var _item = datasets[item];

                        _datasets.push({
                            label: _item.label,
                            backgroundColor: _item.color,
                            data: _item.array,
                            type: _item.chartType,
                            yAxisID: _item.chartType+item,
                            fill: _item.chartType == 'line' ? false : true,
                            borderWidth: 2,
                            borderColor: _item.color,
                        });

                        axes.push({
                            id: _item.chartType+item,
                            type: 'linear',
                            position: _item.axis,
                            display: _item.axis == 'left' ? true : false,
                            stacked: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return value.toLocaleString();
                                },
                                min: _item.min,
                                max: _item.max
                            },
                        });
                    }

                    var data = {
                        datasets: _datasets,
                        labels: _self.data('labels')
                    };

                    console.log(data);

                    //nvar ctx = _self;
                    var myPieChart = new Chart(_self,{
                        type: 'bar',
                        data: data,
                        options: {
                            responsive: true,
                            scales: {
                                yAxes: axes
                            },
                            tooltips: {
                                enabled: true,
                                mode: 'single',
                                callbacks: {
                                    label: function(tooltipItems, data) {
                                        return data.datasets[tooltipItems.datasetIndex].label+": "+tooltipItems.yLabel.toLocaleString();
                                    }
                                }
                            }
                        }
                    });
                },
                // sidebar: function(_self) {
                //     _self.on('click', 'tr:not(.selected)', function(e) {
                //         e.preventDefault();
                //         $.ajaxSetup({
                //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                //         });
                //         $(this).addClass("selected").siblings().removeClass("selected");

                //         var opt = {
                //             lines: 15, length: 30, width: 3, radius: 25, scale: 1, corners: 1, color: '#e55166', fadeColor: 'transparent', opacity: 0.25, rotate: 0, direction: 1, speed: 1, trail: 60, fps: 20, zIndex: 2e9, className: 'spinner', top: '50%', left: '50%', position: 'absolute'
                //         };
                //         var tenderId = $(this).data('tenderId');

                //         //_self.spin(opt);
                //         //$('#tender-content').spin(opt);

                //         $.post('/api/tenders_sidebar', {id: $(this).data('id')},
                //             function (resp, textStatus, xhr) {
                //                 //console.log(resp);
                //                 $('#tender-content').html(resp);
                //                 $('#tender-content').find('.tender-header__descr-title.risks-title').each(function() {
                //                     $(this).parent().find('.risks-title span').text( $(this).parent().find('.risk-values .risk-item').length );
                //                 });
                //                 $('#tender-content').show();
                //                 //_self.spin(false);
                //                 $('html, body').animate({
                //                     scrollTop: $('#tender-content').offset().top
                //                 }, 1000);

                //                 var md = new MobileDetect(window.navigator.userAgent);

                //                 if(md.mobile() || md.tablet()) {
                //                     $('#community-content').hide();
                //                 }

                //                 window.History.pushState(null, document.title, '/indicators/' + tenderId);
                //             }
                //         );
                //     });
                // },
                pairs: function(_self){
                    var previousPair = '';
                    var cancelDiv=_self.find('[pair-cancel]');
                    var stateDiv=_self.find('[pairs-current-state]');
                    var sliderDiv=_self.find('[pair-slider]');
                    var panelDiv=_self.find('[pair-panel]');
                    var skipDiv=_self.find('[pair-skip]');
                    var doneDiv=$('#pair-done');
                    var current=0;
                    var whenUpdate=3;
                    var total=_self.data('total');
                    var dt=_self.data('dt');
                    var slides=_self.find('li');
                    var route = '/pairs/update/';

                    var opt = {
                        lines: 13, length: 7, width: 2, radius: 10, scale: 1, corners: 1, color: '#ffffff', fadeColor: 'transparent', opacity: 0.25, rotate: 0, direction: 1, speed: 1, trail: 60, fps: 20, zIndex: 2e9, className: 'spinner', top: '350px', left: '50%', position: 'absolute'
                    };

                    _self.on('click', '.pair-answer.active2', function(e) {
                        e.preventDefault();

                        //var url = $(this).attr('data-href');
                        var answer = 'yes';
                        //var favorite = 0;
                        //var comment = sliderDiv.find('#pair-comment textarea').val();

                        if(sliderDiv.find('.tender_last').hasClass('left-tender')) {
                            answer = 'no';
                        }
                        /*if(sliderDiv.find('.pair-favorite').hasClass('active')) {
                            favorite = 1;
                        }

                        url = url+answer+'&favorite='+favorite;

                        if (comment && favorite == 1) {
                            url = url + '&comment=' + comment;
                        }*/

                        var slide=slides.eq(current);
                        var id=slide.data('id');

                        slide.fadeOut();

                        post(id, answer, function(){
                            current++;
                            next();
                        });

                        //window.location.href = url;
                        return false;
                    });

                    _self.on('click', '.pair-answer.none-active', function(e) {
                        e.preventDefault();
                        return false;
                    });

                    _self.on('click', '.pair-block .item', function(e) {
                        e.preventDefault();

                        sliderDiv.find('.pair-block .item').removeClass('tender_last');
                        sliderDiv.find('.pair-block .item').removeClass('tender_sep');
                        sliderDiv.find('.pair-answer').removeClass('none-active');
                        sliderDiv.find('.pair-answer').addClass('active2');
                        sliderDiv.find('.pair-block .item strong').removeClass('opacity-left').removeClass('opacity0');

                        $(this).addClass('tender_last');

                        var answer = '';

                        if($(this).hasClass('left-tender')) {
                            answer='no';
                            $(this).find('strong').addClass('opacity-left');
                            sliderDiv.find('.pair-block .right-tender strong').css('margin-right', '0');
                            sliderDiv.find('.pair-block .right-tender strong').css('margin-left', '-100%');
                        } else {
                            answer='yes';
                            sliderDiv.find('.pair-block .right-tender strong').attr('style', '');
                            $(this).find('strong').addClass('opacity0');
                        }
                    });
                    
                    cancelDiv.click(function(e) {
                        e.preventDefault();

                        window.location.href = PREVIOUS_PAIR;
                        return false;

                        /*
                        if(slides.eq(current).data('prev')) {
                            window.location.href = '/pairs/' + slides.eq(current).data('prev') + '/prev';
                        }*/
                    });

                    _self.on('click', '.pair-favorite', function(e) {
                        e.preventDefault();
                        var th = $(this);

                        if(th.hasClass('none-active')) {
                            th.removeClass('none-active');
                            th.addClass('active');
                            sliderDiv.find('#pair-comment').show();
                        } else {
                            th.addClass('none-active');
                            th.removeClass('active');
                            sliderDiv.find('#pair-comment').hide();
                        }

                        sliderDiv.trigger('swipe');

                        /*slides.eq(current).find('.pairs_button:not(.pair-favorite)').each(function() {
                           $(this).attr('href', $(this).data('href')+'&favorite='+(th.hasClass('none-active') ? 0 : 1));
                        });*/
                    });

                    sliderDiv.each(function(){
                        var swipe = !$(this).data('swipe') ? false : true;

                        $(this).slick({
                            dots: false,
                            prevArrow: '',
                            nextArrow: '',
                            infinite: false,
                            initialSlide: 1,
                            draggable: swipe,
                            swipe: swipe,
                            swipeToSlide: swipe,
                        });
                    });

                    skipDiv.click(function(e){
                        e.preventDefault();

                        var answer='skip';
                        var slide=slides.eq(current);
                        var id=slide.data('id');

                        slide.fadeOut();

                        post(id, answer, function(){
                            current++;
                            next();
                        });
                    })

                    function next() {
                        if(current==whenUpdate){
                           // alert('getNext');
                        }
                        if(current==total){
                            setTimeout(function(){
                                doneDiv.show();
                                _self.hide();
                            }, 700);
                        }else{
                            stateDiv.html(slides.eq(current).data('id'));
                            slides.fadeTo(0, .001);
                            slides.css({
                                'pointer-events': 'none'
                            });

                            slides.eq(current).fadeTo('normal', 1).css({
                                'pointer-events': 'all'
                            });

                            panelDiv.fadeTo('normal', 1);
                        }

                        $('html, body').animate({
                            scrollTop: stateDiv.offset().top
                        }, 1000);

                        var prev = window.location.href.indexOf('/prev') > -1 ? '/prev' : '';
                        var info = window.location.href.indexOf('/info') > -1 ? '/info' : '';

                        window.History.pushState(null, document.title, '/pairs/' + slides.eq(current).data('id') + prev);

                        if(!info) {
                            $('.indicator-page').hide();
                        }
                    }

                    function post(id, answer, callback){
                        $.ajaxSetup({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                        });

                        previousPair = id;
                        _self.spin(opt);

                        var comment = '';
                        var favorite = slides.eq(current).find('.pair-favorite').hasClass('active') ? 1 : 0;

                        if(favorite == 1) {
                            comment = slides.eq(current).find('#pair-comment textarea').val();
                        }

                        PREVIOUS_PAIR = id;

                        $.ajax({
                            method: 'POST',
                            url: route+id,
                            data: {
                                comment: comment,
                                answer: answer,
                                favorite: favorite,
                                dt: dt,
                            },
                            dataType: 'json',
                            success: function(resp){

                                if(resp.html) {
                                    $('.block_pairs ul').append(resp.html);

                                    sliderDiv = _self.find('[pair-slider]');
                                    sliderDiv.each(function () {
                                        var swipe = !$(this).data('swipe') ? false : true;
                                        
                                        if (!$(this).hasClass('slick-slider')) {
                                            $(this).slick({
                                                dots: false,
                                                prevArrow: '',
                                                nextArrow: '',
                                                infinite: false,
                                                initialSlide: 1,
                                                draggable: swipe,
                                                swipe: swipe,
                                                swipeToSlide: swipe,
                                            });

                                            $(this).on('swipe', function(event, slick, currentSlide){
                                                var answer=slick.slickCurrentSlide()===0 ? 'no' : 'yes';
                                                var slide=slides.eq(current);
                                                var id=slide.data('id');

                                                slide.fadeOut();

                                                post(id, answer, function(){
                                                    current++;
                                                    next();
                                                });
                                            });
                                        }
                                    });

                                    slides=_self.find('li');
                                }

                                _self.spin(false);

                                callback();

                                //if(slides.eq(current).data('prev')) {
                                    panelDiv.find('.block_number').removeClass('col-2').addClass('col-3');
                                    stateDiv.css({'text-align': 'center'});
                                    cancelDiv.parent().show();
                                //}
                            }
                        });
                    }

                    next();

                    sliderDiv.on('swipe', function(event, slick, currentSlide){
                        var answer=slick.slickCurrentSlide()===0 ? 'no' : 'yes';
                        var slide=slides.eq(current);
                        var id=slide.data('id');

                        slide.fadeOut();

                        post(id, answer, function(){
                            current++;
                            next();
                        });
                    });
                },
                ngo_header_reload: function (_self) {
                    _self.on('click', '.ngo-header-submit', function(e) {
                        e.preventDefault();

                        var th = $(this);
                        var parent = th.closest('.block_info_go_analytics_parent');
                        var _LANG=$('html').attr('lang') == $('html').attr('default-lang') ? '' : '/'+$('html').attr('lang');

                        var opt = {
                            lines: 13 // The number of lines to draw
                            , length: 28 // The length of each line
                            , width: 4 // The line thickness
                            , radius: 3 // The radius of the inner circle
                            , scale: 1 // Scales overall size of the spinner
                            , corners: 1 // Corner roundness (0..1)
                            , color:'#e55166'// #rgb or #rrggbb or array of colors
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
                        };

                        parent.addClass('loading').spin(opt);

                        $.ajax({
                            url: _LANG+'/ajax/ngo',
                            data: {
                                id: th.data('id'),
                                date_from: th.closest('.block_info_go_filter').find('input[name="date_from"]').val(),
                                date_to: th.closest('.block_info_go_filter').find('input[name="date_to"]').val(),
                            },
                            method: 'post',
                            headers: APP.utils.csrf(),
                            dataType: "json",
                            success: function(response){
                                if(response.html){
                                    parent.find('.block_info_go_analytics').html(response.html);
                                }

                                parent.removeClass('loading');
                                parent.find('.spinner').remove();
                                pieChart()
                            }
                        });
                    });
                },
                monitoring_measures: function (_self) {
                    var _LANG=$('html').attr('lang') == $('html').attr('default-lang') ? '' : '/'+$('html').attr('lang');
                    _self.selectize({
                        valueField: 'value',
                        labelField: 'value',
                        searchField: ['value', 'key'],
                        create: true,
                        maxItems: 1,
                        render: {
                            option: function(item, escape) {
                                return '<div>' + escape(item.value) + '</div>';
                            }
                        },
                        load: function(query, callback) {
                            $.ajax({
                                url: _LANG+'/monitoring/measure/search?query=' + encodeURIComponent(query),
                                type: 'GET',
                                error: function() {
                                    callback();
                                },
                                success: function(res) {
                                    callback(res);
                                }
                            });
                        },

                        onDelete: function () {
                            _self.find('.selectize-control').find('.selectize-dropdown').hide();
                        }
                    });
                },
                monitoring_forms: function (_self) {
                    var _LANG=$('html').attr('lang') == $('html').attr('default-lang') ? '' : '/'+$('html').attr('lang');
                    _self.selectize({
                        valueField: 'value',
                        labelField: 'value',
                        searchField: ['value', 'key'],
                        create: false,
                        maxItems: 1,
                        render: {
                            option: function(item, escape) {
                                return '<div>' + escape(item.value) + '</div>';
                            }
                        },
                        load: function(query, callback) {

                            var name = _self.closest('tr').find('#monitoring-names').val();

                            if(!name) {
                                return false;
                            }

                            $.ajax({
                                url: _LANG+'/monitoring/forms/search/products/?query=' + encodeURIComponent(query)+'&name='+name,
                                type: 'GET',
                                error: function() {
                                    callback();
                                },
                                success: function(res) {
                                    callback(res);
                                }
                            });
                        },

                        onDelete: function () {
                            _self.find('.selectize-control').find('.selectize-dropdown').hide();
                        }
                    });
                },
                monitoring_names: function (_self) {
                    var _LANG=$('html').attr('lang') == $('html').attr('default-lang') ? '' : '/'+$('html').attr('lang');
                    _self.selectize({
                        create: false,
                        valueField: 'value',
                        labelField: 'value',
                        searchField: ['value', 'key'],
                        maxItems: 1,
                        render: {
                            option: function(item, escape) {
                                return '<div>' + escape(item.value) + '</div>';
                            }
                        },
                        load: function(query, callback) {
                            $.ajax({
                                url: _LANG+'/monitoring/names/search/products/?query=' + encodeURIComponent(query),
                                type: 'GET',
                                error: function() {
                                    callback();
                                },
                                success: function(res) {
                                    callback(res);
                                }
                            });
                        },

                        onDelete: function () {
                            _self.find('.selectize-control').find('.selectize-dropdown').hide();
                        }
                    });
                },
                customer_search: function () {
                    var _LANG=$('html').attr('lang') == $('html').attr('default-lang') ? '' : '/'+$('html').attr('lang');
                    $('#tender-customer').selectize({
                        valueField: 'key',
                        labelField: 'value',
                        searchField: ['value', 'key'],
                        create: false,
                        maxItems: 1,
                        render: {
                            option: function(item, escape) {
                                return '<div>' + escape(item.value) + '</div>';
                            },

                        },
                        load: function(query, callback) {
                            $.ajax({
                                url: _LANG+'/customers_search/?query=' + encodeURIComponent(query),
                                type: 'GET',
                                error: function() {
                                    callback();
                                },
                                success: function(res) {
                                    callback(res);
                                }
                            });
                        },

                        onDelete: function () {
                            $('.selectize-dropdown').hide();
                            //console.log(values);
                        },

                        onChange: function(value){
                            console.log(value);
                        }

                    });
                },
                disableSearchButton: function (_self) {
                    $('#c-find-form').validate({
                        messages: {
                            tid: 'Формат: UA-2016-01-01-000001'
                        },
                        errorPlacement: function(error, element) {
                            error.appendTo('#errordiv');
                        },
                        rules: {
                            tid: {
                                required: false,
                                pattern: /^UA\-20\d{2}\-\d{2}\-\d{2}\-\d{6}(\-([a-z]))?$/
                            }
                        }
                    });

                    jQuery.extend(jQuery.validator.messages, {
                        // Add custom text message for pattern
                        pattern: "Invalid pattern format for tender number.",
                    });

                    $('input[id="btn-find"]').prop('disabled', true);
                    $('input[id="tender-number"], input[id="tender-customer"]').change(function () {
                        if ($(this).val() != '') {
                            $('input[id="btn-find"]').prop('disabled', false);
                        } else {
                            $('input[id="btn-find"]').prop('disabled', true);
                        }
                    });
                },
                imageSlider: function(_self){
                    _self.slick({
                        dots: true,
                        autoplay: _self.data('autoplay')
                    });
                },
                feedback_thanks: function(_self){
                    var send_more=_self.find('.send-more'),
                        close=_self.find('.close'),
                        opened=_self.is(':visible');

                    send_more.click(function(e){
                        e.preventDefault();
                        _self.fadeOut();

                        opened=false;
                        $('.form-button').click();
                    });

                    close.click(function(e){
                        e.preventDefault();

                        $('.form-button').fadeIn();
                        opened=false;
                        _self.fadeOut();
                    });

                    if(!$('.center-page-form').length){
                        $(document).on('keydown', function(e) {
                            if(opened){
                                switch (e.keyCode){
                                    case KEY_ESC:
                                        close.click();
                                        return;
                                    break;
                                }
                            }
                        }).click(function(e){
                            if(opened && !$(e.target).closest('.form-container').length){
                                close.click();
                            }
                        });
                    }
                },
                feedback: function(_self){
                    var button=_self.next(),
                        form=_self.find('form'),
                        close=_self.find('.close'),
                        footer=$('.navbar.footer'),
                        opened=false;

                    _self.find('[name="phone"]').inputmask({
                        "mask": "+99 (999) 999-99-99"
                    });

                    close.click(function(e){
                        e.preventDefault();

                        opened=false;
                        _self.fadeOut();
                        button.fadeIn();
                    });

                    button.click(function(e){
                        e.preventDefault();

                        opened=true;
                        form.show();
                        _self.fadeIn();
                        button.fadeOut();
                        scrollForm();
                    });

                    var toggleButton=function() {
                        if ($(this).scrollTop()+viewport().height > parseInt(footer.offset().top)-50) {
                            button.fadeOut('fast');
                        } else {
                           button.fadeIn('fast');
                        }
                    }

                    var scrollForm=function(){
                        _self[form.outerHeight()>viewport().height ? 'addClass':'removeClass']('form-scrolled');

                    }

                    $(window).resize(scrollForm);

                    //toggleButton();

                    //$(window).scroll(toggleButton);

                    if(!$('.center-page-form').length){
                        $(document).on('keydown', function(e) {
                            if(opened){
                                switch (e.keyCode){
                                    case KEY_ESC:
                                        close.click();
                                        return;
                                    break;
                                }
                            }
                        }).click(function(e){
                            if(opened && !$(e.target).closest('.form-container').length){
                                close.click();
                            }
                        });
                    }
                },
                lot_tabs: function(_self){
                    var tabs_content=$('.'+_self.data('tab-class')),
                        tabs=_self.find('a');

                    tabs.click(function(e){
                        e.preventDefault();

                        tabs_content.removeClass('active');
                        tabs_content.eq($(this).parent().index()).addClass('active');
                    });
                },
                openpopup: function(_self){
                    _self.click(function(e){
                        if(!$(e.target).is('a')){
                            e.preventDefault();

                            _self.fadeOut(function(){
                                _self.remove();
                        });
                    }
                    });
                },
                go_up_down: function(){
                    var offset = 220,
                        duration = 500,
                        goto_up=$('.back-to-top'),
                        goto_down=$('.go-down');

                    $(window).scroll(function() {
                        if ($(this).scrollTop() > offset) {
                            goto_up.fadeIn(duration);
                        } else {
                           goto_up.fadeOut(duration);
                        }
                    });

                    goto_up.click(function(event) {
                        event.preventDefault();

                        $('html, body').animate({
                            scrollTop: 0
                        }, duration);
                    });

                    var topset = $(document).height()- 2*($(window).height());

                    $(window).scroll(function() {
                        if ($(this).scrollTop() < (topset+200)) {
                            goto_down.fadeIn(duration);
                        } else {
                            goto_down.fadeOut(duration);
                        }
                    });

                    var do_action = false;

                    goto_down.click(function(event) {
                        if(do_action){
                            return;
                        }

                        do_action=true;

                        event.preventDefault();

                        $('html, body').animate({
                            scrollTop: ($(document).scrollTop() + $(window).height())
                        }, duration, function(){
                            do_action=false;
                        });
                    });
                },
                home_more: function(_self){
                    var text_height=0,
                        text=$('.description .text'),
                        check_height=function(){
                            text_height=$('.text-itself').height()+20;
                    },
                    opened=false;

                    $(window).resize(check_height);

                    check_height();

                    _self.click(function(e){
                            e.preventDefault();
                            $(this).closest('.description').toggleClass('opened');

                            text.animate({
                                height: !opened ? text_height : 0
                        }, 400);

                        opened=!opened;
                    });

                    $('.slider-list').slick({
                        dots: true,
                        arrows: true,
                        speed: 300,
                        slidesToShow: 1,
                        pauseOnDotsHover: true,
                        pauseOnHover: true,
                        autoplay: true,
                        infinite: false
                    });
                },
                home_equal_height: function(_self){
                    var max_height=0,
                        blocks=_self.find('[block]');

                    blocks.each(function(i){
                        max_height=Math.max($(this).height(), max_height);
                    });

                    blocks.height(max_height);
                },
                tender_sign_check: function(_self){
                    var loader=_self.find('.loader');

                    _self.find('a').click(function(e){
                        e.preventDefault();

                        loader.spin(spin_options);
                        $('#signPlaceholder').html('');

                        window.callbackCheckSign=function(signData, currData, diff, ownerInfo, timeInfo, obj){
                            loader.spin(false);

                            if (!signData) {
                                $('#signPlaceholder').html('Підпис відсутній');
                                return;
                            }
                            var certInfo = "Підписувач:  <b>" + ownerInfo.GetSubjCN() + "</b><br/>" +
                                    "ЦСК:  <b>" + ownerInfo.GetIssuerCN() + "</b>";

                            var timeMark;
                            if (timeInfo.IsTimeAvail()) {
                                timeMark = (timeInfo.IsTimeStamp() ?
                                                "Мітка часу: <b>" : "Час підпису: <b>") + timeInfo.GetTime().toISOString() + "</b>";
                            } else {
                                timeMark = "Час підпису відсутній";
                            }
                            var diffInfo = '<b>Підпис вірний</b><br/>';
                            if (diff) {
                                diffInfo = '<b>Підпис не вірний</b>, відмінності :' + JSON.stringify(diff) + ' <br/>';
                            }
                            $('#signPlaceholder').html(diffInfo + certInfo + timeMark);
                        }

                        window.callbackRender=function(data){
                            console.log(data);
                        }

                        opSign.init({
                            apiResourceUrl: _self.data('url'),
                            callbackCheckSign: 'callbackCheckSign',
                            callbackRender: 'callbackRender',
                            verifyOnly: true
                        });
                    });
                },
                tender_menu_fixed: function(_self){
                    var offset_element=$('.wide-table:first'),
                        offset=0;

                    if(offset_element.length)
                        offset=offset_element.offset().top-50;

                    _self.sticky({
                        topSpacing: _self.position().top-80,
                        responsiveWidth: true,
                        bottomSpacing: $(document).height()-offset+_self.find('.tender--menu').height()+70
                    });
                },
                tender: function(_self){
                    _self.on('click', '.question--open', function(e){
                        e.preventDefault();
                        var self=$(this);

                        self.closest('.questions-block').find('.none').toggle();
                        self.toggleClass('open');

                        $('html, body').animate({
                            scrollTop: self.closest('.row.questions').offset().top-50
                        }, 500);
                    });

                    _self.on('click', '.search-form--open', function(e){
                        e.preventDefault();

                        $(this).closest('.description-wr').toggleClass('open');
                    });

                    _self.find('.blue-btn').click(function(e){
                        e.preventDefault();

                        $('html, body').animate({
                            scrollTop: $('.tender--platforms').position().top
                        }, 500);
                    });

                    _self.find('.tender--offers--ancor').click(function(e){
                        e.preventDefault();

                        $('html, body').animate({
                            scrollTop: $('.tender--offers.margin-bottom-xl').position().top-30
                        }, 500);
                    });

                    $('a.documents-all').click(function(e){
                        e.preventDefault();

                        $('.overlay-documents-all').addClass('open');
                    });

                    $('a.info-all').click(function(e){
                        e.preventDefault();

                        $('.overlay-info-all').addClass('open');
                    });
                },
                // search_indicators: function(_self){
                //     _self.on('click', '.search-form--open', function(e){
                //         e.preventDefault();
                //         $(this).closest('.description-wr').toggleClass('open');
                //     });

                //     _self.on('click', '.link_pagination', function(e){
                //         e.preventDefault();

                //         $('.link_pagination').addClass('loading').spin(spin_options);

                //         $.ajax({
                //             url: LANG+'/indicators/search',
                //             data: {
                //                 query: APP.utils.get_query(),
                //                 start: $('.link_pagination').attr('data-start')
                //             },
                //             method: 'post',
                //             headers: APP.utils.csrf(),
                //             dataType: "json",
                //             success: function(response){
                //                 if(response.html){
                //                     $('#result').append(response.html);
                //                     var start = $('.link_pagination').attr('data-start');
                //                     $('.link_pagination').attr('data-start', parseInt(start)+10);
                //                     $('[no-reviews]').hide();
                //                     $('.link_pagination').show();
                //                 } else {
                //                     $('.link_pagination').hide();

                //                     if($('.link_pagination').attr('data-start') == '20') {
                //                         $('[no-reviews]').show();
                //                     }
                //                 }

                //                 $('.link_pagination').removeClass('loading').spin(false);
                //             }
                //         });
                //     });
                // },
                search_medical: function(_self){
                    _self.on('click', '.search-form--open', function(e){
                        e.preventDefault();
                        $(this).closest('.description-wr').toggleClass('open');
                    });

                    _self.on('click', '.link_pagination', function(e){
                        e.preventDefault();

                        $('.link_pagination').addClass('loading').spin(spin_options);

                        $.ajax({
                            url: LANG+'/medical_contracts/search',
                            data: {
                                query: APP.utils.get_query(),
                                page: $('.link_pagination').attr('data-page')
                            },
                            method: 'post',
                            headers: APP.utils.csrf(),
                            dataType: "json",
                            success: function(response){
                                if(response.html){
                                    $('#result').append(response.html);
                                    var start = $('.link_pagination').attr('data-page');
                                    $('.link_pagination').attr('data-page', parseInt(start)+1);
                                    $('[no-reviews]').hide();
                                    $('.link_pagination').show();
                                } else {
                                    $('.link_pagination').hide();

                                    if($('.link_pagination').attr('data-page') == '2') {
                                        $('[no-reviews]').show();
                                    }
                                }

                                $('.link_pagination').removeClass('loading').spin(false);
                            }
                        });
                    });

                    $('#tender-content').sticky({
                        topSpacing: 20,
                        zIndex: 200,
                        bottomSpacing: 397+20
                    });
                },
                me: function(_self){
                    _self.on('click', '.search-form--open', function(e){
                        e.preventDefault();
                        $(this).closest('.description-wr').toggleClass('open');
                    });

                    var ngo_f20=$('#ngo_open_multi_form_f202, #ngo_open_multi_form_f203'),
                        ngo_f201=$('#ngo_open_multi_form_f201'),
                        ngo_f20_visible=false;

                    $('.ngo-buttons-container').sticky({
                        topSpacing: 20,
                        zIndex: 200
                    });

                    var checked_tenders=function(){
                        ngo_f20_visible=false;
                        ngo_f20.addClass('disabled');
                        ngo_f201.addClass('disabled');

                        var checked=$('.ngo-checkbox:checked');

                        checked.each(function(){
                            var checkbox=$(this);

                            if(!ngo_f20_visible && checkbox.data('f201')==1) {
                                ngo_f20_visible=true;
                            }
                        });

                        if(ngo_f20_visible) {
                            ngo_f20.removeClass('disabled');
                        }

                        if(checked.length) {
                            ngo_f201.removeClass('disabled');
                        }
                    }

                    _self.on('click', '#ngo_open_multi_form_select_all', function(e){
                        e.preventDefault();

                        _self.find('input[type="checkbox"]').prop('checked', true);
                        checked_tenders();
                    });

                    _self.on('click', 'input[type="checkbox"]', function(e){
                        checked_tenders();

                        return true;
                    });

                    _self.on('click', '.show-more', function(e){
                        e.preventDefault();

                        $('.show-more').addClass('loading').spin(spin_options);
                        $('.main-result').find('#ngo_open_multi_form').hide();

                        $.ajax({
                            url: LANG+'/'+SEARCH_TYPE+'/form/search',
                            data: {
                                query: APP.utils.get_query(),
                                start: $('.show-more').data('start')
                            },
                            method: 'post',
                            headers: APP.utils.csrf(),
                            dataType: "json",
                            success: function(response){
                                $('.show-more').remove();

                                if(response.html){
                                    $('#result').append(response.html);
                                    $('.main-result').find('#ngo_open_multi_form').show();

                                    var b = $('#result').find('.show-more');
                                    $('.button-show-more').append(b.clone());
                                    b.remove();

                                    if($('.button-show-more').find('.show-more').length > 1) {
                                        $('.button-show-more').find('.show-more').eq(1).remove();
                                    }

                                    APP.utils.result_highlight(response.highlight);
                                }
                            }
                        });
                    });
                },
                search_result: function(_self){
                    _self.on('click', '.search-form--open', function(e){
                        e.preventDefault();
                        $(this).closest('.description-wr').toggleClass('open');
                    });

                    var ngo_f20=$('#ngo_open_multi_form_f202, #ngo_open_multi_form_f203'),
                        ngo_f201=$('#ngo_open_multi_form_f201'),
                        ngo_f20_visible=false;

                    $('.ngo-buttons-container').sticky({
                        topSpacing: 20,
                        zIndex: 200
                    });

                    var checked_tenders=function(){
                        ngo_f20_visible=false;
                        ngo_f20.addClass('disabled');
                        ngo_f201.addClass('disabled');

                        var checked=$('.ngo-checkbox:checked');

                        checked.each(function(){
                            var checkbox=$(this);

                            if(!ngo_f20_visible && checkbox.data('f201')==1) {
                                ngo_f20_visible=true;
                            }
                        });

                        if(ngo_f20_visible) {
                            ngo_f20.removeClass('disabled');
                        }

                        if(checked.length) {
                            ngo_f201.removeClass('disabled');
                        }
                    }

                    _self.on('click', '#ngo_open_multi_form_select_all', function(e){
                        e.preventDefault();

                        _self.find('input[type="checkbox"]').prop('checked', true);
                        checked_tenders();
                    });

                    _self.on('click', 'input[type="checkbox"]', function(e){
                        checked_tenders();

                        return true;
                    });

                    _self.on('click', '.show-more', function(e){
                        e.preventDefault();

                        $('.show-more').addClass('loading').spin(spin_options);
                        $('.main-result').find('#ngo_open_multi_form').hide();

                        $.ajax({
                            url: LANG+'/'+SEARCH_TYPE+'/form/search',
                            data: {
                                query: APP.utils.get_query(),
                                start: $('.show-more').data('start')
                            },
                            method: 'post',
                            headers: APP.utils.csrf(),
                            dataType: "json",
                            success: function(response){
                                $('.show-more').remove();

                                if(response.html){
                                    $('#result').append(response.html);
                                    $('.main-result').find('#ngo_open_multi_form').show();

                                    var b = $('#result').find('.show-more');
                                    $('.button-show-more').append(b.clone());
                                    b.remove();

                                    if($('.button-show-more').find('.show-more').length > 1) {
                                        $('.button-show-more').find('.show-more').eq(1).remove();
                                    }

                                    APP.utils.result_highlight(response.highlight);
                                }
                            }
                        });
                    });
                },
                form: function(_self){
                    var timeout,
                        input_query='',
                        $document=$(document);

                    APP.utils.totals.init();

                    LANG=$('html').attr('lang') == $('html').attr('default-lang') ? '' : '/'+$('html').attr('lang');//_self.data('lang').slice(0, -1);
                    SEARCH_TYPE=_self.data('type');

                    /*
                    if(['', '/en', '/ru'].indexOf(LANG)===-1){
                        return;
                    }*/

                    INPUT=_self;
                    BLOCKS=$('#blocks');

                    SEARCH_BUTTON=$('#search_button');

                    setInterval(function(){
                        if(input_query!=INPUT.val()){
                            input_query=INPUT.val();

                            if(input_query){
                                clearTimeout(timeout);

                                timeout=setTimeout(function(){
                                    APP.utils.suggest.show(input_query);
                                }, 200);
                            }

                            if(INPUT.val()==''){
                                APP.utils.suggest.clear();
                            }
                        }
                    }, 100);

                    setTimeout(function(){
                        INPUT.val('');

                        if(!INPUT.data('preselected')){
                            INPUT.attr('placeholder', INPUT.data('placeholder'));
                        }

                        INPUT.focus();
                    }, 500);

                    SEARCH_BUTTON.click(function(){
                        APP.utils.query();
                    });

                    BLOCKS.click(function(e){
                        if($(e.target).closest('.block').length){
                            return;
                        }

                        if(INPUT.val()!=''){
                            $('#suggest').show();
                        }

                        INPUT.focus();
                    });

                    INPUT.focus(function(){
                        if(INPUT.val()!=''){
                            $('#suggest').show();
                        }
                    });

                    $document.on('keydown', function(e) {
                        _self.isCmdDown = e[IS_MAC ? 'metaKey' : 'ctrlKey'];
                    });

                    $document.on('keyup', function(e) {
                        if (e.keyCode === KEY_CMD){
                            _self.isCmdDown = false;
                        }
                    });

                    INPUT.keydown(function(e){

                        switch (e.keyCode){
                            case 90://z
                                if(_self.isCmdDown && INPUT.val()==''){
                                    //undelete
                                    return false;
                                }
                            break;

                            case KEY_ESC:
                                APP.utils.suggest.clear();
                                return;
                            break;

                            case KEY_RETURN:
                                $('#suggest a:eq('+suggest_current+')').click();

                                return;
                            break;

                            case KEY_UP:
                                if(APP.utils.suggest.opened()){
                                    if(suggest_current>0){
                                        suggest_current--;

                                        $('#suggest a').removeClass('selected');
                                        $('#suggest a:eq('+suggest_current+')').addClass('selected');

                                        return;
                                    }
                                }
                            break;

                            case KEY_DOWN:
                                if(APP.utils.suggest.opened()){
                                    if(suggest_current<$('#suggest a').length-1){
                                        suggest_current++;

                                        $('#suggest a').removeClass('selected');
                                        $('#suggest a:eq('+suggest_current+')').addClass('selected');

                                        return;
                                    }
                                }
                            break;

                            case KEY_BACKSPACE:
                                if (INPUT.val()=='' && BLOCKS.find('.block').length){
                                    BLOCKS.find('.block:last').find('a.delete').click();

                                    return;
                                }
                            break;
                        }
                    });

                    if(INPUT.data('buttons') && INPUT.data('buttons')!='*'){
                        var buttons=INPUT.data('buttons').split(',');

                        for(var i=0;i<window.query_types.length;i++){
                            if(buttons.indexOf(window.query_types[i]().prefix)===-1){
                                delete window.query_types[i];
                            }
                        };
                    }

                    APP.utils.block.preload();
                    APP.utils.block.buttons();

                    $document.click(function(e){
                        if(APP.utils.suggest.opened() && !$(e.target).closest('#blocks').length){
                            $('#suggest').hide();
                        }
                    });

                    $document.on('click', '#blocks a.delete', function(e){
                        e.preventDefault();

                        if(BLOCKS.find('.block').length <= 1) {
                            $('.show-more').remove();
                        }

                        var block=$(this).closest('.block'),
                            after_remove;

                        if(typeof block.data('block').remove === 'function'){
                            block.data('block').remove();
                        }

                        if(typeof block.data('block').after_remove === 'function'){
                            after_remove=block.data('block').after_remove;
                        }

                        block.remove();

                        if(after_remove){
                            after_remove();
                        }

                        APP.utils.callback.remove();

                        INPUT.focus();
                        APP.utils.query();
                    });

                    APP.utils.history.bind();
                    APP.utils.history.init();
                }
            },
            utils: {
                init_plan_print_button: function(){
                    if(SEARCH_TYPE!='plan'){
                        return;
                    }

                    var show=0,
                        SEARCH_QUERY=APP.utils.get_query(),
                        href=$('#print-list'),
                        header_totals=$('[header-totals]');

                    for(var i=0; i<SEARCH_QUERY.length; i++){
                        if(SEARCH_QUERY[i].indexOf('dateplan[')>=0)
                            show++;

                        if(SEARCH_QUERY[i].indexOf('edrpou=')>=0)
                            show++;
                    }

                    href.hide();

                    if(parseInt(header_totals.html())>0 && SEARCH_QUERY.length==2 && show==2){
                        href.show();
                        href.attr('href', LANG+'/'+SEARCH_TYPE+'/search/print/html/?'+SEARCH_QUERY.join('&'));
                    }
                },
                totals: {
                    init: function(){
                            var items_list=$('.items-list');

                        $('[mobile-totals]').click(function(e){
                            e.preventDefault();

                            $((navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)?'body':'html,body')).animate({
                                scrollTop: items_list.position().top
                            }, 400);

                            return true;
                        });

                        APP.utils.totals.show();
                    },
                    show: function(){
                        var container=$('[mobile-totals]'),
                            header_totals=$('[header-totals]'),
                            total=header_totals.text();

                        if(total){
                            container.removeClass('none-important').find('.result-all-link span').text(total);
                        }else{
                            container.addClass('none-important');
                        }
                    },
                    reset: function(){
                        $('[mobile-totals]').addClass('none-important');
                    }
                },
                history: {
                    bind: function(){
                        if (IS_HISTORY){
                            window.History.Adapter.bind(window, 'statechange', function(){
                                var state = window.History.getState();
                            });
                        }
                    },
                    init: function(){
                        var search=location.search;

                        if(search && search.indexOf('=')>0){
                            search=search.substring(1).split('&');

                            for(var i=0;i<search.length;i++){
                                var param=search[i].split('=');
                                if(param[0] && param[1]){

                                    if(param[0].indexOf('date[')>=0){
                                        param[1]={
                                            type: param[0].match(/\[(.*?)\]/)[1],
                                            value: decodeURI(param[1]).split('—')
                                        };

                                        param[0]='date';
                                    }

                                    if(param[0].indexOf('dateplan[')>=0){
                                        param[1]={
                                            type: param[0].match(/\[(.*?)\]/)[1],
                                            value: decodeURI(param[1]).split('—')
                                        };

                                        param[0]='dateplan';
                                    }

                                    var button=$('<div/>');

                                    button.data('input_query', '');
                                    button.data('block_type', param[0]);
                                    button.data('preselected_value', param[1]);

                                    APP.utils.block.add(button);
                                }
                            }
                        }

                        APP.utils.result_highlight(INPUT.data('highlight'));
                        APP.utils.init_plan_print_button();

                        INITED=true;
                    },
                    push: function(){
                        if (IS_HISTORY){

                            if($('#query').data('route')) {
                                var route = $('#query').data('route');
                                route = LANG+route.replace('/search', '');
                            } else {
                                var route = LANG+'/'+SEARCH_TYPE+'/search/';
                            }

                            window.History.pushState(null, document.title, route+(SEARCH_QUERY.length ? '?'+SEARCH_QUERY.join('&') : ''));
                        }
                    }
                },
                get_query: function(){
                    var out=[];

                    $('.block').each(function(){
                        var self=$(this),
                            block=self.data('block'),
                            type=block.prefix;

                        if(typeof block.result === 'function'){
                            var result=block.result();

                            if(typeof result === 'object'){
                                out.push(result.join('&'));
                            }else if(result){
                                out.push(type+'='+result);
                            }
                        }
                    });

                    return out;
                },
                query: function(){
                    if(!INITED){
                        return false;
                    }

                    clearTimeout(SEARCH_QUERY_TIMEOUT);

                    $('.main-result').find('#ngo_open_multi_form').hide();

                    SEARCH_QUERY_TIMEOUT=setTimeout(function(){

                        if($('#query').data('route')) {
                            var route = LANG+$('#query').data('route');
                        } else {
                            var route = LANG+'/'+SEARCH_TYPE+'/form/search';
                        }

                        SEARCH_QUERY=APP.utils.get_query();

                        $('#server_query').val(SEARCH_QUERY.join('&'));
                        SEARCH_BUTTON.prop('disabled', SEARCH_QUERY.length?'':'disabled')

                        APP.utils.history.push();

                        if(!SEARCH_QUERY.length){
                            $('#result').html('');

                            if(route.indexOf('indicators') > -1 || route.indexOf('medical') > -1) {
                                $('.link_pagination').hide();
                                $('[no-reviews]').show();
                                $('#tender-content #tender-content-block').remove();
                                $('#tender-content').hide();
                            }

                            return;
                        }

                        $('#search_button').addClass('loading').spin(spin_options);
                        $('.main-result').find('#ngo_open_multi_form').hide();

                        $.ajax({
                            url: route,
                            data: {
                                query: SEARCH_QUERY
                            },
                            method: 'post',
                            headers: APP.utils.csrf(),
                            dataType: "json",
                            success: function(response){
                                if(route.indexOf('indicators') > -1 || route.indexOf('medical') > -1) {
                                    if(response.html) {
                                        $('#result').html(response.html);
                                        $('[no-reviews]').hide();
                                        $('.link_pagination').show();
                                    } else {
                                        $('#result').html('');
                                        $('.link_pagination').hide();
                                        $('[no-reviews]').show();
                                    }

                                    $('#tender-content').hide();
                                    $('#tender-content #tender-content-block').remove();
                                }
                                else {
                                    $('[homepage]').remove();

                                    if (response.html) {
                                        $('#result').html(response.html);
                                        $('.main-result').find('#ngo_open_multi_form').show();

                                        var b = $('#result').find('.show-more');
                                        $('.button-show-more').append(b.clone());
                                        b.remove();

                                        if ($('.button-show-more').find('.show-more').length > 1) {
                                            $('.button-show-more').find('.show-more').eq(1).remove();
                                        }

                                        APP.utils.totals.show();
                                        APP.utils.result_highlight(response.highlight);

                                        APP.utils.init_plan_print_button();
                                    } else {
                                        $('#result').html(INPUT.data('no-results'));
                                    }

                                    var userType = $('body').data('userType');

                                    if (response.userTypes.length > 0 && response.userTypes.indexOf(userType) > -1) {
                                        var filters = [];

                                        $('#blocks').find("div[class^='block-'],div[class*=' block-']").each(function() {

                                            var classes = $(this).attr('class').split(' ');

                                            for (var i = 0; i < classes.length; i++) {
                                                var matches = /^block\-(.+)/.exec(classes[i]);

                                                if (matches != null) {
                                                    var fxclass = matches[0].split('-')[1];

                                                    if(filters.indexOf(fxclass) == -1) {
                                                        filters.push(fxclass);
                                                    }
                                                }
                                            }
                                        });

                                        filters = filters.sort().join('-');
                                        //console.log(filters);
                                        //console.log(PREVIOUS_FILTER);

                                        if(filters+response.count !== PREVIOUS_FILTER) {
                                            if (typeof ga === 'function') {
                                                ga('send', 'event', userType, 'filter', filters, response.count);
                                                PREVIOUS_FILTER = filters+response.count;
                                            }
                                        }
                                    }
                                }

                                $('#search_button').removeClass('loading').spin(false);
                            }
                        });
                    }, 300);
                },
                result_highlight: function(words){
                    if(words){
                        $.each(words, function(key, value){
                            $('#result').highlight(value, {
                                element: 'i',
                                className: 'select'
                            });
                        });
                    }
                },
                block: {
                    remove: function(e){
                        e.preventDefault();

                    },
                    create: function(block_type){
                        for(var i=0; i<window.query_types.length; i++){
                            if(typeof window.query_types[i] === 'function'){
                                var type=window.query_types[i]();

                                if(type.prefix==block_type){
                                    return type;
                                }
                            }
                        }
                    },
                    add: function(self){
                        var input_query=self.data('input_query'),
                            block_type=self.data('block_type'),
                            block=APP.utils.block.create(block_type),
                            template=block && block.template ? block.template.clone().html() : null,
                            is_exact=false//(type.pattern_exact && type.pattern_exact.test(input))

                        if(!template){
                            return;
                        }

                        block.value=input_query;
                        template=APP.utils.parse_template(template, block);

                        INPUT.removeClass('no_blocks').removeAttr('placeholder');

                        if(self.data('preselected_value')){
                            template.data('preselected_value', self.data('preselected_value'));
                        }

                        if(self.data('multi_value')){
                            template.data('multi_value', self.data('multi_value'));
                        }

                        template.append('<a href="" class="delete">×</a>');

                        BLOCKS.append(template);
                        BLOCKS.append(INPUT);

                        if(typeof block.init === 'function'){
                            block=block.init(input_query, template);
                        }else{
                            INPUT.focus();
                        }

                        if(typeof block.after_add === 'function'){
                            block.after_add();
                        }

                        template.data('block', block);

                        INPUT.val('');
                    },
                    preload: function(){
                        for(var i=0; i<window.query_types.length; i++){
                            if(typeof window.query_types[i] === 'function'){
                                var type=window.query_types[i]();

                                if(typeof type.load === 'function'){
                                    type.load();
                                }
                            }
                        }
                    },
                    buttons: function(){
                        var button_blocks=[];

                        for(var i=0; i<window.query_types.length; i++){
                            if(typeof window.query_types[i] === 'function'){
                                var type=window.query_types[i]();

                                if(type.button_name || type.template.data('buttonName')){
                                    button_blocks.push(type);
                                }
                            };
                        }

                        button_blocks.sort(function(a, b){
                            if (a.order < b.order)
                                return -1;

                            if (a.order > b.order)
                                return 1;

                            return 0;
                        });

                        for(var i=0; i<button_blocks.length; i++){
                            APP.utils.button.add(button_blocks[i]);
                        }
                    }
                },
                callback: {
                    remove: function(){
                        if(!BLOCKS.find('.block').length){
                            INPUT.addClass('no_blocks');
                            INPUT.attr('placeholder', INPUT.data('placeholder'));
                            APP.utils.totals.reset();
                        }
                    },
                    check: function(suggest){
                        return function(response, textStatus, jqXHR){
                            if(response){
                                suggest.removeClass('none');
                            }else{
                                suggest.remove();
                            }
                        }
                    }
                },
                button: {
                    add: function(block){
                        var button=$('#helper-button').clone().html(),
                            button_data_name=block.template.data('buttonName');

                        button=$(button.replace(/\{name\}/, button_data_name ? button_data_name : block.button_name));

                        button.data('input_query', '');
                        button.data('block_type', block.prefix);

                        button.click(function(e){
                            e.preventDefault();

                            APP.utils.block.add($(this));
                        });

                        $('#buttons').append(button);
                    }
                },

				getFocusStatus: function(){

                },
                suggest: {
                    show: function(input_query){
                        var blocks=APP.utils.detect_query_block(input_query),
                            row,
                            item,
                            suggestName;

                        APP.utils.suggest.clear();

                        if(blocks.length){
                            $.each(blocks, function(index, block){
                                row=$('#helper-suggest').clone().html();

                                if(typeof block.suggest_item=='function'){
                                    row=block.suggest_item(row, input_query);
                                }else{
                                        suggestName=block.template.data('suggestName');

                                    row=row.replace(/\{name\}/, suggestName ? suggestName : block.name);
                                    row=row.replace(/\{value\}/, input_query);
                                }

                                if(row){
                                    item=$(row);

                                    if(input_query && block.json && block.json.check){
                                        $.ajax({
                                            url: LANG+'/'+SEARCH_TYPE+block.json.check,
                                            method: 'POST',
                                            dataType: 'json',
                                            headers: APP.utils.csrf(),
                                            data: {
                                                query: input_query
                                            },
                                            success: APP.utils.callback.check(item)
                                        });
                                    }else{
                                        item.removeClass('none');
                                    }

                                    item.data('input_query', input_query);
                                    item.data('block_type', block.prefix);

                                    item.click(function(e){
                                        e.preventDefault();

                                        APP.utils.block.add($(this));
                                    });

                                    $('#suggest').append(item);
                                }
                            });

                            $('#suggest a:first').addClass('selected');

                            $('#suggest').show();

                            suggest_opened=true;
                        }
                    },
                    clear: function(){
                        $('#suggest').hide().empty();
                        suggest_current=0;
                        suggest_opened=false;
                    },
                    opened: function(){
                        return suggest_opened;
                    }
                },
                detect_query_block: function(query){
                    var types=[];

                    for(var i=0; i<window.query_types.length; i++){
                        if(typeof window.query_types[i] === 'function'){
                            var type=window.query_types[i]();

                            if(typeof type.validate === 'function' && type.validate(query)){
                                types.push(type);
                            } else if(type.pattern_search.test(query)){
                                types.push(type);
                            }
                        }
                    }

                    types.sort(function(a, b){
                        if (a.order < b.order)
                            return -1;

                        if (a.order > b.order)
                            return 1;

                        return 0;
                    });

                    return types;
                },
                parse_template: function(template, data){
                    for(var i in data){
                        template=template.replace(new RegExp('{' + i + '}', 'g'), data[i]);
                    }

                    return $(template);
                },
                csrf: function(){
                    return {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    };
                }
            }
        };

        return methods;
    }());

    APP.common();

    $(function (){
        $('[data-js]').each(function(){
            var self = $(this);

            if (typeof APP.js[self.data('js')] === 'function'){
                APP.js[self.data('js')](self, self.data());
            } else {
                console.log('No `' + self.data('js') + '` function in app.js');
            }
        });
    });

})(window);

String.prototype.trunc = String.prototype.trunc || function(n){
    return (this.length > n) ? this.substr(0, n-1)+'&hellip;' : this;
};

$('body').click(function() {
    if ( $('.selectize-input').hasClass('focus')) {
        $('.c-find-form__input-group-transp').addClass('is-focus');

    } else if ($('.selectize-input').hasClass('has-items')) {
        $('.c-find-form__input-group-transp').addClass('is-focus');
    } else {
        $('.c-find-form__input-group-transp').removeClass('is-focus');
    }
});


$(document).ready(function(){
    $(".js-menu").on('click', function () {
        $(this).toggleClass('open').next(".menu-header").slideToggle();
    });
    /*$(".js-more").on('click', function () {
        $(this).closest('tr').addClass('open');
    });*/
    $('body').on('click', ".js-more .show_more", function () {
        $(this).closest('.js-more').addClass('open').prev('h4').find('.maxheight').addClass('open');
        $(this).closest('.js-more').addClass('open').prev('.maxheight, .blog-maxheight').addClass('open');
        $(this).closest('.js-more').addClass('open').closest('td').addClass('open');
    });
    $('body').on('click', ".js-more .hide_more", function () {
        $(this).closest('.js-more').removeClass('open').prev('h4').find('.maxheight').removeClass('open');
        $(this).closest('.js-more').removeClass('open').prev('.maxheight, .blog-maxheight').removeClass('open');
        $(this).closest('.js-more').removeClass('open').closest('td').removeClass('open');
    });
    $(".js-accordeon").on('click', function () {
        $(this).next('.js-accordeon-wrap').slideToggle();
        var checkbox=$(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
    });






    $(".js-filter-tender.mobile").on('click', function () {
        $(this).next('form').slideToggle();
    });
    $('.js-filter-go.mobile').on( 'click', function () {
        $(this).toggleClass('open').next('form').slideToggle();
    });





    //modal form
    var overlay = $('.overlay2');
    var modal_main_wrap = $('body');
    var modal = $('.modal_div');

    if($(modal).hasClass('show')){
        overlay.fadeIn(100,
            function(){
                modal_main_wrap.addClass('no-scroll');
            });

    }

    modal_main_wrap.on('click', '.open_modal', function(event){
        event.preventDefault();
        var div = $(this).attr('href');
        overlay.fadeIn(
            function(){
                $(div)
                    .css('display', 'block')
                    .animate({opacity: 1});
                modal_main_wrap.addClass('no-scroll');
            });
    });

    modal_main_wrap.on('click', '.modal_close, #overlay', function(){
        modal.animate({opacity: 0}, 100,
            function(){
                $(this).css('display', 'none');

                if($(this).parent().data('form-modal') !== undefined) {
                    $(this).parent().css('display', 'none');
                }

                overlay.fadeOut(100);
                $(".message_modal").removeClass("show");
                $(".modal_div.show").removeClass("show");
                modal_main_wrap.removeClass('no-scroll');
            }
        );
    });


    $(this).keydown(function(eventObject){
        if (eventObject.which == 27)
            modal.animate({opacity: 0}, 200,
                function(){
                    $(this).css('display', 'none');
                    overlay.fadeOut(400);
                    $(".message_modal").removeClass('show');
                    modal_main_wrap.removeClass('no-scroll');
                    $(".modal_div.show").removeClass("show");

                }
            );
    });

    $('.container').on('click', '.badge_icon', function () {
        $(this).parent().parent().find('.info_text').removeClass('current');
        $(this).next('.info_text').addClass('current');
        $(this).parent().parent().find('div:not(.current)').removeClass('show');
        $(this).next('.info_text').toggleClass('show');
    });

    $('.container').on('click', '.info_icon', function () {
        if($(this).closest('.item_risk').length == 0) {
            $(this).next('.info_text').toggleClass('show');
        }
    });
    $('.bid-contacts').on('click', 'span', function () {

        //$('.bid-contacts .info_text').siblings().removeClass('show');

        $(this).parent().find('.info_text').toggleClass('show');
    });

    $('.js_open_form_reviews').on('click', function () {
        $(this).closest('.form-holder').find('.js_form_reviews').slideToggle();
    });

    $('.js_more .show_more').on('click', function () {
        $(this).addClass('hide').next('.hide_more').removeClass('hide').closest('.js_more').prev('.block_text').removeClass('maxheight');

    });
    $('.js_more .hide_more').on('click', function () {
        $(this).addClass('hide').prev('.show_more').removeClass('hide').closest('.js_more').prev('.block_text').addClass('maxheight');

    });


    $(function() {

        var clickFunction = function(hash) {
            var hrefVal, target;
            if (typeof hash === 'string') {
                hrefVal = hash;
            } else {
                hrefVal = $(this).attr('href');
            }

            if(hrefVal=='#_=_'){
                return;
            }

            target = $(hrefVal);
            if (target.length) {

                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 1000);


            }
        };
        $('.list_fixed_menu a[href^="#"]').click(clickFunction);
        if (window.location.hash) {
            clickFunction(window.location.hash);
        }

    });

    $('.block_faq .item h5').on('click', function () {
        $(this).next(".faq_text").slideToggle();
        $(this).closest('.item').toggleClass('open');
    });

    $(this).scroll(function(){
        var top = $(window).scrollTop();
        var footerHeight = $('.c-f').innerHeight() + 15;
        var navHeight = $('.list_fixed_menu .static_wrap').innerHeight();
        var windowHeight = $(window).height();
        var mainHeight = $('.wrapper-main').height() + (windowHeight - navHeight);

            var bottom = $(window).scrollTop() + window.innerHeight;
        //console.log('bottom'+bottom );
            //console.log('main '+mainHeight );
                //console.log('windowHeight'+windowHeight );
                    //console.log('nav '+navHeight );

            if(top>200 && window.location.href.indexOf('indicators') > -1){
                $(".list_fixed_menu").addClass('static');
            }
        else if(top>600){
                $(".list_fixed_menu").addClass('static');
            } else{
                $(".list_fixed_menu").removeClass('static');
            }

            if(bottom > mainHeight){
                $(".list_fixed_menu .static_wrap").css({'top': 'inherit', 'bottom': footerHeight+'px'})
            } else {
                $(".list_fixed_menu .static_wrap").css({'top': '0', 'bottom':  'inherit'})
            }
    });

    /*$('.js_slick_slataristic').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        //autoplay: true,
        autoplaySpeed: 2000,
        centerMode: true,
        variableWidth: true,
        prevArrow: '<button type="button" data-role="none" class="slick-prev slick-arrow" aria-label="Previous" role="button" ></button>',
        nextArrow: '<button type="button" data-role="none" class="slick-next slick-arrow" aria-label="Next" role="button" ></button>',
        responsive: [
            {
                breakpoint: 1199,
                settings: {
                    slidesToShow: 2

                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1
                }
            }

        ]
    });*/


    // $(document).mouseup(function(e){

    //     var metrics_selector = $('.item-dropdown .form-holder'),
    //         metrics_dropdown = $('.item-dropdown .settings');

    //     if (  metrics_selector.is(':visible') ) {
    //         if (!metrics_selector.is(e.target) && metrics_selector.has(e.target).length === 0)
    //         {
    //             metrics_selector.hide();
    //             metrics_dropdown.removeClass('open');
    //         }
    //     }

    // });

    $('#tender-content').on('click', '.js_accordeon_sidebar', function () {
        $(this).toggleClass('open').next().next('.accordeon-wrap').slideToggle();
    });

    // Блок сайдбар-тендер
    /*$('.tender-header-banner').on('click', '.tender-header__descr-toggle', function (e) {
        if (!e) var e = window.event;
        e.cancelBubble = true;
        if (e.stopPropagation) e.stopPropagation();

        $(this).parent().toggleClass('toggled').find('.tender-header__descr-risks .risks-items').addClass('hidden');
        $(this).parent().find('.kick-item .kick-item-info-btn').removeClass('hidden');

        if($(this).closest('.tender-header__contracts').html() !== undefined) {
            if($(this).closest('.tender-header__contracts').find('.tender-header__descr').hasClass('toggled')) {
                $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info-btn').removeClass('toggled');
                $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info').addClass('hidden');
            } else {
                $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info-btn').addClass('toggled');
                $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info').removeClass('hidden');
            }
        } else {
            $(this).parent().find('.kick-item .kick-item-info').addClass('hidden');
        }
    });*/

    /*$('#vue-indicators').on('click', '#sidebar .tender-header-block', function (e) {

        if(getSelectedText()) {
            if (!e) var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();
            return;
        }

        console.log('#sidebar .tender-header-block');

        if($(this).hasClass('tender-header__lot')) {
            $(this).next('.tender-header__lots-blocks').toggleClass('hidden');
        }

        console.log($(this).find('.tender-header__descr-toggle').length);

        $(this).find('.tender-header__descr-toggle').each(function() {

            $(this).parent().toggleClass('toggled');//.find('.tender-header__descr-risks .risks-items').addClass('hidden');

            if($(this).parent().hasClass('toggled')) {
                $(this).parent().find('.kick-item .kick-item-info-btn.bottom:not(.hidden)').trigger('click');
            }

            if($(this).closest('.tender-header__contracts').html() !== undefined) {
                if($(this).closest('.tender-header__contracts').find('.tender-header__descr').hasClass('toggled')) {
                    $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info-btn').removeClass('toggled');
                    $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info').addClass('hidden');
                } else {
                    $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info-btn').addClass('toggled');
                    $(this).closest('.tender-header__contracts').find('.kick-item .kick-item-info').removeClass('hidden');
                }
            } else {
                $(this).parent().find('.kick-item .kick-item-info').addClass('hidden');
            }

            if($(this).parent().find('.risks-title-toggled').html() !== undefined) {
                $(this).parent().find('.risks-title:not(.risks-title-toggled)').trigger('click');
            }
        });
    });*/

    /*$('#vue-indicators').on('DOMNodeInserted', '#sidebar', function () {
        console.log('sidebar inserted');

        var th = $(this);
        th.slick('slickPrev');
    });*/

    $('body').on('DOMNodeInserted', '#sidebar', function () {

        //console.log('sidebar inserted');

        var th = $('#sidebar');

        if(!th.closest('#vue-indicators').data('isMobile')) {
            initSidebar();
            return;
        }

        if(!th.hasClass('swipe')) {

            th.addClass('swipe');
            th.swipe({
                swipeLeft: function () {
                    $(this).fadeOut('fast', function() {
                        $(this).find('.next-tender').click();
                    });
                    $(this).fadeIn();
                },
                swipeRight: function () {
                    $(this).fadeOut('fast', function() {
                        $(this).find('.previous-tender').click();
                    });
                    $(this).fadeIn();
                }
            });
            initSidebar();
        }
    });

    function initSidebar() {

        var ngo_f20=$('#sidebar').find('#ngo_open_multi_form_f202, #ngo_open_multi_form_f203'),
            ngo_f201=$('#sidebar').find('#ngo_open_multi_form_f201'),
            ngo_f20_visible=false;

        var checked_tenders=function(){
            ngo_f20_visible=false;
            ngo_f20.addClass('disabled');
            ngo_f201.addClass('disabled');

            var checked=$('.ngo-checkbox:checked');

            checked.each(function(){
                var checkbox=$(this);

                if(!ngo_f20_visible && checkbox.data('f201')==1) {
                    ngo_f20_visible=true;
                }
            });

            if(ngo_f20_visible) {
                ngo_f20.removeClass('disabled');
            }

            if(checked.length) {
                ngo_f201.removeClass('disabled');
            }
        }

        $('.indicator-search-results').on('click', '#ngo_open_multi_form_select_all', function(e){
            var checked = $(this).is(':checked');
            $('.indicator-search-results').find('input[type="checkbox"].ngo-checkbox').prop('checked', checked);
            checked_tenders();
        });

        $('.indicator-search-results').on('click', 'input[type="checkbox"].ngo-checkbox', function(e){
            checked_tenders();
        });

        $('#sidebar').on('click', '.ngo_open_multi_form_button', function(e) {
            e.preventDefault();

            if($(this).is('.disabled')) {
               return false;
            }

            var ids = [];

            $('.indicator-search-results').find('input[type="checkbox"]:checked').each(function() {
                var self = $(this);
                ids.push(self.data('tender-public-id'));
            });

            if(ids.length <= 0) {
                alert('Потрібно обрати мінімум 1 тендер!');
                return false;
            }

            window.location = $(this).data('href')+'/'+ids.join();
        });

        $('#sidebar').on('click', '.feedback', function(e) {
            e.preventDefault();
            $('.feedback-overlay, .feedback-form-container').addClass('active');
        });

        $('#sidebar').on('click', '.chat-item-text,.detail-value,.detail-value-description,.block-value,.block-value-description', function (e) {
            if (!e) var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();
            //console.log('.chat-item-text,.detail-value,.detail-value-description,.block-value,#sidebar .block-value-description');
            var h = $(this).attr('class').indexOf('description') > -1 ? 60 : 35;

            $(this).css('max-height', function (i, v) {
                return (parseInt(v) + (h * 3)) + 'px';
            });
            ;
        });

        $('#sidebar').on('click', '.tender-header-banner .tender-header__descr .risks-title.show-docs', function (e) {
            if (!e) var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();
            //console.log('#sidebar .tender-header-banner .tender-header__descr .risks-title.show-docs');
            $(this).next().removeClass('hidden').next().removeClass('hidden');
        });

        $('#sidebar').on('click', '.tender-header-banner .tender-header__descr .risks-title:not(.show-docs)', function (e) {
            if (!e) var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();
            //console.log('#sidebar .tender-header-banner .tender-header__descr .risks-title:not(.show-docs)');
            $(this).siblings('.risks-items').removeClass('hidden');
        });

        $('#sidebar').on('click', '.tender-header-banner .risks-title-toggled', function (e) {
            if (!e) var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();

            //console.log('.tender-header-banner .risks-title-toggled');
            $(this).closest('.tender-header-block').trigger('click');
            $(this).parent().find('.risks-title:not(.risks-title-toggled)').trigger('click');
        });

        $('#sidebar').on('click', '.tender-header-banner .kick-item', function (e) {
            if (!e) var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();
            //console.log('#sidebar .tender-header-banner .kick-item');
            var button = $(this).find('.kick-item-info-btn:not(.bottom)');

            button.toggleClass('toggled');
            button.siblings('.kick-item-info').toggleClass('hidden');

            if (button.data('hideText') !== undefined) {
                if (button.hasClass('toggled')) {
                    button.children().html(button.data('hideText'));
                } else {
                    button.children().html(button.data('showText'));
                }
            }

            button.next().next('.bottom').toggleClass('hidden').toggleClass('toggled');
        });

        $('#sidebar').on('click', '.tender-header-banner .kick-item-info-btn.bottom', function (e) {
            if (!e) var e = window.event;
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();
            //console.log('#sidebar .tender-header-banner .kick-item-info-btn.bottom');
            $(this).prev().prev('.kick-item-info-btn:not(.bottom)').trigger('click');
        });

        $('#sidebar').on('click', '.tender-header-banner .items-block .item-option', function (e) {
            e.preventDefault();
            //console.log('#sidebar .tender-header-banner .items-block .item-option');
            $(this).closest('.items-block').nextAll('.items-block').toggleClass('hidden');
        });


        $('#sidebar').on('click', '.show-comments-risk', function (e) {
            e.preventDefault();

            var _self = $(this);
            var data = $(this).data('riskComments');
            var comments = JSON.parse(JSON.stringify(data));
            var html = '<div>';

            for(var i in comments) {
                var comment = comments[i];

                var mark = '<div class="reviews__stars">\
                <ul class="tender-stars tender-stars--'+comment.risk_evaluation+'">\
                    <li></li><li></li><li></li><li></li><li></li>\
                    </ul>\
                    </div>';

                html += '<p>';
                html += mark+'<br><span>'+comment.risk_comment+'<span><br><span style="font-size:12px;">'+comment.date_submitted+'</span><br><span style="font-size:12px;">'+comment.full_name+'</span>';
                html += '</p>';
            }

            html += '</div>';

            $('.modal_div[data-form-modal="' + _self.data('form') + '"]').find('.risk_comment_data').html(html);

            $('[data-form-modal="' + _self.data('form') + '"]')
                .css('display', 'block');

            $('.overlay2[data-form-modal="' + _self.data('form') + '"]')
                .animate({opacity: 0.5});

            $('.modal_div[data-form-modal="' + _self.data('form') + '"]')
                .animate({opacity: 1});
        });

        $('#sidebar').on('click', '.comment-risk', function (e) {
            e.preventDefault();

            var _self = $(this);

            $('.modal_div[data-form-modal="' + _self.data('form') + '"]').find('input[name="risk_code"]').val(_self.attr('data-risk-code'));

            $('[data-form-modal="' + _self.data('form') + '"]')
                .css('display', 'block');

            $('.overlay2[data-form-modal="' + _self.data('form') + '"]')
                .animate({opacity: 0.5});

            $('.modal_div[data-form-modal="' + _self.data('form') + '"]')
                .animate({opacity: 1});
        });


    }

    $('.page_indicator_search').on('click', '.modal_close', function (e) {
        $(this).parent().parent().hide();
        $(this).parent().find('textarea').val('');
    });

    $('.page_indicator_search').on('submit', '#risk-form', function (e) {
        e.preventDefault();

        var th = $(this);
        var data = th.serializeArray();

        $.post('/api/risk_comment', data,
            function (resp, textStatus, xhr) {
                if (resp.status == 'ok') {
                    th.closest('.modal_div').find('.modal_close').trigger('click');
                }
            }
        );
    });

    // Отключение футера на странице интикаторов
    /*if( window.location.href=="/indicators") {
        $('.c-f').hide();
    };*/

    $('.filter-reset').on('click', function() {
        var body = $(this).closest('.indicator-filter-body');
        var priceBlock = $('#price-range').parent();
        var price = $('#price-range');

        priceBlock.find('.js-from').val(price.attr('data-min'));
        priceBlock.find('.js-from').trigger('change');
        priceBlock.find('.js-to').val(price.attr('data-max'));
        priceBlock.find('.js-to').trigger('change');

        body.find('input[type="checkbox"]:checked').trigger('click');
        body.find('.selected-tags').html('');
        body.find('.filters-count').html('');
        body.find('input[type="text"]').val('');

        $('.top-tags').addClass('hide');
        document.getElementById('any-risks-block').classList.add('hide');
    });

    $('.detail-title.lot-title').each( function() {
        if ( $(this).height() > 48 ) {
            $(this).addClass('overflow');
        }
        $(this).addClass('overflow').height();
        //console.log( $(this).height() );
    });

    $('.detail-title.lot-title.overflow').click( function() {
        $(this).removeClass('overflow');
    });

     $('.detail-title.overflow').click( function() {
        $(this).removeClass('overflow');
    });

    $('.tender-content-close').click( function() {
        $('#result').find('tr').removeClass('selected');
        $('#community-content').show();
        $('#tender-content').hide();
    });

    $('.filter-modal-btn').click( function() {
        $('body').addClass('modal-open');
        $('.indicator-filter-modal').addClass('open');
        $('.filter-items').scrollTop(0);
        
        window.location.hash = '';
    });
    
    if(window.location.hash=='#filters') {
        $('.filter-modal-btn').click();
    }

    $('.indicator-filter-overlay, .indicator-filter-body .close').click( function() {
        $('.indicator-filter-modal').removeClass('open');
        $('body').removeClass('modal-open');
    });

    $('.filter-item .title').click( function() {
        $(this).toggleClass('open').closest('.filter-item').find('.filter-item-body').toggleClass('open');
    });

    // $("#price-range").ionRangeSlider();
    function rangeslider() {

        var $range = $("#price-range"),
            $from = $(".js-from"),
            $to = $(".js-to"),
            range,
            min = $range.attr('data-min'),
            max = $range.attr('data-max'),
            from,
            to;

        var updateValues = function () {
            $from.prop("value", from);
            $to.prop("value", to);
        };

        $range.ionRangeSlider({
            type: "double",
            min: min,
            max: max,
            keyboard: true,
            prettify_enabled: true,
            prettify_separator: " ",
            onChange: function (data) {
                from = data.from;
                to = data.to;
                
                updateValues();
            }
        });

        range = $range.data("ionRangeSlider");

        var updateRange = function () {
            range.update({
                from: from,
                to: to
            });
        };

        $from.on("change", function () {
            from = +$(this).prop("value");
            if (from < min) {
                from = min;
            }
            if (from > to) {
                from = to;
            }

            updateValues();    
            updateRange();
        });

        $to.on("change", function () {
            to = +$(this).prop("value");
            if (to > max) {
                to = max;
            }
            if (to < from) {
                to = from;
            }

            updateValues();    
            updateRange();
        });

        $from.trigger('change');
        $to.trigger('change');
    }

    rangeslider();

    // $('#date').mask('99/99/9999',{placeholder:"mm/dd/yyyy"});
    $('.date-mask').inputmask();

    // var now = new Date();
    // $('#date_from').inputmask();
    // $('#date_to').inputmask(
    //     mask: '_ _ _',
    //     placeholder: now.getDay() + " " + (now.getMonth() + 1) + " " + (1900 + now.getYear())
    // );

    $('.indicator-search-form-sorting').selectize();
});

function getSelectedText() {
    if (window.getSelection) {
        return window.getSelection().toString();
    } else if (document.selection) {
        return document.selection.createRange().text;
    }
    return '';
}

function pieChart() {
    $('[data-js="ngo_header_reload"]').find('.pie_diagram .pie').each(function () {

        var data = $(this).data();

        if(data.first >= 1000 || data.second >= 1000) {
            data.first = parseInt(data.first/100);
            data.second = parseInt(data.second/100);
        }
        else if(data.first >= 100 || data.second >= 100) {
            data.first = parseInt(data.first/10);
            data.second = parseInt(data.second/10);
        }

        var dataset = [
            {
                value: data.first,
                color: '#ebebeb'
            }, {
                value: data.second,
                color: '#e55166'
            }
        ];

        //var maxValue = data.first > data.second ? data.first : data.second;
        //maxValue = parseInt(maxValue/10);
        var maxValue = 25;

        var container = $(this);

        var addSector = function (data, startAngle, collapse) {
            var sectorDeg = 3.6 * data.value;
            var skewDeg = 90 + sectorDeg;
            var rotateDeg = startAngle;
            if (collapse) {
                skewDeg++;
            }

            var sector = $('<div>', {
                'class': 'sector'
            }).css({
                'background': data.color,
                'transform': 'rotate(' + rotateDeg + 'deg) skewY(' + skewDeg + 'deg)'
            });
            container.append(sector);

            return startAngle + sectorDeg;
        };

        dataset.reduce(function (prev, curr) {
            return (function addPart(data, angle) {
                if (data.value <= maxValue) {
                    return addSector(data, angle, false);
                }

                return addPart({
                    value: data.value - maxValue,
                    color: data.color
                }, addSector({
                    value: maxValue,
                    color: data.color,
                }, angle, true));
            })(curr, prev);
        }, 0);

    });

}

(function(a){var r=a.fn.domManip,d="_tmplitem",q=/^[^<]*(<[\w\W]+>)[^>]*$|\{\{\! /,b={},f={},e,p={key:0,data:{}},i=0,c=0,l=[];function g(g,d,h,e){var c={data:e||(e===0||e===false)?e:d?d.data:{},_wrap:d?d._wrap:null,tmpl:null,parent:d||null,nodes:[],calls:u,nest:w,wrap:x,html:v,update:t};g&&a.extend(c,g,{nodes:[],parent:d});if(h){c.tmpl=h;c._ctnt=c._ctnt||c.tmpl(a,c);c.key=++i;(l.length?f:b)[i]=c}return c}a.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(f,d){a.fn[f]=function(n){var g=[],i=a(n),k,h,m,l,j=this.length===1&&this[0].parentNode;e=b||{};if(j&&j.nodeType===11&&j.childNodes.length===1&&i.length===1){i[d](this[0]);g=this}else{for(h=0,m=i.length;h<m;h++){c=h;k=(h>0?this.clone(true):this).get();a(i[h])[d](k);g=g.concat(k)}c=0;g=this.pushStack(g,f,i.selector)}l=e;e=null;a.tmpl.complete(l);return g}});a.fn.extend({tmpl:function(d,c,b){return a.tmpl(this[0],d,c,b)},tmplItem:function(){return a.tmplItem(this[0])},template:function(b){return a.template(b,this[0])},domManip:function(d,m,k){if(d[0]&&a.isArray(d[0])){var g=a.makeArray(arguments),h=d[0],j=h.length,i=0,f;while(i<j&&!(f=a.data(h[i++],"tmplItem")));if(f&&c)g[2]=function(b){a.tmpl.afterManip(this,b,k)};r.apply(this,g)}else r.apply(this,arguments);c=0;!e&&a.tmpl.complete(b);return this}});a.extend({tmpl:function(d,h,e,c){var i,k=!c;if(k){c=p;d=a.template[d]||a.template(null,d);f={}}else if(!d){d=c.tmpl;b[c.key]=c;c.nodes=[];c.wrapped&&n(c,c.wrapped);return a(j(c,null,c.tmpl(a,c)))}if(!d)return[];if(typeof h==="function")h=h.call(c||{});e&&e.wrapped&&n(e,e.wrapped);i=a.isArray(h)?a.map(h,function(a){return a?g(e,c,d,a):null}):[g(e,c,d,h)];return k?a(j(c,null,i)):i},tmplItem:function(b){var c;if(b instanceof a)b=b[0];while(b&&b.nodeType===1&&!(c=a.data(b,"tmplItem"))&&(b=b.parentNode));return c||p},template:function(c,b){if(b){if(typeof b==="string")b=o(b);else if(b instanceof a)b=b[0]||{};if(b.nodeType)b=a.data(b,"tmpl")||a.data(b,"tmpl",o(b.innerHTML));return typeof c==="string"?(a.template[c]=b):b}return c?typeof c!=="string"?a.template(null,c):a.template[c]||a.template(null,q.test(c)?c:a(c)):null},encode:function(a){return(""+a).split("<").join("&lt;").split(">").join("&gt;").split('"').join("&#34;").split("'").join("&#39;")}});a.extend(a.tmpl,{tag:{tmpl:{_default:{$2:"null"},open:"if($notnull_1){__=__.concat($item.nest($1,$2));}"},wrap:{_default:{$2:"null"},open:"$item.calls(__,$1,$2);__=[];",close:"call=$item.calls();__=call._.concat($item.wrap(call,__));"},each:{_default:{$2:"$index, $value"},open:"if($notnull_1){$.each($1a,function($2){with(this){",close:"}});}"},"if":{open:"if(($notnull_1) && $1a){",close:"}"},"else":{_default:{$1:"true"},open:"}else if(($notnull_1) && $1a){"},html:{open:"if($notnull_1){__.push($1a);}"},"=":{_default:{$1:"$data"},open:"if($notnull_1){__.push($.encode($1a));}"},"!":{open:""}},complete:function(){b={}},afterManip:function(f,b,d){var e=b.nodeType===11?a.makeArray(b.childNodes):b.nodeType===1?[b]:[];d.call(f,b);m(e);c++}});function j(e,g,f){var b,c=f?a.map(f,function(a){return typeof a==="string"?e.key?a.replace(/(<\w+)(?=[\s>])(?![^>]*_tmplitem)([^>]*)/g,"$1 "+d+'="'+e.key+'" $2'):a:j(a,e,a._ctnt)}):e;if(g)return c;c=c.join("");c.replace(/^\s*([^<\s][^<]*)?(<[\w\W]+>)([^>]*[^>\s])?\s*$/,function(f,c,e,d){b=a(e).get();m(b);if(c)b=k(c).concat(b);if(d)b=b.concat(k(d))});return b?b:k(c)}function k(c){var b=document.createElement("div");b.innerHTML=c;return a.makeArray(b.childNodes)}function o(b){return new Function("jQuery","$item","var $=jQuery,call,__=[],$data=$item.data;with($data){__.push('"+a.trim(b).replace(/([\\'])/g,"\\$1").replace(/[\r\t\n]/g," ").replace(/\$\{([^\}]*)\}/g,"{{= $1}}").replace(/\{\{(\/?)(\w+|.)(?:\(((?:[^\}]|\}(?!\}))*?)?\))?(?:\s+(.*?)?)?(\(((?:[^\}]|\}(?!\}))*?)\))?\s*\}\}/g,function(m,l,k,g,b,c,d){var j=a.tmpl.tag[k],i,e,f;if(!j)throw"Unknown template tag: "+k;i=j._default||[];if(c&&!/\w$/.test(b)){b+=c;c=""}if(b){b=h(b);d=d?","+h(d)+")":c?")":"";e=c?b.indexOf(".")>-1?b+h(c):"("+b+").call($item"+d:b;f=c?e:"(typeof("+b+")==='function'?("+b+").call($item):("+b+"))"}else f=e=i.$1||"null";g=h(g);return"');"+j[l?"close":"open"].split("$notnull_1").join(b?"typeof("+b+")!=='undefined' && ("+b+")!=null":"true").split("$1a").join(f).split("$1").join(e).split("$2").join(g||i.$2||"")+"__.push('"})+"');}return __;")}function n(c,b){c._wrap=j(c,true,a.isArray(b)?b:[q.test(b)?b:a(b).html()]).join("")}function h(a){return a?a.replace(/\\'/g,"'").replace(/\\\\/g,"\\"):null}function s(b){var a=document.createElement("div");a.appendChild(b.cloneNode(true));return a.innerHTML}function m(o){var n="_"+c,k,j,l={},e,p,h;for(e=0,p=o.length;e<p;e++){if((k=o[e]).nodeType!==1)continue;j=k.getElementsByTagName("*");for(h=j.length-1;h>=0;h--)m(j[h]);m(k)}function m(j){var p,h=j,k,e,m;if(m=j.getAttribute(d)){while(h.parentNode&&(h=h.parentNode).nodeType===1&&!(p=h.getAttribute(d)));if(p!==m){h=h.parentNode?h.nodeType===11?0:h.getAttribute(d)||0:0;if(!(e=b[m])){e=f[m];e=g(e,b[h]||f[h]);e.key=++i;b[i]=e}c&&o(m)}j.removeAttribute(d)}else if(c&&(e=a.data(j,"tmplItem"))){o(e.key);b[e.key]=e;h=a.data(j.parentNode,"tmplItem");h=h?h.key:0}if(e){k=e;while(k&&k.key!=h){k.nodes.push(j);k=k.parent}delete e._ctnt;delete e._wrap;a.data(j,"tmplItem",e)}function o(a){a=a+n;e=l[a]=l[a]||g(e,b[e.parent.key+n]||e.parent)}}}function u(a,d,c,b){if(!a)return l.pop();l.push({_:a,tmpl:d,item:this,data:c,options:b})}function w(d,c,b){return a.tmpl(a.template(d),c,b,this)}function x(b,d){var c=b.options||{};c.wrapped=d;return a.tmpl(a.template(b.tmpl),b.data,c,b.item)}function v(d,c){var b=this._wrap;return a.map(a(a.isArray(b)?b.join(""):b).filter(d||"*"),function(a){return c?a.innerText||a.textContent:a.outerHTML||s(a)})}function t(){var b=this.nodes;a.tmpl(null,null,null,this).insertBefore(b[0]);a(b).remove()}})(jQuery);
