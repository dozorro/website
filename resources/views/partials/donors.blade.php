@if(!$ngo->donors->isEmpty())
    <div class="block_customers_logo3 ">
        <div class="inline-layout">
            <div class="item item-full">
                <div class="list_customers inline-layout">
                    <h3><span>{{ t('ngo.donors_title') }}</span></h3>
                    @foreach($ngo->getDonors() as $donor)
                        <a href="{{ $donor->link }}" class="item-customer" title="{{ $donor->title }}">
                            <div class="image-holder">
                                <div class="img" style="background: url({{ $donor->image() }}) center no-repeat;background-size: contain;"></div>
                            </div>
                            <div class="name">
                                {{ $donor->title }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif