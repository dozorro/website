@extends('layouts/app')

@section('content')
    <div class="c-blog">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    @include('partials.blog.search', ['type' => 'blog'])
                    <div class="c-blog__left">
                        @if($main)
                            @include('partials.blog.main_post', ['post' => $main])
                        @endif
                        @if(count($posts) > 0)
                            <div class="c-blog__news-list full-blog">
                                @foreach($posts as $post)
                                    @if($post)
                                        @include('partials.blog.post')
                                    @endif
                                @endforeach
                            </div>
                            @if($posts->currentPage() < $posts->lastPage())
                            <div class="c-blog__more-button">
                                <div class="sb-more-button link_pagination2" data-current-page="{{ $posts->currentPage() }}" data-last-page="{{ $posts->lastPage() }}">{{ t('blog.load_more') }}</div>
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
            var q = '&q='+$('#form-blog input[name="q"]').val()+'&date_from='+$('#form-blog input[name="date_from"]').val()+'&date_to='+$('#form-blog input[name="date_to"]').val()+'&region='+$('#form-blog input[name="region"]').val();

            $.get(window.location.pathname+'?page=' + page + q,
                    function(data, textStatus, xhr)
                    {
                        $('.full-blog').append(data.data);
                        $('.link_pagination2').attr('data-current-page', page);

                        if(page >= last_page) {
                            $('.link_pagination2').hide();
                        }
                    });
        });
    });
</script>
@endpush

