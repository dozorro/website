<div class="block_info_go">

    <div class="block_info_go_main">
        @include('partials._ngo_info_main')
    </div>

</div>

@if($showBadges && !$ngo->badges->isEmpty())
    <div class="ngo_badges">
        @include('partials/badges', ['badges' => $ngo->badges, 'featureBadges' => true])
    </div>
@endif
