@extends('layouts/app')

@section('content')

    @if(!empty($content_blocks))
        @foreach($content_blocks as $block)
            @include('partials.longread.' . $block->alias, [
                'data' => $block->value
            ])
        @endforeach    
    @endif
    <div class="block_nadporogovi_zakupivli">
        <div class="container">
            <h2>{{t('complaints.'.$type.'.title')}}</h2>
            <div class="desc">{{t('complaints.'.$type.'.info')}}</div>
            <div class="list_nadporogovi_zakupivli inline-layout">
                @foreach($items as $item)
                    <a href="{{ route('page.complaints.item', ['type' => $type, 'slug' => $item->slug]) }}" class="item inline-layout{{strpos($_SERVER['REQUEST_URI'], 'complaints/'.$type.'/'.$item->slug)!==false ? ' selected':'' }}">
                        <div class="name">{{ $item->title }}</div>
                        <div class="img-holder">
                            <img src="{{ $item->image() }}">
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @include('partials/longread/complaint_types')
@endsection