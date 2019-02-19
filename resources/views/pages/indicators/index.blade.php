@extends('layouts/app')

@section('content')
    <div class="bg_grey page_risk" id="vue-indicators" data-is-mobile="{{ $isMobile }}" data-api-url="{{ route('page.indicators.search') }}">
        <spinner></spinner>
        <div class="container">
            <div class="col-md-9" id="tenders-table">
                <div class="bg_white">
                    <div class="list_tender_company">
                        <div class="overflow-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="30%">{{ t('indicators.result.customer') }}</th>
                                        <th width="35%">{{ t('indicators.result.title') }}</th>
                                        <th width="15%">{{ t('indicators.result.sum') }}</th>
                                        <th>{{ t('indicators.result.ngo') }}</th>
                                        {{--<th width="5%">{{ t('indicators.result.rating') }}</th>--}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, key) in tenders" v-on:click="showSidebar(key)" v-bind:class="{ 'selected': tenderKey == key }">
                                        <td>
                                            <a target="_blank" :href="item.tender_route_role" v-html="item.procuringEntity.name"></a>
                                        </td>
                                        <td v-html="item.title" style="max-height: 57px;overflow: hidden;display: inline-block;"></td>
                                        <td v-html="item.price"></td>
                                        <td v-html="item.ngo ? '{{ t('indicator.ngo.yes') }}':'{{ t('indicator.ngo.no') }}'"></td>
                                        {{--<td v-html="item.rating"></td>--}}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="pagination" v-bind:class="{ 'link_pagination': true, 'loading': nextPageLoading }" v-on:click="nextPage()"><spinner size="small"></spinner>{{ t('indicators.show_more') }}</div>
                </div>
            </div>
            <div id="sidebar" class="col-md-3" v-if="tender" v-sticky="{ zIndex: 1, stickyTop: 20 }">
                <div>
                    <button class="tender-content-close" v-on:click="closeSidebar()"></button>
                    <div class="tender-header-banner" ref="sidebarScroll">
                        @include('partials.sidebar.vue.common')
                        @include('partials.sidebar.vue.procedure')
                        @include('partials.sidebar.vue.dates')
                        @include('partials.sidebar.vue.risks')
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
                        @include('partials.sidebar.vue.form')
                        @include('partials.sidebar.vue.feedback')
                    </div>
                </div>
            </div>
        </div>

        @include('partials.sidebar.feedback')

    </div>
@endsection