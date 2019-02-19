@if(!empty($block->data->customers) || !empty($block->data->ngo))

    <div class="block_customers_logo_tabs">

        <div class="container">
            <div class="bg_white">

                <ul class="nav nav-tabs inline-layout" role="tablist">
                    @if(!empty($data->customers_title) && !empty($block->data->customers))
                        <li class="active">
                            <a href="#block_customers" data-toggle="tab">{{ $data->customers_title }}</a>
                        </li>
                    @endif

                    @if(!empty($data->ngos_title))
                        <li @if(empty($block->data->customers)) class="active" @endif>
                            <a href="#block_ngo" data-toggle="tab">{{ $data->ngos_title }}</a>
                        </li>
                    @endif

                </ul>

                <div class="tab-content">
                    @if(!empty($block->data->customers))
                        <div class="tab-pane active" id="block_customers" role="tabpanel">

                            <div class="list_customers inline-layout">
                                @foreach($block->data->customers as $customer)
                                    <a href="{{ $customer['customer']->getOriginal('url') }}" class="item-customer" title="{{ $customer['customer']->title }}">
                                        <div class="image-holder">
                                            <div class="img" style="background: url({{ $customer['customer']->logo }}) center no-repeat;background-size: contain;"></div>
                                        </div>
                                        <div class="name">{{ $customer['customer']->title }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if(!empty($block->data->ngo))
                        <div class="tab-pane @if(empty($block->data->customers)) active @endif" id="block_ngo" role="tabpanel">

                            <div class="list_customers inline-layout">
                                @foreach($block->data->ngo as $customer)
                                    <a href="{{ route('page.ngo', ['slug' => $customer['customer']->slug]) }}" class="item-customer" title="{{ $customer['customer']->title }}">

                                        <div class="image-holder">
                                            <div class="img" style="background: url({{ $customer['customer']->logo }}) center no-repeat;background-size: contain;"></div>
                                        </div>
                                        <div class="name">{{ $customer['customer']->title }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

@endif