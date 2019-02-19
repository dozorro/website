<div class="list_badge inline-layout">
    @foreach($badges as $badge)
        <div class="info" >
            <img class="badge_icon" src="{{ $badge->image }}" style="cursor: pointer;" onclick="window.location = '{{ route('page.rating') }}'">
            <div class="info_text">
                <div>
                    {{ $badge->name }}
                    @if($badge->getOriginal('pivot_is_auto'))
                        <p>{{t('ngo.badge.forms')}} {{ $badge->forms }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    @if($featureBadges)
        @foreach($badges as $badge)
            @if($badge->next_badge)
                <div class="info disabled">
                    <img class="badge_icon" src="{{ $badge->next_badge->image }}" onclick="window.location = '{{ route('page.rating') }}'">
                    <div class="info_text">
                        <div>
                            <div>
                                {{ $badge->next_badge->name }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>