<div class="search-form">
	<div class="main-search p-t-30 p-b-30">

		<div class="container">
			<div class="text-center p-b-30">
				<img src="/assets/images/medical_contracts.png" />
			</div>
			<h3 class="text-center p-b-30">{{t('medical.header_text')}}</h3>
			<div class="blocks-wr">
				<div id="blocks" class="blocks clearfix">
					<input data-route="/medical_contracts/search" id="query" class="query_input no_blocks" type="text" autocomplete="off" data-js="form" data-type="tender" data-lang="{{Config::get('locales.href')}}" data-no-results="{{Config::get('form.no_results')}}" data-buttons="{{Config::get('prozorro.buttons.contract')}}" data-placeholder="{{t('form.placeholder')}}"@if (!empty($preselected_values)) data-preselected='{{$preselected_values}}'@endif @if (!empty($preselected_values)) data-highlight='{{$highlight}}'@endif>
					<button id="search_button" class="more" disabled></button>
				</div>
				<div id="suggest" class="suggest"></div>
			</div>
			<div class="search-form--filter mob-visible none-important" mobile-totals>
				<div class="result-all"><a href="" class="result-all-link">{{t('medical.results_found')}} <span></span>. {{t('medical.results_show')}}</a></div>
            </div>
			<div class="search-form--add-cryteria">
				<div class="nav navbar-nav inline-navbar">
					<div id="buttons" class="buttons"></div>
				</div>
                <a id="print-list" href="" target="_blank" class="none pull-right">Друкувати форму</a>
			</div>
		</div>
	</div>
	{{--<div class="main-result">

	</div>--}}
</div>

<script id="helper-suggest" type="text/x-jquery-tmpl">
<div class="none"><a href>{name}: <span class="highlight">{value}</span></a></div>
</script>

<script id="helper-button" type="text/x-jquery-tmpl">
<button>{name}</button>
</script>

<script id="block-measure" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.measure')}}" data-button-name="<span>{{t('form.measure')}}</span>">
<div class="block block-measure"><span class="block-key">{{t('form.measure')}}</span><select /></div>
</script>

<script id="block-product_name" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.product_name')}}" data-button-name="<span>{{t('form.product_name')}}</span>">
<div class="block block-product_name"><span class="block-key">{{t('form.product_name')}}</span><select /></div>
</script>

<script id="block-product_name_other" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.product_other_name')}}" data-button-name="<span>{{t('form.product_other_name')}}</span>">
<div class="block block-product_name_other"><span class="block-key">{{t('form.product_other_name')}}</span><select /></div>
</script>

<script id="block-date" type="text/x-jquery-tmpl" data-types='{!!t('form.date_types')!!}' data-button-name="<span class='button_purple'>{{t('form.date')}}</span>">
<div class="block block-date dateselect"><a href class="block-date-arrow"></a><div class="block-date-tooltip"></div><span class="block-key"></span><div class="block-date-picker"><input class="date start" type="text">—<input class="date end" class="text"></div></div>
</script>

<script id="block-edrpou" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.customer')}}" data-button-name="<span>{{t('form.customer')}}</span>">
<div class="block block-edrpou"><span class="block-key">{{t('form.customer')}}</span><select /></div>
</script>

<script id="block-region" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.region')}}" data-button-name="<span class='button_pink'>{{t('form.region')}}</span>">
<div class="block block-region"><span class="block-key">{{t('form.region')}}</span><select /></div>
</script>

<script id="block-tid" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.tenderid')}}" data-button-name="<span class='button_pink'>{{t('form.tenderid')}}</span>">
<div class="block block-tid"><span class="block-key">{{t('form.tenderid')}}</span><input type="text" value="{value}"></div>
</script>