@extends('layouts.app')

@section('content')
    <div class="bg_grey p-t-15">
        <div class="container bg_white">
            <div class="back_link pull-left p-t-20">
                <a href="{{ route('page.reports') }}">{{ t('page.back_to_reports') }}</a>
            </div>
        </div>
    </div>
    @if(!empty($blocks))
        @foreach($blocks as $block)
            @include('partials.longread.' . $block->alias, [
                'data' => $block->value
            ])
        @endforeach
    @endif
@endsection