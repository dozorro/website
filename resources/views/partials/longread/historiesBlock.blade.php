@if(!empty($data->items) || !empty($data->link1) || !empty($data->link2))
    <div class="bg_grey">
        <div class="container bg_white">
            <div class="block_history">
                <h4>{{ t('page.tool.histories') }}</h4>
                <div class="list_history inline-layout">
                    @if(!empty($data->items))
                        @foreach($data->items as $k => $item)
                        <div class="item" data-history="{{ $k }}" style="cursor: pointer;">
                            <div class="image-holder">
                                <div class="img" style="background-image: url({{ \App\Helpers::getMediaPath($item->image) }})"></div>
                            </div>
                            <h5>{{ $item->title }}</h5>
                        </div>
                        @endforeach
                    @endif
                </div>
                @if(!empty($data->items))
                    <div class="block_faq" style="display:none;">
                        <div class="item" style="width: 100%;">
                            <h5></h5>
                            <div class="faq_text">
                                @foreach($data->items as $k => $item)
                                    @foreach($item->_items as $_item)
                                        <p>
                                            <a data-history-links="{{ $k }}" href="{{ $_item->link }}" target="_blank" style="display: none;">{{ $_item->name }}</a>
                                        </p>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="block_link inline-layout">
                    <div style="width: 60%;">{!! $data->text !!}</div>
                    <div>
                        @if(!empty($data->link1))
                            <a href="{{ $data->link1 }}" class="" target="_blank">{{ t('page.tool.histories.others') }}</a>
                        @endif
                        @if(!empty($data->link2))
                            <a href="{{ $data->link2 }}" class="bg_color" target="_blank">{{ t('page.tool.histories.subs') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    $(function() {
        $('[data-history]').on('click', function() {
            var th = $(this);
            var hk = th.data('history');
            $('[data-history]').removeClass('active');
            th.addClass('active');
            th.closest('.block_history').find('[data-history-links]').hide();
            th.closest('.block_history').find('[data-history-links="'+hk+'"]').show();
            th.closest('.block_history').find('.block_faq').show();

            if(!th.closest('.block_history').find('.block_faq .item').hasClass('open')) {
                th.closest('.block_history').find('.block_faq .item h5').trigger('click');
            }
        });
    });
</script>
@endpush

@endif