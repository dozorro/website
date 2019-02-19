@extends('layouts/app')

@section('content')
    <div class="c-blog">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    @include('partials.blog.search', ['type' => 'news'])
                    <div class="c-blog__left">
                        @if(count($news) > 0)
                            <div class="c-blog__news-list news_feed">
                                <h3>{{ t('news.title') }}</h3>
                                <div class="list_news">
                                @foreach($groups as $date => $posts)
                                    <div class="date_news_feed" data-date="{{$date}}">
                                        {{ $date }}
                                    </div>
                                    @foreach($posts as $post)
                                        @include('partials.news.post')
                                    @endforeach
                                @endforeach
                                </div>
                            </div>
                            @if($news->currentPage() < $news->lastPage())
                            <div class="c-blog__more-button">
                                <div class="sb-more-button link_pagination2" data-current-page="{{ $news->currentPage() }}" data-last-page="{{ $news->lastPage() }}">{{ t('blog.load_more') }}</div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
                @include('partials.blog.sidebar', ['banner' => $main])
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function(){

        $.fn.datepicker.dates['ua'] = {
            days: ["Неділя", "Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота"],
            daysShort: ["Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            daysMin: ["Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            months: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Версень', 'Жовтень', 'Листопад', 'Грудень'],
            monthsShort: ['Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер', 'Лип', 'Сер', 'Вер', 'Жов', 'Лис', 'Гру'],
            today: "Сьогодні",
            clear: "Очистити",
            format: "dd.mm.yyyy",
            titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
            weekStart: 1
        };

        $('.datepicker').datepicker({
            language: 'ua'
        });

        $('.link_pagination2').on('click', function() {
            var page = parseInt($(this).attr('data-current-page'));
            var last_page = parseInt($(this).attr('data-last-page'));
            var page = page + 1;

            var dates = $('.list_news').find('.date_news_feed').length;
            var date = $('.list_news').find('.date_news_feed').eq(dates-1).data('date');

            var q = '&q='+$('#form-blog input[name="q"]').val()+'&date_from='+$('#form-blog input[name="date_from"]').val()+'&date_to='+$('#form-blog input[name="date_to"]').val()+'&region='+$('#form-blog input[name="region"]').val();

            $.get('{{ route('page.news') }}?page=' + page + '&date='+date+q,
                function(data, textStatus, xhr)
                {
                    $('.list_news').append(data.data);
                    $('.link_pagination2').attr('data-current-page', page);

                    if(page >= last_page) {
                        $('.link_pagination2').hide();
                    }

                    /*
                    var dates = $('.list_news').find('.date_news_feed').each(function (index) {
                        
                    });

                    for(var i =0; i < dates.length; i++) {
                        if(dates[i].data('date') == dates[i+1].data('date')) {
                            $('.list_news').find('.date_news_feed').eq(i+1).remove();
                        }
                    }*/

            });
        });
    });
</script>
@endpush
