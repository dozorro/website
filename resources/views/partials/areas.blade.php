@if($areas)
    <div class="container wide-table">
        <div class="row">
            <div class="tender--platforms margin-bottom-xl col-md-9">
                <div class="block_tender_info">

                    <h3>{{t('tender.apply_title')}}</h3>
                    {{t('tender.apply_info')}}
                    <div class="tender--platforms--list clearfix">
                        @foreach ($areas as $item)
                            <div class="item">
                                <div class="img-wr">
                                    <a href="{{str_replace('{tenderID}', $item->tenderID, $item->url)}}" target="_blank">
                                        <img src="{{ env('BACKEND_URL') }}{{ $item->image()->path }}" alt="{{ $item->title }}" title="{{ $item->title }}">
                                    </a>
                                </div>
                                <div class="border-hover">
                                    <div class="btn-wr">
                                        <a href="{{str_replace('{tenderID}', $item->tenderID, $item->url)}}" target="_blank" class="btn">{{t('tender.apply_go')}}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{--<a href="#" class="more margin-bottom"><i class="sprite-arrow-down"></i> Показати всіх</a>--}}
            </div>
        </div>
    </div>
@endif