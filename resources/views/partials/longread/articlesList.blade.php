@if(!$block->data['articles']->isEmpty())
    <div class="c-n p-b-0">
        <div class="container">
            <div class="c-n__list longread-blog">
            @foreach($block->data['articles'] as $article)
                @include('partials.blog.post', ['post'=>$article, 'is_small'=>true ])
            @endforeach
            </div>
            @if($block->data['articles']->currentPage() < $block->data['articles']->lastPage())
            <div class="c-n__more-button">
                <div class="sb-more-button link_pagination2" data-count="{{ $block->data['count'] }}" data-current-page="{{ $block->data['articles']->currentPage() }}" data-last-page="{{ $block->data['articles']->lastPage() }}">{{ t('blog.load_more') }}</div>
            </div>
            @endif
        </div>
    </div>
    @push('scripts')
    <script>
        $(document).ready(function(){
            $('.link_pagination2').on('click', function() {
                var page = parseInt($(this).attr('data-current-page'));
                var count = parseInt($(this).attr('data-count'));
                var last_page = parseInt($(this).attr('data-last-page'));
                var page = page + 1;

                $.get('{{ route('page.blog.by_ajax') }}?page=' + page + '&count=' + count,
                    function(data, textStatus, xhr)
                    {
                        $('.longread-blog').append(data.data);
                        $('.link_pagination2').attr('data-current-page', page);

                        if(page >= last_page) {
                            $('.link_pagination2').hide();
                        }
                    });
            });
        });
    </script>
    @endpush
@endif