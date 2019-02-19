@extends('layouts.app')

@section('content')
    <div class="bg_grey p-t-15">
        <div class="container bg_white">
            <div class="back_link pull-left p-t-20">
                <a href="{{ route('page.tools') }}">{{ t('page.back_to_tools') }}</a>
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