@extends('layouts.app')

@section('content')
<div class="c-b">
    <div class="container ">

        <div class="bg_white page_badge_open">
            <div class="back_link pull-left">
                <a href="{{ route('page.ratings') }}">{{t('page.rating.go_back')}}</a>
            </div>
            <div class="link_share pull-right">
                <label>{{t('page.rating.share')}} </label>
                <div class="likely inline-layout">
                    <div class="twitter"></div>
                    <div class="facebook"></div>
                    <div class="linkedin"></div>
                    <div class="gplus"></div>
                </div>
            </div>

            <div class="info_badge text-center clearfix">
                @if(isset($badges->badgesList))
                    @foreach($badges->badgesList as $badge)
                        <img src="{{ $badge->image }}">
                    @endforeach
                @endif
                @if($badges)
                    <h3>{{ $badges->badgeName }}</h3>
                    <div class="block_text">
                        <p>
                            {{ $badges->badgeDesc }}
                        </p>
                    </div>
                @endif
            </div>

            @if($ngos && !$ngos->isEmpty())
                @if($badges->is_hand)
                    <div class="overflow-table">
                        <table>
                            <tbody>
                            <tr>
                                <th width="15%">{{t('rating.level')}}</th>
                                <th width="50%">{{t('rating.name')}}</th>
                            </tr>
                            @foreach($ngos as $k => $ngo)
                                <tr>
                                    <td>@if(isset($badges->badgesList[0]))<img src="{{ $badges->badgesList[0]->image }}">@endif</td>
                                    <td>
                                        <a href="{{ route('page.ngo', ['slug' => $ngo->slug]) }}">{{ $ngo->title }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="overflow-table">
                        <table>
                            <tbody>
                            <tr>
                                <th width="10%">{{t('rating.position')}}</th>
                                <th width="15%">{{t('rating.level')}}</th>
                                <th width="15%">{{t('rating.winners')}}</th>
                                <th width="50%">{{t('rating.name')}}</th>
                            </tr>
                            @foreach($ngos as $k => $ngo)
                                <tr>
                                    <td>{{ $k+1 }}</td>
                                    <td>@if($ngo->_badge)<img src="{{ $ngo->_badge->image}}">@endif</td>
                                    <td>{{ $ngo->forms }}</td>
                                    <td>
                                        <a href="{{ route('page.ngo', ['slug' => $ngo->slug]) }}">{{ $ngo->title }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection