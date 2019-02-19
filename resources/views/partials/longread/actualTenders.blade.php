@if(!empty($block->data['tenders']))
<div class="c-hot">
    <div class="container">
        <div class="row">

                <div class="col-md-6">
                    <div class="c-list-card">
                        <div class="c-list-card__inner">
                            <h3 class="c-list-card__header">{{t('tender.actual_tenders')}}</h3>
                            @foreach($block->data['tenders'] as $item)

                                <div class="sb-top-item">
                                    <h3><a href="{{ route('page.tender_by_id', ['id' => $item->data->tenderID]) }}">{{ $item->data->title }}</a></h3>
                                    <div class="sb-top-item__table">
                                        <div class="sb-top-item__row">
                                            <div class="sb-top-item__cell">
                                                {{t('tender.customer')}}:
                                            </div>
                                            <div class="sb-top-item__cell">
                                                {{ $item->data->procuringEntity->name }}
                                            </div>
                                        </div>
                                        <div class="sb-top-item__row">
                                            <div class="sb-top-item__cell">
                                                {{t('tender.status')}}:
                                            </div>
                                            <div class="sb-top-item__cell">
                                                {{ !empty($dataStatuses[$item->data->status]) ? $dataStatuses[$item->data->status] : t('tender.not_specified')}}
                                            </div>
                                        </div>
                                        <div class="sb-top-item__row">
                                            <div class="sb-top-item__cell">
                                                {{t('tender.reviews')}}:
                                            </div>
                                            <div class="sb-top-item__cell">
                                                {{ sizeof($item->reviews) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="c-list-card__link-wrap">
                                <a href="#">{{t('tender.all_tenders')}}</a>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>
@endif