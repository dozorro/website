@if(!empty($block->data['partners']))

    <div class="block_customers_logo_tabs">

        <div class="container">
            <div class="">

                <div class="tab-content">
                    @if(!empty($block->data['partners']))
                        <div class="tab-pane active" id="block_customers" role="tabpanel">

                            <div class="list_customers inline-layout">
                                @foreach($block->data['partners'] as $customer)
                                    <div class="bg_white" style="padding: 10px;width: 185px;">
                                        <a target="_blank" href="{{ $customer->url }}" class="item-customer item-customer2">
                                            <div class="image-holder">
                                                <div class="img" style="background: url({{ $customer->image() }}) center no-repeat;background-size: contain;"></div>
                                            </div>
                                            <div class="info" style="display: inline-block;">
                                                <div class="info_text" style="margin-left: -15px;width: 200px;">
                                                    <div>
                                                        {{ $customer->text }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

@endif