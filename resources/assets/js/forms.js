/*!
 *
 * Dozorro JsonForms v1.0.0
 *
 * Author: Lanko Andrey (lanko@perevorot.com)
 *
 * © 2016
 *
 */

var FORMS,
    LANG='uk',
    spin_options={
        color:'#FFF',
        lines: 15,
        width: 2
    };

(function(window, undefined){
    'use strict';

    FORMS = (function(){
        var formSelector=$('#review_form [form-selector]'),
            formContainer=$('#review_form [form-container]'),
            formToolbar=$('#review_form [form-toolbar], .add-ngo-review-form [form-toolbar]'),
            formSuccess=$('#review_form [form-success], .add-ngo-review-form [form-success]'),
            formError=$('#review_form [form-error], .add-ngo-review-form [form-error]'),
            formTitle=$('#review_form [form-title], .add-ngo-review-form [form-title]'),
            formTitleDefault=formTitle.text(),
            tenderEdrpou=$('[data-tender-edrpou]').data('tender-edrpou'),
            formCurrent = '',
            loader=$('[loader]'),
            submitCounter,
            formSchema,
            formParent,
            _params,
            _paramsDefault={
                tenderId: formContainer.data('tender-id'),
                tenderPublicId: formContainer.data('tender-public-id')
            },
            _extraValues={};

        var generators={
            comment: function(){
            }
        };

        var initializers={
            comment: function(_self){
            },
            F204: function(_self){
                _self.click(function(e){
                    e.preventDefault();

                    $('.add-review-form').popup({
                        transition: 'all 0.3s'
                    });

                    $('#review_form').popup('show');

                    _extraValues={};

                    formSelector.show();
                    formContainer.empty();
                    formToolbar.empty();
                    formError.hide();
                    formSuccess.hide();
                });
            }
        };

        var validators={
            comment: function(errors, values){
                if (!values.comment || values.comment.length < 30) {
                    $('[name=comment]').closest('.controls').find('.jsonform-errortext').removeAttr('style').text('Поле обов`язкове до заповнення, та повине мати довжину більше 30 символів');

                    return false;
                }

                return !errors;
            },
            formF102: function(errors, values) {
                if (!values.bestPrice && !values.maxCompetition && !values.productQuality && !values.qualitativeCriteria){
                    $('[name=bestPrice]').closest('.controls').find('.jsonform-errortext').removeAttr('style').text('Це або інше запитання обов`язкове для відповіді');
                    return false;
                }

                return !errors;
            },
            formF110: function(errors, values) {
                if (!values.answeredInTime && !values.communicationMethod && !values.procuringQuestions && !values.supplierQuestions){
                    $('[name=supplierQuestions]').closest('.controls').find('.jsonform-errortext').removeAttr('style').text('Це або інше запитання обов`язкове для відповіді');
                    return false;
                }

                return !errors;
            },
            formF101: function(errors, values) {
                if (!values.overallScore){
                    return false;
                }

                if (!values.overallScoreComment || values.overallScoreComment.length < 30) {
                    $('[name=overallScoreComment]').closest('.controls').find('.jsonform-errortext').removeAttr('style').text('Поле обов`язкове до заповнення, та повине мати довжину більше 30 символів');

                    return false;
                }

                return !errors;
            },
            formF201: function(errors, values) {
                if (!values.abuseName){
                    return false;
                }

                if (!values.abuseComment || values.abuseComment.length < 30) {
                    $('[name=abuseComment]').closest('.controls').find('.jsonform-errortext').removeAttr('style').text('Поле обов`язкове до заповнення, та повине мати довжину більше 30 символів');

                    return false;
                }

                return !errors;
            },
            formF202: function(errors, values) {
                if (!values.actionName){
                    return false;
                }

                if (!values.actionComment || values.actionComment.length < 30) {
                    $('[name=actionComment]').closest('.controls').find('.jsonform-errortext').removeAttr('style').text('Поле обов`язкове до заповнення, та повине мати довжину більше 30 символів');

                    return false;
                }

                return !errors;
            },
            formF203: function(errors, values) {
                if (!values.resultName){
                    return false;
                }

                if (!values.resultComment || values.resultComment.length < 30) {
                    $('[name=resultComment]').closest('.controls').find('.jsonform-errortext').removeAttr('style').text('Поле обов`язкове до заповнення, та повине мати довжину більше 30 символів');

                    return false;
                }

                return !errors;
            }
        };

        var generateForm=function(formCode, validateCode, callback){
            $.ajax({
                url: '/forms/' + formCode + '/' + tenderEdrpou,
                method: 'GET',
                success: function(json){
                    var formSchema = findFormShema(json);

                    formSchema=patchFormSchema(formSchema);

                    if(formCode == 'comment' && $('[data-hash="ngo"]').hasClass('is-show')) {
                        delete formSchema.form[1];
                    }

                    if(formSchema){
                        formSchema.onSubmitValid=function (values) {
                            submitReviewForm(values, formCode);
                        };

                        if(_params.validate && typeof validators[_params.validate]=='function'){
                            formSchema.onSubmit=validators[_params.validate];
                        } else if (validateCode && typeof validators[validateCode]=='function') {
                            formSchema.onSubmit=validators[validateCode];
                        }

                        if(typeof callback == 'function'){
                            var form=$('<form>').attr('action', '/jsonforms').attr('novalidate', true);

                            if(formSchema.title){
                                formContainer.append('<h3>' + formSchema.title + '</h3>');
                            }

                            callback(formSchema, form);
                        }
                    }
                },
                dataType: 'json'
            });
        };

        var initMultiFormAccordeon=function(){
            formContainer.find('h3').wrapInner('<a href="">').each(function(){
                $(this).next().hide();
            });

            formContainer.find('h3 > a').click(function(e){
                e.preventDefault();

                formContainer.find('form').slideUp();
                formCurrent = $(this).closest('h3').next();

                $(this).parent().next().stop(true).slideToggle(checkButton);

                return false;
            });
        }

        var checkButton=function(){
            var hidden=formContainer.find('form:visible').length==0;

            formToolbar.find('[type="submit"]')[hidden?'hide':'show']();
            $('.add-review-form__content')[hidden?'removeClass':'addClass']('toolbar');
        }

        var generateForms=function(callback){
            var forms=_params.form.split('+');
            var validate = [];

            if(_params.validates !== undefined) {
                validate = _params.validates.split('+');
            }

            for(var i=0; i<forms.length; i++){

                var _v = '';

                if(validate.length > 0 && validate[i] !== undefined && validate[i]) {
                    _v = validate[i];
                }

                generateForm(forms[i], _v, callback);
            }
        }

        var isMultiForm=function(){
            return formsCount()>1;
        }

        var formsCount=function(){
            return _params.form.split('+').length;
        }

        var submitReviewForm=function(values, formCode, successCallback){
            loader.show().spin(spin_options);

            values=$.extend(values, _extraValues);

            if(!_params.model){
                _params.model='form';
            }

            $.ajax({
                method: 'POST',
                data: {
                    form: values,
                    tender: _params.tenderId,
                    schema: formCode,
                    tender_public_id: _params.tenderPublicId,
                    parent: _params.parent ? _params.parent : '',
                },
                url: '/jsonforms/'+_params.model,
                dataType: 'json',
                headers: csrf(),
                success: function (response) {
                    submitCounter++;

                    if (response.error) {
                        loader.spin(false).hide();
                        var errorHtml=formError.html();
                        formError.html(response.error).show();
                        formContainer.find('form').addClass('hidden');

                        setTimeout(function () {
                            formError.html(errorHtml).hide();
                            formContainer.find('form').removeClass('hidden');
                        }, 5000);
                    } else if(response) {

                        if(submitCounter==formsCount()){
                            loader.spin(false).hide();

                            $.get(window.location.href, function(html){
                                $("[reviews]").html($(html).find('[reviews]').html());
                                $("[stars]").html($(html).find('[stars]').html());

                                if(values.thread){
                                    $('.reviews__item[data-object-id='+values.thread+']').html($(html).find('.reviews__item[data-object-id='+values.thread+']').html());
                                }
                            });

                            if(typeof successCallback == 'function'){
                                successCallback();
                            }

                            if(_params.next && $('['+_params.next+']').length){
                                formContainer.empty();
                                formToolbar.empty();

                                var selector = _params.thanks;

                                if (selector !== null) {
                                    $(selector).show();
                                    setTimeout(function () {
                                        $(selector).hide();
                                    }, 2000);
                                }

                                $('['+_params.next+'] a').click();
                            } else {
                                formSuccess.show();
                                formContainer.empty();
                                formToolbar.empty();
                            }
                        } else {
                            loader.spin(false).hide();

                            if (isMultiForm()) {
                                formCurrent.prev().fadeOut();
                                formCurrent.fadeOut();
                            }
                        }
                    } else {
                        loader.spin(false).hide();
                        formError.show();
                        formContainer.find('form').addClass('hidden');

                        setTimeout(function () {
                            formError.hide();
                            formContainer.find('form').removeClass('hidden');
                        }, 4000);
                    }
                }
            });
        }

        var csrf=function(){
            return {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            };
        }

        var findFormShema=function(top) {
            if ('form' in top && 'properties' in top) {
                top['schema'] = top['properties'];

                delete top['properties'];
                return top;
            }

            if (top && typeof top == 'object') {
                for (var key in top) {
                    var res = null;
                    if (typeof top[key] == 'object'){
                        if (res = findFormShema(top[key])){
                            return res;
                        }
                    }
                }
            }

            return null;
        }


        var patchByLang = function (items) {
            var title_lang = 'title_' + LANG,
                helpvalue_lang = 'helpvalue_' + LANG,
                titleMap_lang = 'titleMap_' + LANG;

            for (var key in items) {
                var val = items[key];

                if (title_lang in val){
                    val['title'] = val[title_lang];
                }

                if (helpvalue_lang in val){
                    val['helpvalue'] = val[helpvalue_lang];
                }

                if (titleMap_lang in val){
                    val['titleMap'] = val[titleMap_lang];
                }

                if ('items' in val){
                    patchByLang(val['items']);
                }
            }
        }

        var patchFormSchema = function (schema) {
            if ('required' in schema) {
                for (var key in schema['required']) {
                    var val = schema['required'][key];

                    schema['schema'][val]['required'] = true;
                }
            }

            if (LANG.length && 'form' in schema) {
                patchByLang(schema['form']);
            }

            if ('form' in schema && 'type' in schema['form'][0]) {
                if (schema['form'][0]['type'] == 'title') {
                    schema['title'] = schema['form'][0]['title'];
                    schema['form'].splice(0, 1);
                }
            }

            return schema;
        }

        var reviews_objected=$('.reviews__item[data-object-id]');

        var methods={
            js: {
                jsonForm: function (_self) {
                    if(_self.data('init') && typeof initializers[_self.data('init')]=='function'){
                        initializers[_self.data('init')](_self);
                    }
                },
                ngoJsonForm: function (_self) {
                    if(_self.data('init') && typeof initializers[_self.data('init')]=='function'){
                        initializers[_self.data('init')](_self);
                    }
                },
                back: function(_self){
                    _self.click(function(e){
                        e.preventDefault();

                        $('.tender-tabs__item:first').click();
                        $('.tender-tabs__item:last').hide();
                    });
                    // $(document).on('click', '[data-formjs="back"]', function(e){
                    //     e.preventDefault();;
                    //
                    //     $('.tender-tabs__item:first').click();
                    //     $('.tender-tabs__item:last').hide();
                    // });
                },
                comments: function(_self){
                    _self.click(function(e){
                        e.preventDefault();

                        var self=$(this);

                        reviews_objected.hide();

                        $('.tender-tabs__item:last').show();
                        $('.tender-tabs__item:last').click();

                        $('.reviews__item[data-object-id='+self.data('object-id')+']').show();
                    });
                    //var obj=$('.reviews__item[data-object-id]');

                    // $(document).on('click', '[data-formjs="comments"]', function(e){
                    //     e.preventDefault();;
                    //
                    //     var self=$(this);
                    //
                    //     $('.tender-tabs__item:last').show();
                    //     $('.tender-tabs__item:last').click();
                    //
                    //     obj.hide();
                    //     var comments=$('.reviews__item[data-object-id='+self.data('object-id')+']');
                    //     console.log(comments.length);
                    //     comments.show();
                    // });
                },
                open_ngo: function(_self){
                    var loader=$('.add-ngo-review-form').find('[loader]'),
                        formError=$('.add-ngo-review-form').find('[form-error]'),
                        formSuccess=$('.add-ngo-review-form').find('[form-success]'),
                        formContainer=$('.add-ngo-review-form').find('[form-container]'),
                        formTitle=$('.add-ngo-review-form').find('[form-title]'),
                        values={};

                    _self.click(function(e) {
                        e.preventDefault();

                        $('.add-ngo-review-form').popup({
                            transition: 'all 0.3s'
                        });

                        $('#ngo_review_form').popup('show');

                        _extraValues={};

                        formSchema=_self.data('form');
                        formParent=_self.data('parent');

                        $('.ngo-form-accordeon').hide();
                        $('.ngo-form-accordeon.ngo-form-'+formSchema).show();

                        formTitle.html($(this).data('form-title'));

                        formError.hide();
                        formSuccess.hide();
                        formContainer.show();
                    });
                    /*

                    console.log(formSchema);

                    _params=$(this).data();
                    _params=$.extend(_params, _paramsDefault);

                    $('.ngo-form-selector').click(function(e){
                        e.preventDefault();

                        $('.add-ngo-review-form').popup({
                            transition: 'all 0.3s'
                        });

                        $('#ngo_review_form').popup('show');

                        _extraValues={
                            parentForm: $(this).data('parentForm')
                        };

                        formSelector.show();
                        formContainer.empty();
                        formToolbar.empty();
                        formError.hide();
                        formSuccess.hide();
                    });
                    */
/*
                    $(document).on('click', '[data-formjs="ngoJsonForm"]', function(e){
                        e.preventDefault();
                        var generateCounter=0;

                        _params=$(this).data();
                        _params=$.extend(_params, _paramsDefault);

                        formTitle.html(_params.formTitle ? _params.formTitle : formTitleDefault);
                        submitCounter=0;

                        generateForms(function(formSchema, form){
                            formSelector.hide();
                            generateCounter++;

                            formContainer.append(form);
                            form.jsonForm(formSchema);

                            form.append('<input type="submit" value="'+_params.submitButton+'">');

                            if(generateCounter==formsCount() && isMultiForm()){
                                initMultiFormAccordeon();
                            }

                            if(_params.generate && typeof generators[_params.generate]=='function'){
                                generators[_params.generate]();
                            }
                        });
                    });
*/
                },
                submit_ngo_action: function(_self){
                    var loader=$('.add-ngo-review-form').find('[loader]'),
                        formError=$('.add-ngo-review-form').find('[form-error]'),
                        formSuccess=$('.add-ngo-review-form').find('[form-success]'),
                        formContainer=$('.add-ngo-review-form').find('[form-container]'),
                        formTitle=$('.add-ngo-review-form').find('[form-title]'),
                        values={},
                        values2=[],
                        edit='';
                    var selected = _self.data('selected');

                    if(selected) {
                        $('#checkbox'+selected).trigger('click');
                    }

                    Dropzone.autoDiscover = false;

                    $('.dropzone').dropzone({
                        url: '/helpers/upload',
                        maxFiles: 1,
                        uploadMultiple: false,
                        success: function(file, response){
                            if(response && response.success){
                                $(file.previewElement).closest('.form_input').find('input').val('{DOWNLOAD_URL}/'+response.file);
                            }

                        },
                        init: function() {
                            this.on("maxfilesexceeded", function(file) {
                                this.removeAllFiles();
                                this.addFile(file);
                            });
                        },
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $('.add-ngo-review-form').find('[submit-action]').click(function(e){
                        e.preventDefault();


                        loader.show().spin(spin_options);
                        var cnt=formContainer.find('input[type="checkbox"]:checked').length;

                        if(cnt==0){
                            cnt=formContainer.find('input[type="radio"]:checked').length;
                        }

                        var other_comment = formContainer.find('.form_textarea textarea').val();
                        var file_name = formContainer.find('.other_main_form_reviews input[name="file_name"]').val();
                        var file_link = formContainer.find('.other_main_form_reviews input[name="file_link"]').val();
                        var href;

                        if(file_link !== undefined && file_link !== '') {
                            href = ('<a target="_blank" href="'+file_link+'">'+(file_name !== undefined && file_name !== '' ? file_name : file_link)+'</a>');

                            if(other_comment.indexOf('<br>') > -1) {
                                other_comment = other_comment.replace('<br><br>', '<br>');
                                var comment = other_comment.split('<br>');

                                comment[0] = comment[0] +'<br>'+ href+'<br>';
                                other_comment = comment.join('<br>');
                            } else {
                                other_comment = other_comment + '<br>' + (href !== undefined ? href : '');
                            }
                        }

                        formContainer.find('.control-group').each(function(){
                            var self=$(this),
                                textarea = self.find('textarea'),
                                file = textarea.closest('.js_form_reviews'),
                                name=textarea.attr('name');

                            if(self.find('input[type="checkbox"]').is(':checked') || self.find('input[type="radio"]').is(':checked')){
                                values[name]=other_comment;

                                if(textarea.val().length || file.find('input[name="file_link"]').val()){
                                    var _file_name = file.find('input[name="file_name"]').val();
                                    var _file_link = file.find('input[name="file_link"]').val();
                                    var _href;

                                    if(_file_link !== undefined && _file_link !== '') {
                                        _href = ('<a target="_blank" href="' + _file_link + '">' + (_file_name !== undefined && _file_name !== '' ? _file_name : _file_link) + '</a>');
                                    }

                                    var val = textarea.val();

                                    if(val.indexOf('<br>') > -1 && _href !== undefined) {
                                        val = val.replace('<br><br>', '<br>');
                                        var val = val.split('<br>');
                                        
                                        val[0] = val[0]+'<br>'+_href+'<br>';
                                        values[name] = val.join('<br>');
                                    } else {
                                        values[name] = textarea.val() + '<br>' + (_href !== undefined ? _href : '');
                                    }
                                }
                            }

                            if(self.find('.status-input:checked').length){
                                values[self.find('.status-input:checked').attr('name')]=self.find('.status-input:checked').val();
                            }
                        });

                        formContainer.find('.input-parents').each(function(){
                            var self=$(this);
                            values2.push(self.val());
                        });

                        edit = formContainer.find('.input-edit').val();

                        if(cnt==0){
                            loader.spin(false).hide();
                            alert('Потрібно обрати мінімум 1 форму!');
                            return true;
                        }

                        _params=_paramsDefault;
                        _params.model='form';

                        $(this).attr('disabled', 'disabled');
                        $('.spinner').show();

                        $.ajax({
                            method: 'POST',
                            data: {
                                form: values,
                                parents: values2,
                                tender: _self.data('tender-id'),
                                schema: _self.data('schema'),
                                multy: (_self.data('multy') !== undefined ? 1 : 0),
                                tender_public_id: _self.data('tender-public-id'),
                                parent: formParent ? formParent : '',
                                edit: edit
                            },
                            url: '/jsonforms/'+_params.model,
                            dataType: 'json',
                            headers: csrf(),
                            success: function (response) {
                                loader.spin(false).hide();

                                if (response) {
                                    formSuccess.show();
                                    formContainer.hide();

                                    formContainer.find('textarea').each(function(){
                                        $(this).val('');
                                    });

                                    window.location = _self.data('return-back');

                                } else {
                                    formError.show();
                                    formContainer.find('form').addClass('hidden');

                                    setTimeout(function () {
                                        formError.hide();
                                        formContainer.find('form').removeClass('hidden');
                                    }, 4000);
                                }

                                $(this).removeAttr('disabled');
                                $('.spinner').hide();
                            }
                        });
                    });
                    /*
                    $(document).on('click', '[data-formjs="ngoJsonForm"]', function(e){
                        e.preventDefault();

                        $('.add-ngo-review-form').popup({
                            transition: 'all 0.3s'
                        });

                        $('#ngo_review_form').popup('show');

                        _extraValues={
                            parentForm: $(this).data('parentForm')
                        };

                        formSelector.show();
                        formContainer.empty();
                        formToolbar.empty();
                        formError.hide();
                        formSuccess.hide();

                        var generateCounter=0;

                        _params=$(this).data();
                        _params=$.extend(_params, _paramsDefault);

                        formTitle.html(_params.formTitle ? _params.formTitle : formTitleDefault);
                        submitCounter=0;

                        generateForms(function(formSchema, form){
                            formSelector.hide();
                            generateCounter++;

                            formContainer.append(form);
                            form.jsonForm(formSchema);

                            form.append('<input type="submit" value="'+_params.submitButton+'">');

                            if(generateCounter==formsCount() && isMultiForm()){
                                initMultiFormAccordeon();
                            }

                            if(_params.generate && typeof generators[_params.generate]=='function'){
                                generators[_params.generate]();
                            }
                        });
                    });
                    */
                },
                ngo_tender_tabs: function(_self){
                    var tabs=$('.reviews_ngo_tab a'),
                        tab=_self.closest('[tab-content]'),
                        reviews=$('.ngo-form-action');

                    tabs.click(function(e){
                        e.preventDefault();
                        tabs.removeClass('active');

                        var self=$(this);

                        self.addClass('active');
                        reviews.hide();

                        $('.ngo-form-action.form-'+self.data('schema')).show();
                        $('.ngo-form-action[data-schema*="|'+self.data('schema')+'|"]').show();

                        window.location.hash='ngo-'+self.data('hash2');
                    });

                    tabs.each(function(){
                        var self=$(this);

                        if($('.ngo-form-action[data-schema*="|'+self.data('schema')+'|"]').length==0){
                            self.remove();
                        }
                    });

                    if(HASH[1] && HASH[1]!=''){
                        $('.reviews_ngo_tab a[data-hash2="'+HASH[1]+'"]').click();
                    }

                },
                ngo_open_modal: function(_self){
                    _self.click(function(e) {
                        e.preventDefault();

                        $('[data-form-modal="'+_self.data('form')+'"]')
                            .css('display', 'block');

                        $('.overlay2[data-form-modal="'+_self.data('form')+'"]')
                            .animate({opacity: 0.5});

                        $('.modal_div[data-form-modal="'+_self.data('form')+'"]')
                            .animate({opacity: 1});
                    });
                },
                ngo_open_multi_form: function(_self){
                    _self.click(function(e) {
                        e.preventDefault();

                        if($(this).is('.disabled')) {
                            return false;
                        }

                        var ids = [];

                        $('#result').find('input[type="checkbox"]:checked').each(function() {
                            var self = $(this);
                            ids.push(self.data('tender-public-id'));
                        });

                        if(ids.length <= 0) {
                            alert('Потрібно обрати мінімум 1 тендер!');
                            return false;
                        }

                        window.location = _self.data('href')+'/'+ids.join();
                    });
                },
                tender_open_form: function(_self){
                    _self.click(function(e) {
                        e.preventDefault();

                        var _schema = true;
                        var schema = '';
                        var form = false;

                        $('.form_ngo').find('input[type="checkbox"]:checked').each(function() {
                            var self = $(this);
                                schema = self.data('schema');
                                form = self.closest('form');

                            form.children('.hidden-input').attr('name', '');
                            form.children('.hidden-input').val('');

                            if(_schema === true) {
                                _schema = schema;
                            }

                            if(_self.data('schema') < self.data('min-schema') || (_schema !== schema && _schema !== false)) {
                                _schema = false;
                            }
                        });

                        var check_val;

                        if(_self.data('schema') == '204' && $('.form_ngo').find('input[type="checkbox"]:checked').length <= 0) {
                            check_val = $('.form_ngo').find('input[type="checkbox"]:first').val();
                            form = $('.form_ngo').find('input[type="checkbox"]:first').closest('form');
                            form.children('.hidden-input').attr('name', 'parents[]');
                            form.children('.hidden-input').val(check_val);
                        } else if(_schema === false || !form) {
                            alert('Невірно обрані форми!');
                            return false;
                        }

                        form.attr('action', _self.data('href')).submit();
                    });
                },
                user_type: function(_self){
                    $.ajaxSetup({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                    });

                    $(document).on('click', '#user-type-form_background', function() {
                       return false;
                    });

                    $('#user-type-form').popup({
                        transition: 'all 0.3s',
                        escape: false,
                        onclose: function() {
                            //$('#review_form2').popup('show');
                            $.ajax({
                                method: 'POST',
                                url: '/api/user_type/defer',
                                data: {},
                                dataType: 'json',
                                success: function(resp) {
                                    //window.location.reload(true);
                                }
                            });
                        }
                    });

                    setTimeout(function() {
                        $('#user-type-form').popup('show');
                        $('#user-type-form').parent().addClass('popup_wrapper_user_type');
                    }, 120000);

                    _self.on('click', 'input', function() {
                        $.ajax({
                            method: 'POST',
                            url: '/api/user_type',
                            data: {
                               auth: $(this).closest('#user-type-form').data('auth'),
                               user_type: $(this).val()
                            },
                            dataType: 'json',
                            success: function(resp) {
                                window.location.reload(true);
                            }
                        });
                    });
                },
                open_login: function(_self){
                    _self.click(function(e) {
                        $('[data-profile]').hide();
                        e.preventDefault();

                        $('.add-review-form').popup({
                            transition: 'all 0.3s'
                        });
                        $('#review_form2').popup('show');
                    });
                },
                open: function(_self){
                    _self.click(function(e) {
                        e.preventDefault();

                        $('.add-review-form').popup({
                            transition: 'all 0.3s'
                        });

                        _extraValues={};

                        formSelector.show();
                        formContainer.empty();
                        formToolbar.empty();
                        formError.hide();
                        formSuccess.hide();
                    });

                    $(document).on('click', '[form-comment]', function(e){
                        e.preventDefault();

                        _extraValues={};
                        _extraValues.thread=$(this).data('thread');

                        formSelector.show();
                        formContainer.empty();
                        formToolbar.empty();
                        formError.hide();
                        formSuccess.hide();

                        $('#review_form').popup('show');
                    });

                    $(document).on('click', '[data-formjs="jsonForm"]', function(e){
                        e.preventDefault();

                        var generateCounter=0;

                        _params=$(this).data();
                        _params=$.extend(_params, _paramsDefault);

                        formTitle.html(_params.formTitle ? _params.formTitle : formTitleDefault);
                        submitCounter=0;

                        generateForms(function(formSchema, form){
                            formSelector.hide();
                            generateCounter++;

                            formContainer.append(form);
                            form.jsonForm(formSchema);

                            form.append('<input type="submit" value="'+_params.submitButton+'">');

                            if(generateCounter==formsCount() && isMultiForm()){
                                initMultiFormAccordeon();
                            }

                            if(_params.generate && typeof generators[_params.generate]=='function'){
                                generators[_params.generate]();
                            }
                        });
                    });
                }
            }
        };

        return methods;
    }());

    $(function (){
        $('[data-formjs]').each(function(){
            var self = $(this);

            if (typeof FORMS.js[self.data('formjs')] === 'function'){
                FORMS.js[self.data('formjs')](self, self.data());
            } else {
                console.log('No `' + self.data('formjs') + '` function in app.js');
            }
        });
    });
})(window);
