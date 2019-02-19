<div class="search-form bg_white">
	<div class="main-search">
		<h3 class="text-center">{{t('indicators.header_text')}}</h3>
		<div class="">

			<div class="blocks-wr">
				<div id="blocks" class="blocks clearfix">
					<input data-route="/indicators/search" id="query" class="query_input no_blocks" type="text" autocomplete="off" data-js="form" data-type="tender" data-lang="{{Config::get('locales.href')}}" data-no-results="{{Config::get('form.no_results')}}" data-buttons="{{Config::get('prozorro.buttons.indicator')}}" data-placeholder="{{t('form.placeholder')}}"@if (!empty($preselected_values)) data-preselected='{{$preselected_values}}'@endif @if (!empty($preselected_values)) data-highlight='{{$highlight}}'@endif>
					<button id="search_button" class="more" disabled></button>
				</div>
				<div id="suggest" class="suggest"></div>
			</div>
			<div class="search-form--filter mob-visible none-important" mobile-totals>
				<div class="result-all"><a href="" class="result-all-link">{{t('indicators.resuts_found')}} <span></span>. {{t('indicators.resuts_show')}}</a></div>
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

<script id="block-risk" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.risk')}}" data-button-name="<span class='button_purple'>{{t('form.risk')}}</span>">
<div class="block block-risk"><span class="block-key">{{t('form.risk')}}</span><select /></div>
</script>

<script id="block-cpv" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.cpv')}}" data-button-name="<span class='button_silver'>{{t('form.cpv')}}</span>">
<div class="block block-cpv"><button class="none">{{t('form.choose')}}&nbsp;(<span></span>)</button><span class="block-key">{{t('form.cpv')}}</span><select /></div>
</script>

<script id="block-date" type="text/x-jquery-tmpl" data-types='{!!t('form.date_types')!!}' data-button-name="<span class='button_silver'>{{t('form.date')}}</span>">
<div class="block block-date dateselect"><a href class="block-date-arrow"></a><div class="block-date-tooltip"></div><span class="block-key"></span><div class="block-date-picker"><input class="date start" type="text">—<input class="date end" class="text"></div></div>
</script>

<script id="block-dateplan" type="text/x-jquery-tmpl" data-types='{!!t('form.date_types_plan')!!}' data-button-name="<span class='button_silver'>{{t('form.date')}}</span>">
<div class="block block-date dateselect"><a href class="block-date-arrow"></a><div class="block-date-tooltip"></div><span class="block-key"></span><div class="block-date-picker"><input class="date start" type="text">—<input class="date end" class="text"></div></div>
</script>

<script id="block-edrpou" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.customer')}}" data-button-name="<span class='button_silver'>{{t('form.customer')}}</span>">
<div class="block block-edrpou"><span class="block-key">{{t('form.customer')}}</span><select /></div>
</script>

<script id="block-region" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.region')}}" data-button-name="<span class='button_silver'>{{t('form.region')}}</span>">
<div class="block block-region"><span class="block-key">{{t('form.region')}}</span><select /></div>
</script>

<script id="block-procedure_p" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.procedure_p')}}" data-button-name="<span class='button_silver'>{{t('form.type')}}</span>">
<div class="block block-procedure_p"><span class="block-key">{{t('form.procedure_p')}}</span><select /></div>
</script>

<script id="block-procedure_t" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.procedure_t')}}" data-button-name="<span class='button_silver'>{{t('form.type')}}</span>">
<div class="block block-procedure_t"><span class="block-key">{{t('form.procedure_t')}}</span><select /></div>
</script>

<script id="block-status" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.status')}}" data-button-name="<span class='button_silver'>{{t('form.status')}}</span>">
<div class="block block-status"><span class="block-key">{{t('form.status')}}</span><select /></div>
</script>

<script id="block-tid" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.tenderid')}}" data-button-name="<span class='button_silver'>{{t('form.tenderid')}}</span>">
<div class="block block-tid"><span class="block-key">{{t('form.tenderid')}}</span><input type="text" value="{value}"></div>
</script>

<script id="block-value" type="text/x-jquery-tmpl" data-suggest-name="{{t('form.value')}}" data-button-name="<span class='button_silver'>{{t('form.value')}}</span>">
	<div class="block block-value">
		<span class="block-key">{{ t('form.value') }}</span>
        <input class="value from" type="text">&nbsp;—&nbsp;<input class="value to" type="text">
        <span class="block-comment">{{ t('form.uah') }}</span>
    </div>
</script>
