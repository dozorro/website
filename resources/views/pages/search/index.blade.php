@extends('layouts.app')

@section('content')
<div>
    <div class="bg_grey page_risk page_indicator_search" id="vue-indicators" data-is-mobile="{{ $isMobile }}" data-filters="true" data-api-url="{{ route('search.tenders') }}">
        <spinner></spinner>
        <div class="container">
            <div class="col-md-9 indicator-search" id="tenders-table">
                <div class="title">{{ t('tender.search.title') }}</div>
                <div class="indicator-search-form">
                    <form>
                        <input value="{{ @$filters['tid'][0] }}" type="text" id="searchByTid" name="tid">
                        <button v-on:click.prevent="search()" class="search-btn">{{ t('tender.search.submit') }}</button>
                    </form>
                    <div class="search-result-status buttons">
                        <button class="filter-modal-btn">{{ t('tender.search.sidebar_filter') }} <span id="filters-count"></span></button>
                        <div class="search-result-status" id="total-tenders">
                            <span id="total-tenders-span" class="hide">&nbsp;{{ t('tender.search.total') }}&nbsp;<span v-html="total">0</span></span>
                        </div>
                        @include('partials.search._sort')
                    </div>
                </div>

                <div class="indicator-search-results">

                    @if(!$examples->isEmpty())
                        <div class="example-requests" v-if="!tenders.length">
                            @foreach($examples as $example)
                                <a style="font-size: {{ $example->font_size }}px;" v-on:click.prevent="exampleRequest('{{ $example->request }}')" href="#">{{ $example->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    <div id="selected-filters" style="text-align: center;">
                        <span id="selected-filters-span" style="display:none;margin: 0 auto;">{{ t('tender.search.profileURL') }}</span>
                    </div>

                    <table v-if="tenders.length">
                        <thead>
                        <tr>
                            @if($user && $user->ngo)
                                <th class="checkbox-col">
                                    <div v-if="tender">
                                        <input type="checkbox" id="ngo_open_multi_form_select_all" class="ngo_open_multi_form_button_white tender-header__link review_form_open" data-href="{{ route('page.ngo_multy_form', ['form' => 'F203', 'tender_ids' => '']) }}">
                                    </div>
                                </th>
                            @endif
                            <th class="title-col">
                                <span>{{ t('indicators.result.customer') }}</span>
                            </th>
                            <th class="subject-col">{{ t('indicators.result.title') }}</th>
                            <th class="price-col">{{ t('indicators.result.sum') }}</th>
                            @if(!$isMobile)
                                <th class="go" style="text-align: center;">{{ t('tender.search.tenderID.URL') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, key) in tenders" v-on:click="showSidebar(key, true)" v-bind:class="{ 'selected': tenderKey == key }">
                            @if($user && $user->ngo)
                                <td  class="checkbox-col">
                                    <div v-if="tender">
                                        <input class="ngo-checkbox" v-bind:id="'tender-'+item.tenderID" v-bind:data-f201="item.__is_F201?'1':'0'" v-bind:data-tender-public-id="item.tenderID" type="checkbox">
                                    </div>
                                </td>
                            @endif
                            <td  class="title-col">
                                <a class="maxheight-tender" target="_blank" :href="item.tender_route_role" v-html="item.procuringEntity.name"></a>
                            </td>
                            <td  class="subject-col">
                                <span class="maxheight-tender" v-html="item.title"></span>
                            </td>
                            <td v-html="item.price"  class="price-col"></td>
                            @if(!$isMobile)
                            <td class="go ngo-red" v-on:click="window.open('/tender/'+item.tenderID,'_blank');"></td>
                            @endif
                        </tr>
                        </tbody>
                    </table>
                    <div v-if="pagination" v-bind:class="{ 'link_pagination': true, 'loading': nextPageLoading }" v-on:click="nextPage()"><spinner size="small"></spinner>{{ t('indicators.show_more') }}</div>
                </div>
            </div>
            <div id="sidebar" class="col-md-3" v-bind:data-position="tenderKey" v-if="tender" v-sticky="{ zIndex: 1, stickyTop: 20 }">
                <div>
                    <button class="tender-content-close @if($isMobile){{'desktop-hidden'}}@endif" v-on:click="closeSidebar()"></button>
                    @if($isMobile)
                        <div class="tender-header-siblings-switcher">
                            <button class="tender-link previous-tender" v-on:click="showTender('prev')" >
                                <span>←</span>&nbsp;<span>{{ t('tender.search.prev') }}</span>
                            </button>
                            <button class="tender-content-close" v-on:click="closeSidebar()">
                                назад до пошуку
                            </button>
                            <button class="tender-link next-tender" v-on:click="showTender('next')">
                                <span>{{ t('tender.search.next') }}</span>&nbsp;<span>→</span>
                            </button>
                        </div>
                        <div class="tender-header-modal-search-results">
                            <div class="search-result-status" id="total-tenders">
                                {{-- <span>{{ t('tender.search.total') }}</span>&nbsp; --}}
                                <span v-html="tenderKey+1">0</span>/<span v-html="total">0</span>
                            </div>
                        </div>
                    @endif
                    <div class="tender-header-banner" ref="sidebarScroll">
                        @include('partials.sidebar.vue.common')
                        @include('partials.sidebar.vue.procedure')
                        @include('partials.sidebar.vue.dates')
                        @if($riskAccess)
                            @include('partials.sidebar.vue.risks')
                        @endif
                        @include('partials.sidebar.vue.customer')
                        @include('partials.sidebar.vue.features')
                        @include('partials.sidebar.vue.documents')
                        @include('partials.sidebar.vue.questions')
                        @include('partials.sidebar.vue.reviews')
                        @include('partials.sidebar.vue.complaints')
                        <div v-if="!tender.__isMultiLot">
                            @include('partials.sidebar.vue.items')
                            @include('partials.sidebar.vue.prequalifications')
                            @include('partials.sidebar.vue.qualifications')
                            @include('partials.sidebar.vue.contracts')
                        </div>
                        <div v-if="tender.__isMultiLot">
                            @include('partials.sidebar.vue.lots')
                        </div>
                        @include('partials.sidebar.vue.ngo')

                        @if($user && $user->ngo)
                            <div class="tender-header__descriptions tender-header-block">
                                <div v-bind:class="{ 'tender-header__wrap tender-header__descr': true, 'toggled': toggled.ngo_forms }">
                                    <button class="tender-header__descr-toggle" v-on:click="toggle('ngo_forms')"></button>
                                    <div class="block-title block-title">{{ t('indicators.ngo_forms') }}</div>
                                    <div class="tender-header_info__item">
                                        <div class="ngo-buttons-container" id="ngo_open_multi_form" style="width: 100%;text-align: center;">
                                            <a id="ngo_open_multi_form_f203" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" data-href="{{ route('page.ngo_multy_form', ['form' => 'F203', 'tender_ids' => '']) }}">{{t('tender.multi_button_f203')}}</a>
                                            <a id="ngo_open_multi_form_f202" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" data-href="{{ route('page.ngo_multy_form', ['form' => 'F202', 'tender_ids' => '']) }}">{{t('tender.multi_button_f202')}}</a>
                                            <a id="ngo_open_multi_form_f201" data-formjs="ngo_open_multi_form" class="ngo_open_multi_form_button tender-header__link review_form_open disabled" data-href="{{ route('page.ngo_multy_form', ['form' => 'F201', 'tender_ids' => '']) }}">{{t('tender.multi_button_f201')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @include('partials.sidebar.vue.form')
                        @include('partials.sidebar.vue.feedback')

                    </div>
                </div>
            </div>
        </div>
        <div class="indicator-filter-modal">
            <div class="indicator-filter-overlay"></div>
            <div class="indicator-filter-container">
                <div class="indicator-filter-body">
                    <button class="close" id="closeFilters"></button>
                    {{--<div class="filter-head">
                        <div class="title">{{ t('tender.search.filters_title') }}</div>
                    </div>--}}
                    <div class="filter-items">

                        <div class="filter-item tags-selector hide top-tags">
                            <div class="selected-tags cpv-data-selected hide" data-type="cpv">
                                @if(!empty($filters['cpv_like']))
                                    @foreach($filters['cpv_like'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $id.' '.$item }}</button>
                                    @endforeach
                                @endif
                                @if(!empty($filters['cpv']))
                                    @foreach($filters['cpv'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $id.' '.$item }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <div class="selected-tags edrpou-data-selected hide" data-type="edrpou">
                                @if(!empty($filters['edrpou']))
                                    @foreach($filters['edrpou'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $item }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <div class="selected-tags tenderer_edrpou-data-selected hide" data-type="tenderer_edrpou">
                                @if(!empty($filters['tenderer_edrpou']))
                                    @foreach($filters['tenderer_edrpou'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                                @if(!empty($filters['tenderer_edrpou_all']))
                                    @foreach($filters['tenderer_edrpou_all'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <div class="selected-tags supplier_edrpou-data-selected hide" data-type="supplier_edrpou">
                                @if(!empty($filters['supplier_edrpou']))
                                    @foreach($filters['supplier_edrpou'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                                @if(!empty($filters['supplier_edrpou_all']))
                                    @foreach($filters['supplier_edrpou_all'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <div class="selected-tags supplier_active-data-selected hide" data-type="supplier_active">
                                @if(!empty($filters['supplier_active']))
                                    @foreach($filters['supplier_active'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <div class="selected-tags contract_active-data-selected hide" data-type="contract_active">
                                @if(!empty($filters['contract_active']))
                                    @foreach($filters['contract_active'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <div class="selected-tags region-selected hide" data-type="region">
                                @if(!empty($filters['region']))
                                    @foreach($filters['region'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $item }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <div class="selected-tags status-selected hide" data-type="status">
                                @if(!empty($filters['status']))
                                    @foreach($filters['status'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $item }}</button>
                                    @endforeach
                                @endif
                            </div>
                            @if($riskAccess)
                            <div class="selected-tags risks-selected hide" data-type="risks">
                                @if(!empty($filters['risk_code']))
                                    @foreach($risks as $risk)
                                        @if(in_array($risk->risk_code, $filters['risk_code']))
                                            <button class="selected-tag" data-id="{{ $risk->risk_code }}">{{ t('indicators.'.$risk->risk_title) }}</button>
                                        @endif
                                    @endforeach
                                @endif
                                @if(!empty($filters['risk_code_all']))
                                    @foreach($risks as $risk)
                                        @if(in_array($risk->risk_code, $filters['risk_code_all']))
                                            <button class="selected-tag" data-id="{{ $risk->risk_code }}">{{ t('indicators.'.$risk->risk_title) }}</button>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            @endif
                            <div class="selected-tags forms-selected hide" data-type="forms">
                                @if(!empty($filters['form_code']))
                                    @foreach($forms as $item)
                                        @if(in_array($item, $filters['form_code']))
                                            <button class="selected-tag" data-id="{{ $item }}">{{ t('tender.search.'.$item) }}</button>
                                        @endif
                                    @endforeach
                                @endif
                                @if(!empty($filters['form_code_all'])))
                                @foreach($forms as $item)
                                    @if(in_array($item, $filters['form_code_all']))
                                        <button class="selected-tag" data-id="{{ $item }}">{{ t('tender.search.'.$item) }}</button>
                                    @endif
                                @endforeach
                                @endif
                            </div>
                            <div class="selected-tags proc_type-selected hide" data-type="proc_type">
                                @if(!empty($filters['proc_type']))
                                    @foreach($procedures as $item)
                                        @if($item['selected'])
                                            <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                        @endif
                                    @endforeach
                                @endif
                            </div>

                            {{--<div class="filter-head" style="text-align: right;padding-right: 0;">
                                <button class="filter-reset">{{ t('tender.search.filters_reset') }}</button>
                            </div>--}}
                        </div>

                        {{--div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.key_word') }}</span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                <input value="{{ @$filters['query'][0] }}" type="text" id="searchByQ" name="q">
                            </div>
                        </div>
                        --}}

                        <div class="filter-item date-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.dates') }}</span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                <div class="input-group date">
                                    <input style="max-width: 150px;min-width: 100px;" type="text" value="" name="tender_start" id="date1" class="form-control" />
                                    <input style="max-width: 150px;min-width: 100px;margin-left: 25px;" type="text" value="" name="tender_end" id="date2" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.customers') }} <span data-filters-type="edrpou" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">

                                <input v-on:keyup="searchFilters('edrpou', 0)" value="" type="text" name="edrpou" autocomplete="off" placeholder="{{ t('tenders.form.customer_placeholder') }}">

                                <div id="edrpou-data-selectize" class="selectize-control single hide" style="height: 400px;">
                                    <div class="selectize-dropdown single">
                                        <div class="selectize-dropdown-content" id="edrpou-data" style="max-height: 425px;height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-tags edrpou-data-selected" id="edrpou-data-selected">
                                @if(!empty($filters['edrpou']))
                                    @foreach($filters['edrpou'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $item }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.CPVcode') }} <span data-filters-type="cpv" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">

                                <input v-on:keyup="searchFilters('cpv')" value="" type="text" name="cpv" autocomplete="off" placeholder="{{ t('tender.search.CPVcode_input') }}">

                                <div id="cpv-data-selectize" class="selectize-control single hide" style="height: 400px;">
                                    <div class="selectize-dropdown single">
                                        <div class="selectize-dropdown-content" id="cpv-data" style="max-height: 425px;height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-tags cpv-data-selected" id="cpv-data-selected">
                                @if(!empty($filters['cpv']))
                                    @foreach($filters['cpv'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $item }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.region') }} <span data-filters-type="region" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">

                                <input value="" type="text" name="region" autocomplete="off" placeholder="{{ t('tenders.search.region_input') }}">

                                <div id="region-data-selectize" class="selectize-control single hide" style="height: 400px;">
                                    <div class="selectize-dropdown single">
                                        <div class="selectize-dropdown-content" id="region-data" style="max-height: 425px;height: 400px;">
                                            @foreach($regions AS $region)
                                                <div data-id="{{ $region['id'] }}">{{ $region['name'] }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-tags region-selected" id="region-selected">
                                @if(!empty($filters['region']))
                                    @foreach($filters['region'] as $id => $item)
                                        <button class="selected-tag" data-id="{{ $id }}">{{ $item }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="filter-item checkbox-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.procedureStatus') }} <span data-filters-type="status" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                @foreach($statuses as $item)
                                    <label class="checkbox-item">
                                        <input {{ !empty($filters['status'][$item['id']]) ? 'checked' : '' }} type="checkbox" name="status" value="{{ $item['id'] }}" data-text="{{ $item['name'] }}" id="risk-{{ $item['id'] }}" class="checkbox-default">
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-text" for="risk-{{ $item['id'] }}">{{ $item['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-item checkbox-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.procedureType') }} <span data-filters-type="proc_type" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                @foreach($procedures as $item)
                                    <label class="checkbox-item">
                                        <input {{ @$item['selected'] ? 'checked' : '' }} type="checkbox" name="proc_type" value="{{ $item['id'] }}" data-text="{{ $item['name'] }}" id="risk-{{ $item['id'] }}" class="checkbox-default">
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-text" for="risk-{{ $item['id'] }}">{{ $item['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.tendererID') }} <span data-filters-type="tenderer_edrpou" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">

                                <input v-on:keyup="searchFilters('tenderer_edrpou', 1)" value="" type="text" name="tenderer_edrpou" autocomplete="off" placeholder="{{ t('tender.search.tendererID_input') }}">

                                <div id="tenderer_edrpou-data-selectize" class="selectize-control single hide" style="height: 400px;">
                                    <div class="selectize-dropdown single">
                                        <div class="selectize-dropdown-content" id="tenderer_edrpou-data" style="max-height: 425px;height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-tags tenderer_edrpou-data-selected" id="tenderer_edrpou-data-selected">
                                @if(!empty($filters['tenderer_edrpou']))
                                    @foreach($filters['tenderer_edrpou'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                                @if(!empty($filters['tenderer_edrpou_all']))
                                    @foreach($filters['tenderer_edrpou_all'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.tendererID_limited') }} <span data-filters-type="supplier_edrpou" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">

                                <input v-on:keyup="searchFilters('supplier_edrpou', 1)" value="" type="text" name="supplier_edrpou" autocomplete="off" placeholder="{{ t('tender.search.tendererID_limited_input') }}">

                                <div id="supplier_edrpou-data-selectize" class="selectize-control single hide" style="height: 400px;">
                                    <div class="selectize-dropdown single">
                                        <div class="selectize-dropdown-content" id="supplier_edrpou-data" style="max-height: 425px;height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-tags supplier_edrpou-data-selected" id="supplier_edrpou-data-selected">
                                @if(!empty($filters['supplier_edrpou']))
                                    @foreach($filters['supplier_edrpou'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                                @if(!empty($filters['supplier_edrpou_all']))
                                    @foreach($filters['supplier_edrpou_all'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.supplier_active') }} <span data-filters-type="supplier_active" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                <input v-on:keyup="searchFilters('supplier_active', 1)" value="" type="text" name="supplier_active" autocomplete="off" placeholder="{{ t('tender.search.supplier_active_input') }}">

                                <div id="supplier_active-data-selectize" class="selectize-control single hide" style="height: 400px;">
                                    <div class="selectize-dropdown single">
                                        <div class="selectize-dropdown-content" id="supplier_active-data" style="max-height: 425px;height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-tags supplier_active-data-selected" id="supplier_active-data-selected">
                                @if(!empty($filters['supplier_active']))
                                    @foreach($filters['supplier_active'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="filter-item tags-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.contract_active') }} <span data-filters-type="contract_active" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                <input v-on:keyup="searchFilters('contract_active', 1)" value="" type="text" name="contract_active" autocomplete="off" placeholder="{{ t('tender.search.contract_active_input') }}">

                                <div id="contract_active-data-selectize" class="selectize-control single hide" style="height: 400px;">
                                    <div class="selectize-dropdown single">
                                        <div class="selectize-dropdown-content" id="contract_active-data" style="max-height: 425px;height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-tags contract_active-data-selected" id="contract_active-data-selected">
                                @if(!empty($filters['contract_active']))
                                    @foreach($filters['contract_active'] as $item)
                                        <button class="selected-tag" data-id="{{ $item['id'] }}">{{ $item['name'] }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="filter-item price-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.estimate_value') }}</span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                <div class="extra-controls">
                                    <input type="text" value="{{ !empty($filters['value'][0][0]) ? $filters['value'][0][0] : '' }}" id="price1" />
                                    <input type="text" value="{{ !empty($filters['value'][0][1]) ? $filters['value'][0][1] : '' }}" id="price2" />
                                </div>
                            </div>
                        </div>

                        @if($riskAccess)
                        <div class="filter-item checkbox-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.risks') }} <span data-filters-type="risks" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body" id="any-risks-block">
                                <div style="margin-bottom: 10px;padding-bottom: 20px;border-bottom: 1px solid #dfdfdf;">
                                    <label class="checkbox-item">
                                        <input @if($risk_code_like) checked @endif type="checkbox" id="any-risks" value="1" class="checkbox-default" @if($checkedAnyRisks){{'checked'}}@endif>
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-text" for="any-risks">{{ t('indicators.any_risks') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                @foreach($risks as $risk)
                                    @if(!empty($risk->description))
                                        @include('partials.search._risks')
                                    @endif
                                @endforeach
                                @foreach($risks as $risk)
                                    @if(empty($risk->description))
                                        @include('partials.search._risks')
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="filter-item checkbox-selector">
                            <div class="filter-item-head">
                                <div class="title open">
                                    <span>{{ t('tender.search.review') }} <span data-filters-type="forms" class="filters-count"></span></span>
                                    <span class="filter-item-body-toggle"></span>
                                </div>
                            </div>
                            <div class="filter-item-body">
                                @foreach($forms as $item)
                                    <label class="checkbox-item">
                                        <input {{ !empty($filters['form_code']) && in_array($item, $filters['form_code']) ? 'checked' : '' }}
                                               {{ !empty($filters['form_code_all']) && in_array($item, $filters['form_code_all']) ? 'checked' : '' }}
                                               type="checkbox" name="forms" value="{{ $item }}" data-text="{{ t('tender.search.'.$item) }}" id="form-{{ $item }}" class="checkbox-default">
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-text" for="form-{{ $item }}">{{ t('tender.search.'.$item) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{--<div class="filter-item date-selector">
                            <div class="filter-item-head">
                                <div class="title">Класифікатор</div>
                            </div>
                            <div class="filter-item-body open">
                                <div class="dates-container">
                                    <input class="date-mask" data-inputmask-alias="mm/dd/yyyy" data-inputmask="'yearrange': { 'minyear': '2014', 'maxyear': '2018' }" data-val="true" data-val-required="Required" id="date_from" name="date_from" placeholder="mm/dd/yy" type="text" value="" >
                                    <input class="date-mask" data-inputmask-alias="mm/dd/yyyy" data-inputmask="'yearrange': { 'minyear': '2014', 'maxyear': '2018' }" data-val="true" data-val-required="Required" id="date_to" name="date_to" placeholder="mm/dd/yy" type="text" value="" >
                                </div>
                            </div>
                        </div>
                        --}}
                    </div>
                    <div class="filter-apply">
                        <button class="apply-btn" v-on:click="search()">{{ t('tender.search.submit') }}</button>
                        <button class="filter-reset">{{ t('tender.search.filters_reset') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($riskFeedback))
            @include('partials.sidebar.feedback')
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function(){

        var countFilters = 0;

        /*var checked = $('input[name="risks"]:checked').length;

        if(checked >= 2) {
            $('#any-risks-block').removeClass('hide');
        } else {
            $('#any-risks-block').addClass('hide');
        }*/

        $('input[type="checkbox"]').on('change', function () {
            var state = $(this).is(':checked');
            var value = $(this).val();
            var text = $(this).attr('data-text');
            var name = $(this).attr('name');

            /*if(name == 'risks') {

                var checked = $(this).closest('div').find('input:checked').length;

                if(checked >= 2) {
                    $('#any-risks-block').removeClass('hide');
                 } else {
                    $('#any-risks-block').addClass('hide');
                 }
            }*/

            if(!state) {
                $('.selected-tags.'+name+'-selected').find('.selected-tag[data-id="' + value + '"]').remove();
            } else {
                $(this).closest('.filter-items').find('.selected-tags.'+name+'-selected').append('<button class="selected-tag hide" data-id="'+value+'">'+text+'</button>');
            }

            //changeStates();
        });
        $('select[name="sort"]').on('change', function () {
            $('.apply-btn').click();
        });
        $('.selected-tags').on('click', 'button', function () {
            var id = $(this).attr('data-id');

            $('.selected-tags').find('.selected-tag[data-id="'+id+'"]').remove();
            $('.indicator-filter-body').find('input[type="checkbox"][value="'+id+'"]').trigger('click');

            $('.apply-btn').click();

            //changeStates();
        });
        $('#contract_active-data, #supplier_active-data, #supplier_edrpou-data ,#tenderer_edrpou-data, #edrpou-data, #cpv-data').on('click', 'div', function () {
            var id = $(this).attr('data-id');
            var name = $(this).text();
            var blockId = $(this).parent().attr('id');

            $(this).closest('.filter-items').find('.top-tags .selected-tags.'+blockId+'-selected').append('<button class="selected-tag hide" data-id="'+id+'">'+name+'</button>');
            $(this).closest('.filter-item').find('.selected-tags.'+blockId+'-selected').append('<button class="selected-tag" data-id="'+id+'">'+name+'</button>');
            document.getElementById(blockId+'-selectize').classList.add('hide');
            $('#'+blockId+'-selectize').prev().val('');

            //changeStates();
        });
        /*$('#cpv-data').on('click', 'div', function () {
            var id = $(this).attr('data-id');
            var name = $(this).text();

            $(this).closest('.filter-items').find('.selected-tags.cpv-selected').append('<button class="selected-tag" data-id="'+id+'">'+name+'</button>');
            document.getElementById('cpv-data-selectize').classList.add('hide');
            $('#cpv-data-selectize').prev().val('');

            changeStates();
        });*/
        $('#region-data').on('click', 'div', function () {
            var id = $(this).attr('data-id');
            var fid = $(this).attr('data-full-id');
            var name = $(this).text();

            if(fid && fid !== null && fid !== undefined) {
                id = fid;
            }

            $(this).closest('.filter-items').find('.top-tags .selected-tags.region-selected').append('<button class="selected-tag hide" data-id="'+id+'">'+name+'</button>');
            $(this).closest('.filter-item').find('.selected-tags.region-selected').append('<button class="selected-tag" data-id="'+id+'">'+name+'</button>');

            document.getElementById('region-data-selectize').classList.add('hide');
            $('#region-data-selectize').prev().val('');
            $('#region-data').find('div').show();

            //changeStates();
        });
        $('input[name="region"]').on('click', function () {
            document.getElementById('region-data-selectize').classList.remove('hide');
        });
        $('input[name="region"]').on('keyup', function () {

            var value = $(this).val();
            var searchByIndex = value.length == 5 && !isNaN(parseInt(value));

            $('#region-data').find('div').each(function() {

                if(searchByIndex) {
                    $(this).hide();
                    var range = $(this).attr('data-id').split('-');
                    range[0] = parseInt(range[0]);

                    if(range.length == 1) {
                        range.push(range[0]);
                    } else {
                        range[1] = parseInt(range[1]);
                    }

                    for(var i = range[0]; i <= range[1]; i++) {
                        var index = i.toString();

                        if(index.length == 1) {
                            index = '0'+index;
                        }

                        if(value.indexOf(index) == 0) {
                            $(this).show();
                            $(this).attr('data-full-id', value);
                            break;
                        }
                    }
                } else {
                    if ($(this).text().toLowerCase().indexOf(value.toLowerCase()) <= -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        });
        $('.apply-btn').on('click', function () {
            changeStates();
            $('.indicator-filter-body').find('.filter-item-body').removeClass('open');
            $('.indicator-filter-body').find('.filter-head > .title').addClass('open');
        });

        function changeStates() {
            if ($('.top-tags .selected-tags').find('.selected-tag').length > 0) {
                $('.top-tags').removeClass('hide');
                $('.top-tags .selected-tags').find('.selected-tag').removeClass('hide');
                $('.tags-selector:not(.top-tags) .selected-tags').find('.selected-tag').addClass('hide');
            } else {
                $('.top-tags').addClass('hide');
            }

            countFilters = 0;

            var showSelectedBlock = $('#selected-filters');
            showSelectedBlock.find('a').remove();
            $('#selected-filters-span').hide();

            $('.top-tags').find('.selected-tags').each(function () {

                var type = $(this).attr('data-type');
                var countBlock = $('.filter-items').find('[data-filters-type="'+type+'"]');
                var count = $(this).find('.selected-tag').length;

                if(type.indexOf('edrpou') > -1 || type.indexOf('supplier') > -1 | type.indexOf('contract') > -1) {
                    $(this).find('.selected-tag').each(function() {
                        var id = $(this).attr('data-id');
                        var name = $(this).text();
                        var tpl = type == 'edrpou' ? '/{{ $profileRole1TplId }}/role1' : '/{{ $profileRole2TplId }}/role2';
                        var html = '<a target="_blank" href="/profile/UA-EDR-'+id+tpl+'">'+name+'</a>';

                        showSelectedBlock.append(html);
                    });

                    if(showSelectedBlock.find('a').length) {
                        $('#selected-filters-span').show();
                    }
                }

                if (count > 0) {
                    $(this).removeClass('hide');
                    countFilters += count;
                    countBlock.html('('+count+')');
                } else {
                    $(this).addClass('hide');
                    countBlock.html('');
                }
            });

            if(countFilters) {
                $('#filters-count').text(countFilters);
            } else {
                $('#filters-count').text('');
            }
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

        $('.input-group.date input').datepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            autoclose:true,
            language: 'ua'
        });
        changeStates();
    });
</script>
@endpush
@endsection