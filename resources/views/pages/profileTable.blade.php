@extends('layouts.app')

@section('content')

    @include('partials/profile/_header')

    <div class="bg_grey block_statistic">
        <div class="container">
            <div class="bg_white">

                @if(!empty($blocks))
                    <?php
                        $closeDiv = true;
                    ?>

                    @foreach($blocks as $k => $block)

                        <div class="row list_graph block_table">

                        @include('partials.longread.' . $block->alias, [
                            'data' => $block->data['data'],
                            'results' => $block->data['results'],
                            'total' => $block->data['total'],
                            'single' => true,
                        ])

                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials/profile/handler', [
        'saveTemplateRoute' => route('template_save'),
        'saveCustomTemplateRoute' => route('template_save_custom'),
        'profileRoute' => route('page.profile_by_id', ['scheme'=>$scheme]),
    ])
@endpush
