@extends('layouts/app')

@section('head')
    @if ($item && !$error)
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{t('facebook.site_name')}}">
        <meta property="og:title" content="{{htmlentities($item->procuringEntity->name, ENT_QUOTES)}}">
        <meta property="og:url" content="{{env('ROOT_URL')}}/{{Request::path()}}">
        <meta property="og:image" content="{{env('ROOT_URL')}}/assets/images/social/fb.png">
        <meta property="og:description" content="{{!empty($item->title) ? htmlentities($item->title, ENT_QUOTES) : t('facebook.tender_no_name')}}">
    @endif
@endsection

@section('content')
    
    @if ($item && !$error)
        <div class="tender">
            <div class="tender--head gray-bg">
                <div class="container">
                    <div class="tender--head--title col-sm-9">
                        {{$item->budget->description}}
                    </div>
                        <div class="col-md-3 col-sm-3 tender--description--cost--wr">
                            <div class="gray-bg padding margin-bottom tender--description--cost">
                                {{t('plan.table.sum')}}
                                <div class="green tender--description--cost--number">
                                    <strong>{{str_replace(',00', '', number_format($item->budget->amount, 2, ',', ' '))}} <span class="small">{{$item->budget->currency}}</span></strong>
                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="tender--head--inf">
                                {{$item->planID}} ● {{$item->id}}
                            </div>
                            <div class="tender--head--inf margin-bottom">
                                {{ t('plan.date.posted_modified') }}: {{date('d.m.Y H:i', strtotime($item->dateModified))}}
                            </div>
                            @if(!empty($item->__is_sign))
                                <div data-js="tender_sign_check" data-url="{{$item->__sign_url}}">
                                    {{ t('plan.digital_signature_applied') }} <a href="" class="document-link" data-id="sign-check">

                                        {{ t('plan.digital_signature_applied.verify') }}
                                    </a>
                                    <div class="overlay overlay-documents">
                                        <div class="overlay-close overlay-close-layout"></div>
                                        <div class="overlay-box">
                                            <div class="documents" data-id="sign-check">
                                                <h4 class="overlay-title">{{ t('plan.signature_verification') }}</h4>
                                                <div class="loader"></div>
                                                <div id="signPlaceholder"></div>
                                            </div>
                                            <div class="overlay-close"><i class="sprite-close-grey"></i></div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div>{{ t('plan.electronic_digital_signature_not_imposed') }}</div>
                            @endif                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="tender--description">
                <div class="container">
                    <h2>ФОРМА РІЧНОГО ПЛАНУ ЗАКУПІВЕЛЬ</h2>
                    @if (!empty($item->budget->year))
                        <div style="margin:-30px 0px 40px 0px">{{ t('plan.on') }} {{$item->budget->year}} {{ t('plan.year') }}</div>
                    @endif
                </div>
            </div>            

            <div class="tender--description margin-bottom-xl">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-9">
                            <h3>{{ t('plan.information_about_customer') }}</h3>
                            <div>1. {{ t('plan.name_customer') }}: @if(!empty($item->procuringEntity->identifier->legalName)){{$item->procuringEntity->identifier->legalName}}@elseif(!empty($item->procuringEntity->name)){{$item->procuringEntity->name}}@endif</div>
                            <div>2. {{ t('plan.code_according_to_customer_EDRPOU') }}: #{{$item->procuringEntity->identifier->id}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tender--description margin-bottom-xl">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-9">
                            <h3>{{ t('plan.purchase_information') }}</h3>
                            <div>3. {{ t('plan.exact_name_procurement') }}:
                                <strong>
                                    {{$item->budget->description}}@if(!empty($item->items)), {{implode(', ', array_pluck($item->items, 'description'))}}@endif
                                </strong>
                            </div>
                            <br>
                            @if(!empty($item->__items))
                                <div class="margin-bottom">4. {{ t('plan.codes_relevant_classifications_subject_procurement') }}:</div>
                                @foreach($item->__items as $one)
                                    <div class="margin-bottom">
                                        <div class="description-wr">
                                            <div class="tender--description--text description" style="margin-left:20px;">
                                               {{t('scheme.'.$one->scheme)}}: {{$one->id}} — {!!nl2br($one->description)!!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($item->classification)
                                    @foreach($item->classification as $one)
                                        <div class="tender--description--text description" style="margin-left:20px;">
                                           {{t('scheme.'.$one->scheme)}}: {{$one->id}} — {!!nl2br($one->description)!!}
                                        </div>
                                    @endforeach
                                @endif
                            @else
                                <div class="margin-bottom">4. {{ t('plan.codes_relevant_classifications_subject_procurement') }}: <strong>{{ t('plan.none') }}</strong></div>
                            @endif
                            <br>
                            @if(!empty($item->__items_kekv))
                                <div class="margin-bottom">5. {{ t('plan.code_according_KEKV') }}: </div>
                                @foreach($item->__items_kekv as $one)
                                    <div class="margin-bottom">
                                        <div class="description-wr">
                                            <div class="tender--description--text description" style="margin-left:20px;">
                                                {{$one->scheme}}: {{$one->id}} — {!!nl2br($one->description)!!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="margin-bottom">5. {{ t('plan.code_according_KEKV') }}: <strong>{{ t('plan.none') }}</strong></div>
                            @endif
                            <br>
                            <div>6. {{ t('plan.size_budget_appropriation') }}: <strong>{{str_replace(',00', '', number_format($item->budget->amount, 2, ',', ' '))}} {{$item->budget->currency}}</strong></div>
                            <br>
                            <div>7. {{ t('plan.purchasing procedure') }}: <strong>{{$item->tender->__procedure_name}}</strong></div>
                            <br>
                            <div>
                                8. {{ t('plan.tentative_start_procurement_procedure') }}:
                                <strong>
                                    @if ($item->__is_first_month)
                                        {{$item->__is_first_month}}
                                    @else
                                        {{date('d.m.Y', strtotime($item->tender->tenderPeriod->startDate))}}
                                    @endif
                                </strong>
                            </div>
                            <br>
                            @if(!empty($item->budget->notes))
                                <div>9. {{ t('plan.remarks') }}: <strong>{{$item->budget->notes}}</strong></div>
                            @endif
                        </div>
                    </div>
                </div>
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

    @include('partials.areas')
@endsection