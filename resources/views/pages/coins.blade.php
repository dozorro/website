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
                @foreach($badges->badgesList as $badge)
                    <img src="{{ $badge->image }}">
                @endforeach
                @if($blocks)
                    <h3>{{ $blocks->title }}</h3>
                    <div class="block_text">
                        <p>
                            {{ $blocks->desc }}
                        </p>
                    </div>
                @endif
            </div>

            @if(!$ngos->isEmpty())
                <div class="overflow-table">
                    <table>
                        <thead>
                        <tr>
                            <th width="10%">{{t('rating.position')}}</th>
                            <th width="15%">{{t('rating.level')}}</th>
                            <th width="15%">{{t('rating.winners')}}</th>
                            <th width="50%">{{t('rating.name')}}</th>
                        </tr>
                        </thead>
                        <tbody id="coins-content">
                        @foreach($ngos as $k => $ngo)
                            @if($ngo->sumCoins)
                            <tr>
                                <td>{{ $k+1 }}</td>
                                <td><img src="{{asset('assets/images/d-coin.png')}}"></td>
                                <td>{{ $ngo->sumCoins }}</td>
                                <td>
                                    <a href="{{ route('page.rating', ['slug' =>'dozorrocoin', 'ngo' => $ngo->slug]) }}">{{ $ngo->title }}</a>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if($ngos->currentPage() < $ngos->lastPage())
                    <br>
                    <div id="for-spinner" class="link_pagination" data-current-page="{{$ngos->currentPage()}}" data-last-page="{{$ngos->lastPage()}}">{{ t('ngo.show_more') }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var opts = {
            lines: 13 // The number of lines to draw
            , length: 28 // The length of each line
            , width: 4 // The line thickness
            , radius: 3 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#000' // #rgb or #rrggbb or array of colors
            , opacity: 0.25 // Opacity of the lines
            , rotate: 0 // The rotation offset
            , direction: 1 // 1: clockwise, -1: counterclockwise
            , speed: 1 // Rounds per second
            , trail: 60 // Afterglow percentage
            , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            , zIndex: 2e9 // The z-index (defaults to 2000000000)
            , className: 'spinner' // The CSS class to assign to the spinner
            , top: '50%' // Top position relative to parent
            , left: '40%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'relative' // Element positioning
        }
        var target = document.getElementById('for-spinner');
        var spinner = new Spinner(opts).spin(target);

        $('.spinner').hide();

        $('.link_pagination').on('click', function() {
            $('.spinner').show();
            var page = parseInt($(this).attr('data-current-page'))+1;
            var n = (page-1)*10;
            var paginator = $(this);
            paginator.attr('data-current-page', page);

            $.get('{{route('page.rating', ['slug' => 'dozorrocoin'])}}'+'?page='+page,
                function(data, textStatus, xhr)
                {


                    var _data = data.data;
                    var html;
                    var end = false;

                    for(var i = 0 ; i < _data.length; i++) {
                        if(parseInt(_data[i].sumCoins) > 0) {
                            html += "<tr><td>" + (n+(i + 1)) + "</td><td><img src=\"{{asset('assets/images/d-coin.png')}}\"></td><td>" + _data[i].sumCoins + "</td><td><a href=\"{{ route('page.rating', ['slug' =>'dozorrocoin']) }}" + _data[i].slug + '">' + _data[i].title + "</a></td></tr>"
                        } else {
                            end = true;
                            break;
                        }
                    }

                    $('#coins-content').append(html);
                    $('.spinner').hide();

                    if(end || _data.length <= 0) {
                        paginator.remove();
                    }
            });
        });

    });
</script>
@endpush