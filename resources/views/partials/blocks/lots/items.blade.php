@if(!empty($item->__items))
	<div class="margin-bottom-more">
		<div class="block_title">
			<h3>{{t('tender.items')}}</h3>
		</div>
		@foreach($item->__items as $one)
			<p class="description-wr{{!empty($one->description) && mb_strlen($one->description)>350?' croped':' open'}}">
				@if (!empty($one->description))
					<strong>{!!nl2br($one->description)!!}</strong>
					@if (mb_strlen($one->description)>350)
						<a class="search-form--open"><i class="sprite-arrow-down"></i>
							<span>{{t('interface.expand')}}</span>
							<span>{{t('interface.collapse')}}</span>
						</a>
					@endif
				@endif
				<br>
			</p>
			<div class="row">
				<div class="col-md-6">
					<div class="tender-description__item">
						<div class="tender-description__title">{{t('tender.item_quantity')}}:</div>
						<div class="tender-description__text">
							{{!empty($one->quantity)?$one->quantity:''}} @if(!empty($one->unit->code)){{t('measures.'.$one->unit->code.'.symbol')}}@endif
						</div>
					</div>
				</div>
				@if (!empty($one->classification))
					<div class="col-md-6">
						<div class="tender-description__item">
							<div class="tender-description__title">{{t('tender.cpv')}}:</div>
							<div class="tender-description__text">
								{{$one->classification->id}} — {{$one->classification->description}}
							</div>
						</div>
					</div>
				@else
					<div class="tender-date">{{t('tender.no_cpv')}}</div>
				@endif
				@if(!empty($one->additionalClassifications[0]))
					<div class="col-md-6">
						<div class="tender-description__item">
							<div class="tender-description__title">{{t('tender.dkpp')}}:</div>
							<div class="tender-description__text">
								{{$one->additionalClassifications[0]->id}} — {{$one->additionalClassifications[0]->description}}
							</div>
						</div>
					</div>
				@else
					<div class="col-md-6">
						<div class="tender-description__item">
							<div class="tender-description__title">{{t('tender.no_dkpp')}}:</div>
						</div>
					</div>
				@endif
				<div class="col-md-6">
					<div class="tender-description__item">
						<div class="tender-description__title">{{ t('tender.delivery') }}:</div>
						<div class="tender-description__text">
							{{!empty($one->deliveryAddress->postalCode) ? $one->deliveryAddress->postalCode : ''}}{{!empty($one->deliveryAddress->region) ? ', '.$one->deliveryAddress->region : ''}}{{!empty($one->deliveryAddress->locality) ? ', '.$one->deliveryAddress->locality : ''}}{{!empty($one->deliveryAddress->streetAddress) ? ', '.$one->deliveryAddress->streetAddress : ''}}
						</div>
					</div>
				</div>
				@if(!empty($one->deliveryDate->endDate) || !empty($one->deliveryDate->startDate))
				<div class="col-md-6">
					<div class="tender-description__item">
						<div class="tender-description__title">{{ t('tender.delivery_date') }}:</div>
						<div class="tender-description__text">
							@if(!empty($one->deliveryDate->startDate)) {{ t('tender.from') }} {{date('d.m.Y H:i', strtotime($one->deliveryDate->startDate))}}@endif
							@if(!empty($one->deliveryDate->endDate)) {{ t('tender.before') }} {{date('d.m.Y H:i', strtotime($one->deliveryDate->endDate))}}@endif
						</div>
					</div>
				</div>
				@elseif(!empty($one->deliveryDate))
				<div class="col-md-6">
					<div class="tender-description__item">
						<div class="tender-description__title">{{ t('tender.delivery_date') }}:</div>
						<div class="tender-description__text">{{date('d.m.Y H:i', strtotime($one->deliveryDate))}}</div>
					</div>
				</div>
				@endif
			</div>
		@endforeach
	</div>
@endif