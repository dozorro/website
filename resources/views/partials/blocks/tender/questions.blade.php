@if($item->__icon!='pen')
    @if (!empty($item->__questions))
        <div class="margin-bottom-more" id="block_question{{ $lot_id  ? '_lot' : '' }}">
                <div class="block_title">
                    <h3>{{t('tender.questions_title')}}</h3>
                </div>
                <div class="row questions">
                    <div class="description-wr questions-block">
                        @foreach($item->__questions as $k=>$question)
                            <div class="questions-row{{$k>1?' none':' visible'}}">
                                <h4>{{$question->title}}</h4>
                                <div class="list_date inline-layout">
                                    <div class="item_date">{{t('tender.filing_date')}}: {{date('d.m.Y H:i', strtotime($question->date))}}</div>
                                    @if(!empty($question->dateAnswered))
                                        <div class="item_date">{{t('tender.date_answered')}}: {{date('d.m.Y H:i', strtotime($question->dateAnswered))}}</div>
                                    @endif
                                </div>

                                @if (!empty($question->description))
                                    <div class="question-one description-wr margin-bottom{{mb_strlen($question->description)>350?' croped':' open'}}">
                                        <div class="description">
                                            {{$question->description}}
                                        </div>

                                        @if (mb_strlen($question->description)>350)
                                            <a class="search-form--open">
                                                <span>{{t('interface.expand')}}</span>
                                                <span>{{t('interface.collapse')}}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                @if(!empty($question->answer))
                                    <div class="answer">
                                        <h4>{{t('tender.answer')}}:</h4>
                                        <div class="answer_text">{!!nl2br($question->answer)!!}</div>
                                    </div>
                                @else
                                    <div class="answer">
                                        <div class="answer_text">{{t('tender.no_answer')}}</div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @if (sizeof($item->__questions)>2)
                            <a class="question--open">
                                <span class="question-up">{{t('tender.expand_questions')}}: {{sizeof($item->__questions)}}</span>
                                <span class="question-down">{{t('tender.collapse_questions')}}</span>
                            </a>
                        @endif

                    </div>
                    {{--trans('tender.no_questions')--}}
                </div>

        </div>
    @endif
@endif