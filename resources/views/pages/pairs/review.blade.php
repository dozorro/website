@extends('layouts.app')

@section('content')
    @if(!empty($answers))
    <div class="block_pairs clearfix">
        <div class="container" pair-panel>
            <p class="block_number inline-layout col-2">
                <span pairs-current-state>{{ $answers[0]->pair_id }}</span>
            </p>
        </div>
        <ul style="margin-bottom: 200px;height: 600px;">
            @include('partials._pair_item_review', [
                        'answers' => $answers,
                    ])
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