@extends('layouts/app')

@section('head')
    @if (!$sidebarMode && $item && !$error)
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{t('facebook.site_name')}}">
        @if(!isset($seo->og_title) || !$seo->og_title)
        <meta property="og:title" content="{{htmlentities($item->procuringEntity->name, ENT_QUOTES)}}">
        @endif
        @if(!isset($seo->og_url) || !$seo->og_url)
        <meta property="og:url" content="{{env('ROOT_URL')}}/{{Request::path()}}">
        @endif
        @if(!isset($seo->og_description) || !$seo->og_description)
        <meta property="og:description" content="{{!empty($item->title) ? htmlentities($item->title, ENT_QUOTES) : t('facebook.tender_no_name')}}">
        @endif
    @endif
@endsection

@section('content')

    @if($sidebarMode)
        @include('partials._tender_sidebar')
    @elseif ($item && !$error)
        <div class="tender" data-js="tender" >
            <div class="tender-header-wrap">
                @include('partials/blocks/tender/header', ['tender_page' => true])
            </div>

            <div class="tender-tabs-wrapper">
                <div class="tender-tabs jsTenderTabs">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-9 ">
                                <div class="tender-tabs__wrap">
                                    <ul class="tender-tabs__buttons">
                                        <li data-hash="reviews" class="tender-tabs__item jsShowReviews @if(!count($f200_reviews)){{'is-show'}}@endif">
                                            <span class="">{{t('tender.reviews_title')}}</span>
                                            <span class="comment_number" data-reviews-count="{{ sizeof($reviews) }}">{{ sizeof($reviews) }}</span>
                                        </li>
                                        @if(count($f200_reviews))
                                            <li data-hash="ngo" class="tender-tabs__item jsShowDescription @if(count($f200_reviews)){{'is-show'}}@endif">
                                                <span class="">{{t('tender.reviews_title_tab_ngo')}}</span>
                                                <span class="comment_number">{{ sizeof($f200_reviews) }}</span>
                                            </li>
                                        @endif
                                        <li class="tender-tabs__item jsShowDescription" data-hash="tender">
                                            <span >{{t('tender.information_about_tender')}}</span>
                                        </li>
                                        @if(!empty($lot_id))
                                            <li class="tender-tabs__item" data-hash="lot">
                                                <span>{{t('tender.reviews_title_tab_lots')}}</span>
                                            </li>
                                        @endif
                                        <li class="tender-tabs__item" style="display:none" data-hash="comments">
                                            <span>{{t('tender.discussion')}}</span>
                                        </li>
                                        @if((!empty($user->is_tender_risks) || !empty($riskAccess)) && !empty($item->__risks))
                                            <li class="tender-tabs__item" data-hash="risk_tender">
                                                <span>{{t('tender.risk_tabs')}}</span>
                                                <span class="comment_number">{{ $item->__risks->risks_total }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            {{--@if((!empty($user->is_tender_risks) || !empty($riskAccess)) && !empty($item->__risks))
                               <div class="col-md-3 bg_white risk_desc">
                                    <div class="title_risk">
                                        <span>{{ t('tender.riskScore') }} {{ !empty($item->dozorro->riskScore) ? round($item->dozorro->riskScore, 2) : 0 }}</span>
                                        <img src="/assets/images/risk_icons.svg" />
                                    </div>
                                </div>
                            @endif--}}
                        </div>

                    </div>
                </div>
            </div>

            @include('partials.blocks.tender._form_popup')

            <div class="reviews @if(!count($f200_reviews)){{'is-show'}}@else{{'none'}}@endif" tab-content>
                <div class="container" reviews>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="list_reviews">
                                @if(!sizeof($reviews))
                                    <div class="reviews__item custom-review">
                                        {{t('tender.reviews.none')}}
                                    </div>
                                @endif
                                @foreach ($reviews as $review)
                                    @include('partials/review', [
                                        'show_related' => true,
                                        'is_parent' => true,
                                    ])

                                    @if (sizeof($review->reviews) > 0)
                                        @foreach ($review->reviews as $innerReview)
                                            @include('partials/review', [
                                                'review' => $innerReview,
                                                'parent' => $review,
                                                'is_parent' => false,
                                                'show_related' => true,
                                            ])
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('partials/blocks/tender/ngo_reviews')

            <div class="tender--description" tab-content>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="block_tender_info">
                                <!--<div class="list_lang inline-layout">
                                    <div class="item active">
                                        <a href="#">UA</a>
                                    </div>
                                    <div class="item">
                                        <a href="#">EN</a>
                                    </div>
                                </div>-->
                                {{--
                                @if(!empty($item->__open_name) && $item->__open_name!='hide')
                                    @if(!empty($item->__open_name))
                                        <h2>
                                            {!!$item->__open_name!!}
                                        </h2>
                                        @if(!empty($item->__stage2TenderID))
                                            <div style="margin-top:-35px;margin-bottom:60px">
                                                <a href="{{ route('page.tender_by_id', ['id' => $item->__stage2TenderID]) }}">{{t('tender.go_2nd_stage')}}</a>
                                            </div>
                                        @endif
                                        @if(!empty($item->__stage1TenderID))
                                            <div style="margin-top:-35px;margin-bottom:60px">
                                                <a href="{{ route('page.tender_by_id', ['id' => $item->__stage1TenderID]) }}">{{t('tender.go_1nd_stage')}}</a>
                                            </div>
                                        @endif
                                        @if(!empty($item->title_en))
                                            <div style="margin-top:-{{ !empty($item->__stage2TenderID) || !empty($item->__stage1TenderID) ? '55':'35' }}px;margin-bottom:60px">Tender notice</div>
                                        @endif
                                    @endif
                                    @if($item->__print_href && !in_array($item->procurementMethodType, ['negotiation', 'negotiation.quick']))
                                        @if(starts_with($item->__print_href, 'limited'))
                                            @if(empty($item->__active_award))
                                                <div style="margin-top:-30px;margin-bottom:40px">{{t('tender.printable_forms_necessary_complete_action_court')}}</div>
                                            @else
                                            @else
                                                <div style="margin-top:-30px;margin-bottom:40px">{{t('tender.print_request_form')}} <a href="{{('/tender/'.$item->tenderID.'/print/'.$item->__print_href.'/pdf')}}" target="_blank">PDF</a> ● <a href="{{('/tender/'.$item->tenderID.'/print/'.$item->__print_href.'/html')}}" target="_blank">HTML</a></div>
                                            @endif
                                        @else
                                            <div style="margin-top:-30px;margin-bottom:40px">{{t('tender.print_request_form')}} <a href="{{('/tender/'.$item->tenderID.'/print/'.$item->__print_href.'/pdf')}}" target="_blank">PDF</a> ● <a href="{{('/tender/'.$item->tenderID.'/print/'.$item->__print_href.'/html')}}" target="_blank">HTML</a></div>
                                        @endif
                                    @endif
                                    @if(!empty($item->__stage2TenderID))
                                        <div style="margin-top:-30px;margin-bottom:40px">{{t('tender.print_request_form_2nd_stage')}} <a href="{{('/tender/'.$item->__stage2TenderID.'/print/'.$item->__print_href.'/pdf')}}" target="_blank">PDF</a> ● <a href="{{('/tender/'.$item->__stage2TenderID.'/print/'.$item->__print_href.'/html')}}" target="_blank">HTML</a></div>
                                    @endif
                                    @if(!empty($item->__stage1TenderID))
                                        <div style="margin-top:-30px;margin-bottom:40px">{{t('tender.print_request_form_1nd_stage')}} <a href="{{('/tender/'.$item->__stage1TenderID.'/print/'.$item->__print_href.'/pdf')}}" target="_blank">PDF</a> ● <a href="{{('/tender/'.$item->__stage1TenderID.'/print/'.$item->__print_href.'/html')}}" target="_blank">HTML</a></div>
                                    @endif
                                @else
                                    @if ($item->procurementMethod == 'open' && in_array($item->procurementMethodType, ['aboveThresholdEU', 'competitiveDialogueEU', 'aboveThresholdUA.defense']))
                                        @if (Lang::getLocale() == 'en' )
                                            <h2>Tender notice</h2>
                                        @else
                                            <h2></h2>
                                        @endif
                                    @endif
                                @endif

                                @if ($item->__isSingleLot)
                                    @if(in_array($item->status, ['complete', 'unsuccessful', 'cancelled']) && $item->procurementMethod=='open' && in_array($item->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdEU', 'aboveThresholdUA.defense']))
                                        <div style="margin-top:-30px;margin-bottom:40px">{{t('tender.print_report_results_procedure')}} <a href="{{('/tender/'.$item->tenderID.'/print/report/pdf')}}" target="_blank">PDF</a> ● <a href="{{('/tender/'.$item->tenderID.'/print/report/html')}}" target="_blank">HTML</a></div>
                                    @endif
                                    @if(in_array($item->status, ['complete', 'cancelled']) && $item->procurementMethod=='limited' && in_array($item->procurementMethodType, ['negotiation', 'negotiation.quick']))
                                        <div style="margin-top:-30px;margin-bottom:40px">{{t('tender.print_report_results_procedure')}} <a href="{{('/tender/'.$item->tenderID.'/print/report/pdf')}}" target="_blank">PDF</a> ● <a href="{{('/tender/'.$item->tenderID.'/print/report/html')}}" target="_blank">HTML</a></div>
                                    @endif
                                @endif
                                --}}

                                {{--Інформація про замовника--}}
                                {{--@include('partials/blocks/tender/procuring-entity')--}}

                                {{--Обгрунтування застосування переговорної процедури--}}
                                @include('partials/blocks/tender/negotiation')

                                {{--Інформація про процедуру--}}
                                @include('partials/blocks/tender/dates')

                                {{--Інформація про предмет закупівлі--}}
                                @include('partials/blocks/tender/info', [
                                    'lot_id'=>false
                                ])


                                {{--Критерії вибору переможця--}}
                                @include('partials/blocks/tender/criteria')

                                {{--Тендерна документація--}}
                                @include('partials/blocks/tender/documentation', [
                                    'lot_id'=>false
                                ])


                                @if (!empty($item->__complaints_claims) ||!empty($item->__questions))
                                    <!--<h2>{{t('tender.clarification_procedure')}}</h2>-->

                                    {{--Запитання до процедури--}}
                                    @include('partials/blocks/tender/questions', [
                                    'lot_id'=>false
                                ])

                                    {{--Вимоги про усунення порушення--}}
                                    @include('partials/blocks/tender/claims')

                                @endif

                                {{--Скарги до процедури--}}
                                @include('partials/blocks/tender/complaints', [
                                    'lot_id'=>false
                                ])

                                @if (!$item->__isMultiLot)

                                    {{--Протокол розгляду--}}
                                    @include('partials/blocks/tender/qualifications')

                                    {{--auction--}}
                                    @include('partials/blocks/tender/auction', [
                                    'lot_id'=>false
                                    ])

                                    {{--Реєстр пропозицій--}}
                                    @include('partials/blocks/tender/bids')

                                    {{--Протокол розкриття--}}
                                    @include('partials/blocks/tender/awards', [
                                    'lot_id'=>false
                                    ])

                                    {{--Повідомлення про намір укласти договір--}}
                                    @include('partials/blocks/tender/active-awards')

                                    {{--Укладений договір--}}
                                    @include('partials/blocks/tender/contract', [
                                    'lotID'=>false
                                    ])

                                    {{--Зміни до договору--}}
                                    @include('partials/blocks/tender/contract-changes')

                                    {{--Виконання договору--}}
                                    @include('partials/blocks/tender/contract-ongoing')

                                    {{--Інформація про скасування--}}
                                    @include('partials/blocks/tender/cancelled', [
                                        'what'=>'tender',
                                        'tenderPeriod'=>!empty($item->tenderPeriod) ? $item->tenderPeriod : false,
                                        'qualificationPeriod'=>!empty($item->qualificationPeriod) ? $item->qualificationPeriod : false
                                    ])

                                @endif

                                @if($item->__isMultiLot)

                                    <div class="margin-bottom margin-bottom-more">
                                        <div class="block_title">
                                            <h3>{{t('tender.lots_title')}}</h3>
                                        </div>
                                        <div class="list_lots inline-layout">
                                            @if(!empty($item->__lot_titles))
                                                @foreach($item->__lot_titles as $k=>$lot)
                                                    <div class="item">
                                                        <a href="?lot_id={{$lot->id}}#lot" class="word-break{{ $lot_id == $lot->id ? ' active':'' }}">{{ !empty($lot->lotNumber) ? $lot->lotNumber : (!empty($lot->title) ? $lot->title : 'Лот '.($k+1)) }}</a>
                                                        @if($item->riskScore && $riskInLots)
                                                        <div data-rating="{{ $item->riskScore }}" class="rating_stars">
                                                            @foreach(range(1, 10) as $index)
                                                            <div class="star @if($item->__rating >= $index){{'active'}}@endif @if($item->__rating >= $index && $item->__rating < $index+1 && $item->riskScoreHalf <= 0.5){{'half'}}@endif"></div>
                                                            @endforeach
                                                        </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                @endif

                            </div>

                        </div>
                        <div class="col-md-3 list_fixed_menu" style="display: block;">
                            <ul class="list_wrap nav static_wrap">
                                    <li class="item active">
                                        <a href="#block_info">{{t('tender.navigation.inform')}}</a>
                                    </li>

                                    <li class="item">
                                        <a href="#block_docs">{{t('tender.navigation.docs')}}</a>
                                    </li>

                                @if (!empty($item->__questions))
                                    <li class="item">
                                        <a href="#block_question">{{t('tender.navigation.questions')}}</a>
                                    </li>
                                @endif
                                @if (!empty($item->__complaints_complaints))
                                    <li class="item">
                                        <a href="#block_complaints">{{t('tender.navigation.complaints')}}</a>
                                    </li>
                                @endif

                                @if (!$item->__isMultiLot && (!empty($item->auctionPeriod->startDate) || !empty($item->auctionPeriod->endDate) || !empty($item->auctionUrl)))
                                    <li class="item">
                                        <a href="#block_auction">{{t('tender.navigation.auction')}}</a>
                                    </li>
                                @endif

                                @if (!$item->__isMultiLot && !empty($item->awards) && $item->procurementMethod=='open')
                                    <li class="item">
                                        <a href="#block_awards">{{t('tender.navigation.awards')}}</a>
                                    </li>
                                @endif

                                @if(!$item->__isMultiLot && !empty($item->__documents))
                                    <li class="item">
                                        <a href="#block_contract">{{t('tender.navigation.contract')}}</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                @if(!empty($lotted))
                    {{--Інформація про скасування--}}
                    @include('partials/blocks/tender/cancelled', [
                        'item'=>$item,
                        'tenderPeriod'=>!empty($item->tenderPeriod) ? $item->tenderPeriod : false,
                        'qualificationPeriod'=>!empty($item->qualificationPeriod) ? $item->qualificationPeriod : false
                    ])
                @endif
            </div>

            @if(!empty($lot_id))
                <div class="tender--description" tab-content>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="block_tender_info">

                                    <div class="margin-bottom margin-bottom-more">
                                        <div class="block_title">
                                            <h3>{{t('tender.lots_title')}}</h3>
                                        </div>
                                        <div class="list_lots inline-layout">
                                            @if(!empty($item->__lot_titles))
                                                @foreach($item->__lot_titles as $k=>$lot)
                                                    <div class="item">
                                                        <a href="?lot_id={{$lot->id}}#lot" class="word-break{{ $lot_id == $lot->id ? ' active':'' }}">{{ !empty($lot->lotNumber) ? $lot->lotNumber : (!empty($lot->title) ? $lot->title : 'Лот '.($k+1)) }}</a>
                                                        @if($item->riskScore && $riskInLots)
                                                            <div data-rating="{{ $item->riskScore }}" class="rating_stars">
                                                                @foreach(range(1, 10) as $index)
                                                                    <div class="star @if($item->__rating >= $index){{'active'}}@endif @if($item->__rating >= $index && $item->__rating < $index+1 && $item->riskScoreHalf <= 0.5){{'half'}}@endif"></div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    @foreach($item->lots as $k=>$lot)
                                        @if($lot->id == $lot_id)
                                        <h3>{{ !empty($lot->lotNumber) ? $lot->lotNumber : (!empty($lot->title) ? $lot->title : 'Лот '.($item->__lot_k+1)) }}</h3>
                                        <div class="tab-content tab-lot-content active">
                                            {{--Опис--}}
                                            @include('partials/blocks/lots/info', [
                                                'item'=>$lot,
                                                'tender'=>$item,
                                                'lot_id'=>$lot->id
                                            ])

                                            {{--Позиції--}}
                                            @include('partials/blocks/lots/items', [
                                                'item'=>$lot
                                            ])

                                            {{--<h2>Документація</h2>--}}

                                            {{--Критерії вибору переможця--}}
                                            @include('partials/blocks/lots/criteria', [
                                                'item'=>$lot
                                            ])

                                            {{--Документація--}}
                                            @include('partials/blocks/tender/documentation',[
                                                'item'=>$lot,
                                                'lot_id'=>$lot->id
                                            ])

                                            {{--Запитання до лоту--}}
                                            @include('partials/blocks/tender/questions', [
                                                'item'=>$lot,
                                                'lot_id'=>$lot->id
                                            ])

                                            {{--Вимоги про усунення порушення до лоту--}}
                                            @include('partials/blocks/tender/claims', [
                                                'item'=>$lot
                                            ])

                                            {{--Скарги до лоту--}}
                                            @include('partials/blocks/tender/complaints', [
                                                'item'=>$lot,
                                                'title'=>'Скарги до лоту',
                                                'lot_id'=>$lot->id
                                            ])

                                            {{--Протокол розгляду--}}
                                            @include('partials/blocks/tender/qualifications', [
                                                'item'=>$lot
                                            ])

                                            {{--auction--}}
                                            @include('partials/blocks/tender/auction', [
                                                'item'=>$lot,
                                                'lot_id'=>$lot->id
                                            ])

                                            {{--Реєстр пропозицій--}}
                                            @include('partials/blocks/tender/bids', [
                                                'item'=>$lot
                                            ])

                                            {{--Протокол розкриття--}}
                                            @include('partials/blocks/tender/awards', [
                                                'item'=>$lot,
                                                'lot_id'=>$lot->id
                                            ])

                                            {{--Повідомлення про намір укласти договір--}}
                                            @include('partials/blocks/tender/active-awards', [
                                                'item'=>$lot
                                            ])

                                            {{--Інформація про скасування--}}
                                            @include('partials/blocks/tender/cancelled', [
                                                'item'=>$lot,
                                                'what'=>'lot',
                                                'tenderPeriod'=>!empty($item->tenderPeriod) ? $item->tenderPeriod : false,
                                                'qualificationPeriod'=>!empty($item->qualificationPeriod) ? $item->qualificationPeriod : false
                                            ])

                                            {{--Укладений договір--}}
                                            @include('partials/blocks/tender/contract', [
                                                'item'=>$lot,
                                                'lotID'=>$lot->id,
                                            ])

                                            {{--Зміни до договору--}}
                                            @include('partials/blocks/tender/contract-changes', [
                                                'item'=>$lot,
                                                'lotID'=>$lot->id,
                                            ])

                                            {{--Виконання договору--}}
                                            @include('partials/blocks/tender/contract-ongoing', [
                                                'item'=>$lot,
                                                'lotID'=>$lot->id,
                                            ])

                                        </div>
                                        @endif
                                    @endforeach
                                    <?php
                                        $lotted=true;
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3 list_fixed_menu" style="display: block;">
                                <ul class="list_wrap nav static_wrap">
                                    <li class="item active">
                                        <a href="#block_info_lot">{{t('tender.navigation.lot_inform')}}</a>
                                    </li>

                                    <li class="item">
                                        <a href="#block_docs_lot">{{t('tender.navigation.lot_docs')}}</a>
                                    </li>

                                    @if (!empty($lot->questions))
                                        <li class="item">
                                            <a href="#block_question_lot">{{t('tender.navigation.lot_questions')}}</a>
                                        </li>
                                    @endif
                                    @if (!empty($lot->complaints))
                                        <li class="item">
                                            <a href="#block_complaints_lot">{{t('tender.navigation.lot_complaints')}}</a>
                                        </li>
                                    @endif
                                    @if (!empty($lot->auctionPeriod->startDate) || !empty($lot->auctionPeriod->endDate) || !empty($lot->auctionUrl))
                                        <li class="item">
                                            <a href="#block_auction_lot">{{t('tender.navigation.lot_auction')}}</a>
                                        </li>
                                    @endif

                                    @if (!empty($lot->awards) && $lot->procurementMethod=='open')
                                        <li class="item">
                                            <a href="#block_awards_lot">{{t('tender.navigation.lot_awards')}}</a>
                                        </li>
                                    @endif
                                    @if(!empty($lot->__documents))
                                        <li class="item">
                                            <a href="#block_contract_lot">{{t('tender.navigation.lot_contract')}}</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="reviews" style="display:none" tab-content>
                <div class="container" comments>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="list_reviews">
                                @foreach ($all_reviews as $review)
                                    <div class="reviews__item custom-review" data-object-id="{{ $review->object_id }}">
                                        <div class="reviews__item-inner">
                                            <div class="reviews__header">
                                                @if(!in_array($review->schema, ['F114','F115','F116']) && !in_array($item->status, ['active.enquires','active.tendering','active.auction']))
                                                <span class="reviews__author reviews__author--{{ $review->is_anon? 'not-':''}}confirmed">
                                                    @if($review->user && ($review->user->ngo_profile || $review->user->issetEdrpou($review->entity_id)))
                                                        {{ $review->showAuthorName() }}
                                                    @else
                                                        @if($review->is_anon)
                                                            {{t('tender.contact_information_hidden')}}
                                                        @else
                                                            {{ $review->showAuthorName() }}
                                                        @endif
                                                    @endif
                                                </span>
                                                @endif

                                                <span class="reveiw__date">{{ $review->date->format('d.m.Y H:i') }}</span>
                                                @if($is_admin)
                                                    <span class="reveiw__date" style="margin-right:20px;">{{ $review->object_id}}</span>
                                                @endif
                                                @if($review->showGroup())
                                                    <div class="label_custom_reviews2" style="{{ $review->userGroup->color ? 'background:'.$review->userGroup->color : ''}}">{{ $review->userGroup->name }}</div>
                                                @endif
                                            </div>

                                            @include('partials/reviews/'.$review->schema)

                                            <div class="reviews__footer">
                                                <a href="" data-formjs="back" class="reviews__read-reviews">
                                                    {{t('tender.view_all_reviews')}}
                                                </a>
                                                <div data-thread="{{ $review->object_id }}" form-comment style="float:right">
                                                    <a href=""
                                                        class="open-comment__button"
                                                        data-formjs="jsonForm"
                                                        data-form="comment"
                                                        data-form-title="{{t('tender.your_comment')}}"
                                                        data-submit-button="{{t('tender.add_comment')}}"
                                                        data-model="comment"
                                                        data-validate="comment"
                                                        data-init="comment">
                                                            {{t('tender.add_comment')}}
                                                    </a>
                                                </div>
                                            </div>

                                            @if (sizeof($review->comments()))
                                                <div class="reviews__item reviews__item--deep-2 pt2 custom-review comments">
                                                    <h3>Коментарі ({{ sizeof($review->comments()) }}):</h3>
                                                </div>
                                                @foreach ($review->comments() as $comment)
                                                    @if(!empty($comment->json->comment))
                                                        <div class="reviews__item reviews__item--deep-2 custom-review comments">
                                                            <div class="reviews__item-inner">
                                                                <div class="reviews__header">
                                                                    <span class="reviews__author reviews__author--{{ $comment->is_anon ? 'not-':''}}confirmed">
                                                                        @if($comment->user && ($comment->user->ngo_profile || $comment->user->issetEdrpou($comment->entity_id)))
                                                                            {{ $comment->showAuthorName() }}
                                                                        @else
                                                                            @if($comment->is_anon)
                                                                                {{t('tender.contact_information_hidden')}}
                                                                            @else
                                                                                {{ $comment->showAuthorName() }}
                                                                            @endif
                                                                        @endif
                                                                    </span>
                                                                    <span class="reveiw__date">{{ $comment->date->format('d.m.Y H:i') }}</span>
                                                                    @if($is_admin)
                                                                        <span class="reveiw__date" style="margin-right:20px;">{{ $comment->object_id}}</span>
                                                                    @endif
                                                                    @if($comment->user && empty($comment->userGroup) && $comment->user->issetEdrpou($comment->entity_id))
                                                                        <span class="label_custom_reviews2">
                                                                            {{t('tender.answer_customer')}}
                                                                        </span>
                                                                    @elseif($comment->user && !empty($comment->userGroup) && $comment->user->issetEdrpou($comment->entity_id))
                                                                        <span class="label_custom_reviews2" style="{{ $comment->userGroup->color ? 'background:'.$comment->userGroup->color : ''}}">{{ $comment->userGroup->name }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="reviews__body">
                                                                    <div class="reviews__text">
                                                                        <p>{!! auto_format($comment->json->comment) !!}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif

                                            @if (sizeof($review->comments())>2)
                                                <div class="reviews__footer">
                                                    <div data-thread="{{ $review->object_id }}" form-comment>
                                                        <a href=""
                                                            class="open-comment__button"
                                                            data-formjs="jsonForm"
                                                            data-form="comment"
                                                            data-form-title="{{t('tender.your_comment')}}"
                                                            data-submit-button="{{t('tender.add_comment')}}"
                                                            data-model="comment"
                                                            data-validate="comment"
                                                            data-init="comment">
                                                                {{t('tender.add_comment')}}
                                                        </a>
                                                    </div>
                                                </div>
                                             @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="risk_desc bg_grey" tab-content>

                @if((!empty($user->is_tender_risks) || !empty($riskAccess))&& !empty($item->__risks))
                    @include('partials/blocks/tender/_risks')
                @endif

            </div>

        </div>
    @elseif ($error)
        <div style="padding:20px 20px 40px 10px;text-align:center">
            {!!$error!!}
        </div>
    @else
        <div style="padding:20px 20px 40px 10px;text-align:center">
            {{t('tender.tender_not_found')}}
        </div>
    @endif
@endsection

@push('scripts')
<script type="text/javascript">
        var tenderHashes = [
            '#block_info',
            '#block_docs',
            '#block_question',
            '#block_complaints',
            '#block_auction',
            '#block_awards',
            '#block_contract'
        ];

        var lotHashes = [
            '#block_info_lot',
            '#block_docs_lot',
            '#block_question_lot',
            '#block_complaints_lot',
            '#block_auction_lot',
            '#block_awards_lot',
            '#block_contract_lot'
        ];

        var hash = window.location.hash;

        if(hash && tenderHashes.indexOf(hash) > -1) {
            $('[data-hash="tender"]').click();
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 500);
        }
        else if(hash && lotHashes.indexOf(hash) > -1) {
            $('[data-hash="lot"]').click();
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 500);
        }

    $(function() {
        $('.link_back.cancel').on('click', function() {
            $(this).closest('form').hide();
            $('.link_back.submit').show();
            return false;
        });
       $('.link_back.submit').on('click', function() {
           $(this).hide();
           $(this).prev().show();
       });
        $('.goto-ngo').on('click', function() {
            $('[data-hash="ngo"]').trigger('click');
        });
    });
</script>
@endpush
