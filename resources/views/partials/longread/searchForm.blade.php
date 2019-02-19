@if(!empty($data->search_is_short))
    <form class="sb-s" action="{{ route('search', ['search' => 'tender']) }}" id="c-find-form" novalidate="novalidate">
        <div class="row">
            <div class="col-md-3">
                @if (!empty($data->search_form_title))
                    <p class="sb-s__h">{{ $data->search_form_title }}</p>
                @endif
            </div>
            <div class="col-md-6 clearfix" data-js="disableSearchButton">
                <div class="sb-s__input sb-s__input--left">
                    <input id="tender-number" type="text" name="tid" class="jsGetInputVal" autocomplete="off" placeholder="UA-2016-01-01-000001">
                    <div class="sb-s__or">{{t('search.or')}}</div>
                </div>
                <div class="sb-s__input sb-s__input--right">
                    <input id="tender-customer" type="text" name="edrpou" class="jsGetInputVal" autocomplete="off" placeholder="{{t('search.customer')}}" data-js="customer_search">
                </div>
                <div id="errordiv" style="z-index: 9;color: black;"></div>
            </div>
            <div class="col-md-3 clearfix">
                <input id="btn-find" type="submit" value="{{t('search.search_something')}}" disabled="">
            </div>
        </div>
    </form>
    <script>

        $('.jsTenderTabs .tender-tabs__item').click(function() {
            var index=$(this).index('.tender-tabs__item');
            $('[tab-content]').hide();
            $('[tab-content]').eq(index).show();
            $('.jsTenderTabs .tender-tabs__item').removeClass('is-show');

            $(this).addClass('is-show');
        });

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
            $(".tender-header__review-button").sticky({topSpacing:20});
            $(".tender-tabs-wrapper").sticky({topSpacing:0});
        });

    </script>

