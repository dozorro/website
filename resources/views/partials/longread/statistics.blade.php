@if(!empty($block->data['stats']) && ($block->data['stats']->tenders_sum !== '' || $block->data['stats']->violation_sum  !== '' || $block->data['stats']->comments !== '' || $block->data['stats']->reviews !== ''))
    <div class="block_statistic bg_white">
        <div class="container">
            <div class="row ">
                @if($block->data['stats']->tenders_sum !== '')
                    <div class="col-md-3 col-xs-6 item inline-layout">

                            <div class="img-holder">
                                <img src="/assets/images/icon/icon-statistic1.svg">
                            </div>
                            <div class="text_statistic">
                                <div class="number_statistic">
                                    <span>{{ $block->data['stats']->tenders_sum }}</span>
                                    @if($block->data['stats']->tenders_sum_text !== '')
                                        <span class="comment_statistic">{{ $block->data['stats']->tenders_sum_text }}</span>
                                    @endif
                                </div>

                                <div class="name_statistic">{{t('longreads.risk_losing')}}</div>
                            </div>

                            {{--<div class="sb-t__row">
                                <a href="#" class="sb-t__button">@lang('search.best_company')</a>
                            </div>--}}

                    </div>
                @endif
                @if($block->data['stats']->violation_sum !== '')
                    <div class="col-md-3 col-xs-6 item inline-layout">

                        <div class="img-holder">
                            <img src="/assets/images/icon/icon-statistic2.svg">
                        </div>
                        <div class="text_statistic">
                            <div class="number_statistic">
                                <span>{{ $block->data['stats']->violation_sum }}</span>
                                @if($block->data['stats']->violation_sum_text !== '')
                                    <span class="comment_statistic">{{ $block->data['stats']->violation_sum_text }}</span>
                                @endif
                            </div>

                            <div class="name_statistic">@generateword($block->data['stats']->violation_sum)</div>
                        </div>

                        {{--<div class="sb-t__row">
                            <a href="#" class="sb-t__button">@lang('search.best_company')</a>
                        </div>--}}

                    </div>
                @endif
                @if($block->data['stats']->comments !== '')
                    <div class="col-md-3 col-xs-6 item inline-layout">

                        <div class="img-holder">
                            <img src="/assets/images/icon/icon-statistic3.svg">
                        </div>
                        <div class="text_statistic">
                            <div class="number_statistic">
                                <span>{{ $block->data['stats']->comments }}</span>
                            </div>

                            <div class="name_statistic">{{ t('search.comments') }}</div>
                        </div>
                        {{--<div class="sb-t__row">
                            <a href="#" class="sb-t__button">@lang('search.best_company')</a>
                        </div>--}}

                    </div>
                @endif
                @if($block->data['stats']->reviews !== '')
                    <div class="col-md-3 col-xs-6 item inline-layout">

                        <div class="img-holder">
                            <img src="/assets/images/icon/icon-statistic4.svg">
                        </div>
                        <div class="text_statistic">
                            <div class="number_statistic">
                                <span>{{ $block->data['stats']->reviews }}</span>
                            </div>

                            <div class="name_statistic">{{ t('search.reviews') }}</div>
                        </div>
                        {{--<div class="sb-t__row">
                            <a href="#" class="sb-t__button">@lang('search.best_company')</a>
                        </div>--}}

                    </div>
                @endif
            </div>
        </div>
    </div>
@endif