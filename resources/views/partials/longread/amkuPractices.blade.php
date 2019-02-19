@if(!empty($block->data['practices']))
    <div class="block_nadporogovi_zakupivli">
        <div class="container">
            <div class="list_nadporogovi_zakupivli inline-layout">
                @foreach($block->data['practices'] as $item)
                    <a href="{{ route('page.amkupractice.item', ['slug' => $item->slug]) }}" class="item inline-layout{{strpos($_SERVER['REQUEST_URI'], 'amku-practice/'.$item->slug)!==false ? ' selected':'' }}">
                        <div class="name">{{ $item->name }}</div>
                        <div class="img-holder">
                            <img src="{{ $item->image() }}">
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