@else
    <div class="search-form">
        <div class="main-search">
            <div class="container">
                {{--
                <div class="search-form--category">
                    <ul class="nav navbar-nav inline-navbar">
                        <li><a @if ($search_type=='tender') class="active"@endif href="/tender/search">{{t('form.tenders')}}</a></li>
                        <li><a @if ($search_type=='plan') class="active"@endif href="/plan/search">{{t('form.plans')}}</a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                --}}

                <div class="blocks-wr">
                    <div id="blocks" class="blocks clearfix">
                        <input id="query" class="query_input no_blocks" type="text" autocomplete="off" data-js="form" data-type="{{$search_type}}" data-lang="{{Config::get('locales.href')}}" data-no-results="{{Config::get('form.no_results')}}" data-buttons="{{Config::get('prozorro.buttons.'.$search_type)}}" data-placeholder="{{t('form.placeholder')}}"@if (!empty($preselected_values)) data-preselected='{{$preselected_values}}'@endif @if (!empty($preselected_values)) data-highlight='{{$highlight}}'@endif>
                        <button id="search_button" class="more" disabled></button>
                    </div>
                    <div id="suggest" class="suggest"></div>
                </div>
                <div class="search-form--filter mob-visible none-important" mobile-totals>
                    <div class="result-all"><a href="" class="result-all-link">{{t('form.resuts_found')}} <span></span>. {{t('form.resuts_show')}}</a></div>
                </div>
                <div class="search-form--add-cryteria">
                    <div class="nav navbar-nav inline-navbar">
                        <div id="buttons" class="buttons"></div>
                    </div>
                    <a id="print-list" href="" target="_blank" class="none pull-right">{{t('longreads.print_form')}}</a>
                </div>
            </div>
        </div>
        <div class="main-result" data-js="search_result">
            <div class="container">
                <div class="row">
                    @if(\App\Classes\User::isAccessToNgo())
            			<div class="ngo-buttons-container" id="ngo_open_multi_form" style="display:none">
        					<a id="ngo_open_multi_form_f203" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" data-href="{{ route('page.ngo_multy_form', ['form' => 'F203', 'tender_ids' => '']) }}">{{t('tender.multi_button_f203')}}</a>
        					<a id="ngo_open_multi_form_f202" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" style="margin-right:10px" data-href="{{ route('page.ngo_multy_form', ['form' => 'F202', 'tender_ids' => '']) }}">{{t('tender.multi_button_f202')}}</a>
        					<a id="ngo_open_multi_form_f201" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" style="margin-right:10px" data-href="{{ route('page.ngo_multy_form', ['form' => 'F201', 'tender_ids' => '']) }}">{{t('tender.multi_button_f201')}}</a>
        					<a id="ngo_open_multi_form_select_all" class="ngo_open_multi_form_button_white tender-header__link review_form_open" data-href="{{ route('page.ngo_multy_form', ['form' => 'F203', 'tender_ids' => '']) }}">{{t('tender.multi_select_all')}}</a>
        				</div>
                    @endif
                </div>
            </div>
            <div id="result" class="result">
                @if (!empty($result))
                    {!!$result!!}
                @endif
            </div>
        </div>
    </div>

    <script id="helper-suggest" type="text/x-jquery-tmpl">
    <div class="none"><a href>{name}: <span class="highlight">{value}</span></a></div>
    </script>

    <script id="helper-button" type="text/x-jquery-tmpl">
    <button>{name}</button>
    </script>

    <script id="block-query" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.keyword')}}" data-button-name="{{t('form.keyword_short')}}">
    <div class="block block-query"><span class="block-key">{{t('form.keyword_short')}}</span><input type="text" value="{value}"></div>
    </script>

    <script id="block-cpv" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.cpv')}}" data-button-name="{{t('form.cpv')}}">
    <div class="block block-cpv"><button class="none">{{t('form.choose')}}&nbsp;(<span></span>)</button><span class="block-key">{{t('form.cpv')}}</span><select /></div>
    </script>

    <script id="block-dkpp" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.dkpp')}}" data-button-name="{{t('form.dkpp')}}">
    <div class="block block-dkpp"><button class="none">{{t('form.choose')}}&nbsp;(<span></span>)</button><span class="block-key">{{t('form.dkpp')}}</span><select /></div>
    </script>

    <script id="block-date" type="text/x-jquery-tmpl" data-types='{!!json_encode(t('form.date_types'), JSON_UNESCAPED_UNICODE)!!}' data-button-name="{{t('form.date')}}">
    <div class="block block-date dateselect"><a href class="block-date-arrow"></a><div class="block-date-tooltip"></div><span class="block-key"></span><div class="block-date-picker"><input class="date start" type="text">—<input class="date end" class="text"></div></div>
    </script>

    <script id="block-dateplan" type="text/x-jquery-tmpl" data-types='{!!json_encode(t('form.date_types_plan'), JSON_UNESCAPED_UNICODE)!!}' data-button-name="{{t('form.date')}}">
    <div class="block block-date dateselect"><a href class="block-date-arrow"></a><div class="block-date-tooltip"></div><span class="block-key"></span><div class="block-date-picker"><input class="date start" type="text">—<input class="date end" class="text"></div></div>
    </script>

    <script id="block-edrpou" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.customer')}}" data-button-name="{{t('form.customer')}}">
    <div class="block block-edrpou"><span class="block-key">{{t('form.customer')}}</span><select /></div>
    </script>

    <script id="block-region" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.region')}}" data-button-name="{{t('form.region')}}">
    <div class="block block-region"><span class="block-key">{{t('form.region')}}</span><select /></div>
    </script>

    <script id="block-procedure_p" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.procedure_p')}}" data-button-name="{{t('form.type')}}">
    <div class="block block-procedure_p"><span class="block-key">{{t('form.procedure_p')}}</span><select /></div>
    </script>

    <script id="block-procedure_t" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.procedure_t')}}" data-button-name="{{t('form.type')}}">
    <div class="block block-procedure_t"><span class="block-key">{{t('form.procedure_t')}}</span><select /></div>
    </script>

    <script id="block-status" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.status')}}" data-button-name="{{t('form.status')}}">
    <div class="block block-status"><span class="block-key">{{t('form.status')}}</span><select /></div>
    </script>

    <script id="block-tid" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.tenderid')}}" data-button-name="{{t('form.tenderid')}}">
    <div class="block block-tid"><span class="block-key">{{t('form.tenderid')}}</span><input type="text" value="{value}"></div>
    </script>

    <script id="block-pid" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.planid')}}" data-button-name="{{t('form.planid')}}">
    <div class="block block-tid"><span class="block-key">{{t('form.planid')}}</span><input type="text" value="{value}"></div>
    </script>

	<script id="block-value" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.value')}}" data-button-name="{{t('form.value')}}">
    	<div class="block block-value">
    		<span class="block-key">{{ t('form.value') }}</span>
            <input class="value from" type="text">&nbsp;—&nbsp;<input class="value to" type="text">
            <span class="block-comment">{{ t('form.uah') }}</span>
        </div>
	</script>
@endif
