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
                                    @if(!empty(trim($practice->href)))
                                        <a href="{{ $practice->href }}" target="_blank">Закупівля</a>
                                    @endif
                                    @if(!empty(trim($practice->href_decision)))
                                        <a href="{{ $practice->href_decision }}" target="_blank" @if(!empty($practice->href)) style="margin-left: 20px" @endif >Рішення АМКУ</a>
                                    @endif
                                    @if(!empty($practice->status))
                                        <span class="amku-status-{{ $practice->status }}" @if(!empty($practice->href) || !empty($practice->href_decision)) style="float:right; margin-left: 20px" @endif >{{ trans('interface.amku_status.'.$practice->status) }}</span>
                                    @endif
                                    <br>
                                    @if(!empty(trim(strip_tags($practice->comment1))))
                                        <div class="one-item">
                                            <div class="subtitle">Зміст вимоги</div>
                                            {!! $practice->comment1 !!}
                                        </div>
                                    @endif
                                    @if(!empty(trim(strip_tags($practice->comment2))))
                                        <div class="one-item">
                                            <div class="subtitle">Рішення Колегії АМКУ</div>
                                            {!! $practice->comment2 !!}
                                        </div>
                                    @endif
                                    @if(!empty(trim(strip_tags($practice->comment3))))
                                        <div class="one-item">
                                            <div class="subtitle">Коментар</div>
                                            {!! $practice->comment3 !!}
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
    @include('partials/longread/amkuPractices')
@endsection
