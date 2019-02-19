@extends('layouts.app')

@section('content')
<div class="page_table">
    <div class="back_link" style="width: 840px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 15px;
    padding-right: 15px;">
        <a href="{{ route('page.rating', ['slug' => 'dozorrocoin']) }}">{{t('page.rating.go_back')}}</a>
    </div>
    <br>
    <div class="container">
        @if(!$coins->isEmpty())
            <h3>{{ $ngo->title }}</h3>
            <div class="overflow-table">
                <table>
                    <thead>
                    <tr>
                        <th width="5%">{{ t('ngo.coin.dt') }}</th>
                        <th width="20%">{{ t('ngo.coin.type') }}</th>
                        <th width="10%">{{ t('ngo.coin.sum') }}</th>
                        <th width="35%">{{ t('ngo.coin.comment') }}</th>
                        <th width="17%">{{ t('ngo.coin.author') }}</th>
                    </tr>
                    </thead>
                    <tbody id="coins-content">
                    @foreach($coins AS $coin)
                    <tr>
                        <td>
                            {{ $coin->_dt }}
                        </td>
                        <td>
                            {{ $coin->_type }}
                        </td>
                        <td>
                            {{ $coin->sum }}
                        </td>
                        <td>
                            {{ $coin->comment }}
                        </td>
                        <td>
                            {{ $coin->author }}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <br>
            @if($coins->currentPage() < $coins->lastPage())
            <div id="for-spinner" class="link_pagination" data-current-page="{{$coins->currentPage()}}" data-last-page="{{$coins->lastPage()}}">{{ t('ngo.show_more') }}</div>
            @endif
        @endif
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
            $(this).attr('data-current-page', page);

            $.get('{{route('page.rating', ['slug' => 'dozorrocoin', 'ngo' => $ngo->slug])}}'+'?page='+page,
                    function(data, textStatus, xhr)
                    {
                        var _data = data.data;
                        var html;

                        for(var i = 0 ; i < _data.length; i++) {
                            html += "<tr><td>"+_data[i]._dt+"</td><td>"+_data[i]._type+"</td><td>"+_data[i].sum+"</td><td>"+_data[i].comment+"</td><td>"+_data[i].author+"</td></tr>"
                        }

                        $('#coins-content').append(html);
                        $('.spinner').hide();
                });
        });

    });
</script>
@endpush