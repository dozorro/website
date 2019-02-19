@extends('layouts/app')

@section('content')
    <div class="c-text bg-grey">
        <div class="container">
            <h2>{{ $practice->name }}</h2>
        </div>
    </div>
    @if(!empty($practice))
        <div class="container bg_white">
            <div class="block_faq" style="padding-left:0px; padding-right:0px;">
                @foreach($practice->items as $item)
                    @if(!empty($item->practice))
                        <div class="item">
                            @if($item->title)
                                <h5>{{ $item->title }}</h5>
                            @endif
                            @foreach($item->practice as $practice)
                                <div class="faq_text amcu_item">
                                    @if(!empty($practice->status))
                                        <span class="amku-status-red">{{ t('interface.judge_status.'.$practice->status) }}</span>
                                    @endif
                                    <br>
                                    @if(!empty(trim(strip_tags($practice->comment1))))
                                        <div class="one-item">
                                            <div class="subtitle">{{ t('judge.comment1') }}</div>
                                            {!! $practice->comment1 !!}
                                        </div>
                                    @endif
                                    @if(!empty(trim(strip_tags($practice->comment2))))
                                        <div class="one-item">
                                            <div class="subtitle">{{ t('judge.comment2') }}</div>
                                            {!! $practice->comment2 !!}
                                        </div>
                                    @endif
                                    @if(!empty(trim(strip_tags($practice->comment3))))
                                        <div class="one-item">
                                            <div class="subtitle">{{ t('judge.comment3') }}</div>
                                            {!! $practice->comment3 !!}
                                        </div>
                                    @endif
                                    @if(!empty(trim(strip_tags($practice->comment4))))
                                        <div class="one-item">
                                            <div class="subtitle">{{ t('judge.comment4') }}</div>
                                            {!! $practice->comment4 !!}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
    @include('partials/longread/judgePractices')
@endsection
