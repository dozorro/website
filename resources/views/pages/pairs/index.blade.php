@extends('layouts.app')

@section('content')
    @if($pairsTotal === 'auth')
        <div class="login_link">
            <a href="#" data-formjs="open_login">{{t('user.login')}}</a>
        </div>
    @elseif($pairsTotal)
    <div class="block_pairs clearfix" data-js="pairs" data-total="{{ $pairsTotal }}" data-dt="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}">
        <div class="container" style="opacity:0.001" pair-panel>
            <p class="block_number inline-layout col-@if($showCancel){{'3'}}@else{{'2'}}@endif">
                <span class="text-left" @if(!$showCancel){{'style=display:none;'}}@endif>
                    <a href="" pair-cancel>{{ t('pairs.answer_cancel') }}</a>
                </span>
                <span pairs-current-state @if($showCancel){{'style=text-align:center;'}}@endif></span>
                <span class="text-right">
                    <a href="" pair-skip>{{ t('pairs.answer_skip') }}</a>
                </span>
            </p>

            @if(@$showIndicators)
            <p class="block_number inline-layout col-2 indicator-page">
                <span class="text-left">
                    <a class="indicator-page-left" target="_blank" href="/indicators/{{ end($pairs)->__left_tender_id }}">{{ end($pairs)->__left_tender_id }}</a>
                </span>
                <span class="text-right">
                    <a class="indicator-page-right" target="_blank" href="/indicators/{{ end($pairs)->__right_tender_id }}">{{ end($pairs)->__right_tender_id }}</a>
                </span>
            </p>
            @endif
        </div>
        <ul style="margin-bottom: 200px;height: 600px;">
            @if(isset($other))
                {!! $other !!}
            @else
                @foreach($pairs as $k => $pair)
                    @if(is_numeric($k))
                        @include('partials._pair_item', [
                                'pair' => $pair,
                            ])
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
    <div class="block_pairs pair_done" style="display:none" id="pair-done">
        {{ t('pairs.done') }}
    </div>
    @else
        <div class="block_pairs pair_done" id="pair-done">
            {{ t('pairs.done') }}
        </div>
    @endif
@endsection