<li data-id="{{ $k }}" style="opacity:0.001;pointer-events:none">
    <div class="text-center js_pairs" pair-slider data-swipe="{{ $user->is_pairs }}">
        <div></div>
        <div class="block_pairs__bg ">
            <div>
                <div class="inline-layout">
                    <div class="item" style="width: 100%;padding: 10px;margin: 10px;">
                        {!! $text !!}
                    </div>
                    <div class="item" style="width: 100%;">
                        @if($user->is_pairs_button)
                        <a class="pairs_button" href="/pairs/update/{{ $k }}/?answer=yes" style="outline:none">
                            {{ t('pairs.next') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div></div>
    </div>
</li>