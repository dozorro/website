    <div class="tender-header">
        <div class="container">
            <div class="row row-flex">
                <div class="col-md-9 tender-header__info">
                    <div class="bg_grey page_risk" id="vue-indicators" data-api-url="{{ route('page.indicators.search') }}" style="padding-bottom: 0px;">
                        <spinner></spinner>
                        <div id="sidebar" class="col-md-3" v-if="tender" v-sticky="{ zIndex: 1, stickyTop: 20 }" :data-tender-id="tender.id" :data-tender-public-id="tender.tenderID" :data-tender-edrpou="tender.procuringEntity.id">
                            <div>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('partials.sidebar.vue.forms')
                </div>
            </div>
        </div>
    </div>
    @include('partials.blocks.tender._form_popup')