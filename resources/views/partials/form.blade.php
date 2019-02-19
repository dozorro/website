<div class="search-form">
	<div class="main-search">
		<h3 class="text-center">{{t('tender.header_text')}}</h3>
		<div class="container">
			{{--
			<div class="search-form--category">
				<ul class="nav navbar-nav inline-navbar">
					<li><a @if ($search_type=='tender') class="active"@endif href="/tender/search/">{{t('form.tenders')}}</a></li>
					<li><a @if ($search_type=='plan') class="active"@endif href="/plan/search/">{{t('form.plans')}}</a></li>
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
                <a id="print-list" href="" target="_blank" class="none pull-right">Друкувати форму</a>
			</div>
		</div>
	</div>
	<div class="main-result" data-js="search_result">

		<div class="container bg_white">
			@if($user && $user->ngo)
				<div class="ngo-buttons-container" id="ngo_open_multi_form" style="{{ empty($result) ? 'display:none' : '' }}">
					<a id="ngo_open_multi_form_f203" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" data-href="{{ route('page.ngo_multy_form', ['form' => 'F203', 'tender_ids' => '']) }}">{{t('tender.multi_button_f203')}}</a>
					<a id="ngo_open_multi_form_f202" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" style="margin-right:10px" data-href="{{ route('page.ngo_multy_form', ['form' => 'F202', 'tender_ids' => '']) }}">{{t('tender.multi_button_f202')}}</a>
					<a id="ngo_open_multi_form_f201" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" style="margin-right:10px" data-href="{{ route('page.ngo_multy_form', ['form' => 'F201', 'tender_ids' => '']) }}">{{t('tender.multi_button_f201')}}</a>
					<a id="ngo_open_multi_form_select_all" class="ngo_open_multi_form_button_white tender-header__link review_form_open" style="margin-right:10px" data-href="{{ route('page.ngo_multy_form', ['form' => 'F203', 'tender_ids' => '']) }}">{{t('tender.multi_select_all')}}</a>
				</div>
			@endif

			<div id="result" class="result">
				@if (!empty($result))
					{!!$result!!}
				@endif
			</div>
		</div>

		<div class="button-show-more" style="text-align: center;">
		</div>


	</div>
</div>

@push('scripts')
<script>
	$(function () {

		var b = $('#result').find('button');

		if(b !== undefined) {
			$('.button-show-more').append(b.clone());
			b.remove();
		}
	});
</script>
@endpush

<script id="helper-suggest" type="text/x-jquery-tmpl">
<div class="none"><a href>{name}: <span class="highlight">{value}</span></a></div>
</script>

<script id="helper-button" type="text/x-jquery-tmpl">
<button>{name}</button>
</script>

<script id="block-query" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.keyword')}}" data-button-name="<span>{{t('form.keyword_short')}}</span>" >
<div class="block block-query"><span class="block-key">{{t('form.keyword_short')}}</span><input type="text" value="{value}"></div>
</script>

<script id="block-cpv" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.cpv')}}" data-button-name="<span class='button_purple'>{{t('form.cpv')}}</span>">
<div class="block block-cpv"><button class="none">{{t('form.choose')}}&nbsp;(<span></span>)</button><span class="block-key">{{t('form.cpv')}}</span><select /></div>
</script>

<script id="block-dkpp" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.dkpp')}}" data-button-name="<span>{{t('form.dkpp')}}</span>">
<div class="block block-dkpp"><button class="none">{{t('form.choose')}}&nbsp;(<span></span>)</button><span class="block-key">{{t('form.dkpp')}}</span><select /></div>
</script>

<script id="block-date" type="text/x-jquery-tmpl" data-types='{!!t('form.date_types')!!}' data-button-name="<span class='button_purple'>{{t('form.date')}}</span>">
<div class="block block-date dateselect"><a href class="block-date-arrow"></a><div class="block-date-tooltip"></div><span class="block-key"></span><div class="block-date-picker"><input class="date start" type="text">—<input class="date end" class="text"></div></div>
</script>

<script id="block-dateplan" type="text/x-jquery-tmpl" data-types='{!!t('form.date_types_plan')!!}' data-button-name="<span class='button_purple'>{{t('form.date')}}</span>">
<div class="block block-date dateselect"><a href class="block-date-arrow"></a><div class="block-date-tooltip"></div><span class="block-key"></span><div class="block-date-picker"><input class="date start" type="text">—<input class="date end" class="text"></div></div>
</script>

<script id="block-edrpou" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.customer')}}" data-button-name="<span>{{t('form.customer')}}</span>">
<div class="block block-edrpou"><span class="block-key">{{t('form.customer')}}</span><select /></div>
</script>

<script id="block-region" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.region')}}" data-button-name="<span class='button_pink'>{{t('form.region')}}</span>">
<div class="block block-region"><span class="block-key">{{t('form.region')}}</span><select /></div>
</script>

<script id="block-procedure_p" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.procedure_p')}}" data-button-name="<span >{{t('form.type')}}</span>">
<div class="block block-procedure_p"><span class="block-key">{{t('form.procedure_p')}}</span><select /></div>
</script>

<script id="block-procedure_t" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.procedure_t')}}" data-button-name="<span>{{t('form.type')}}</span>">
<div class="block block-procedure_t"><span class="block-key">{{t('form.procedure_t')}}</span><select /></div>
</script>

<script id="block-status" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.status')}}" data-button-name="<span class='button_purple'>{{t('form.status')}}</span>">
<div class="block block-status"><span class="block-key">{{t('form.status')}}</span><select /></div>
</script>

<script id="block-tid" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.tenderid')}}" data-button-name="<span class='button_pink'>{{t('form.tenderid')}}</span>">
<div class="block block-tid"><span class="block-key">{{t('form.tenderid')}}</span><input type="text" value="{value}"></div>
</script>

<script id="block-pid" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.planid')}}" data-button-name="<span>{{t('form.planid')}}</span>">
<div class="block block-tid"><span class="block-key">{{t('form.planid')}}</span><input type="text" value="{value}"></div>
</script>

<script id="block-value" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.value')}}" data-button-name="<span class='button_pink'>{{t('form.value')}}</span>">
	<div class="block block-value">
		<span class="block-key">{{ t('form.value') }}</span>
        <input class="value from" type="text">&nbsp;—&nbsp;<input class="value to" type="text">
        <span class="block-comment">{{ t('form.uah') }}</span>
    </div>
</script>
