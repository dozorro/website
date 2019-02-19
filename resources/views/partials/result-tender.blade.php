<div class="items-list">
	<div class="container">
		<div class="items-list--item clearfix">
			<div class="row clearfix">
				<div class="col-md-8">
					{{--dump($item)--}}
					@if($user && $user->ngo)
						<div class="checkbox">
							<input class="ngo-checkbox" id="tender-{{$item->tenderID}}" data-f201="{{$item->__is_F201?'1':'0'}}" data-tender-public-id="{{$item->tenderID}}" type="checkbox">
						</div>
					@endif
                    <a style="display: block;" href="{{ route('page.tender_by_id', ['id' => $item->tenderID]) }}" class="items-list--header @if(mb_strlen($item->title) > 140) {{'maxheight'}} @endif"><i class="sprite-{{$item->__icon}}-icon"></i>
						<span class="cell">{{!empty($item->title) ? ($item->title) : trans('facebook.tender_no_name')}}</span>
					</a>
					@if(mb_strlen($item->title) > 140)
						<div class="link-more js-more">
							<span class="show_more">{{ t('tender.show_all_text')}}</span>
							<span class="hide_more">{{ t('tender.hide_all_text')}}</span>
						</div>
					@endif
					<div class="clearfix"></div>
					<ol class="breadcrumb">
						<li>{{$item->__icon=='pen' ? t('tender.pen') : t('tender.online')}}</li>
						<li class="marked">{{!empty($dataStatus[$item->status]) ? $dataStatus[$item->status] : t('tender.nostatus')}}</li>
						@if (!empty($item->procuringEntity->address->locality))
							<li>{{$item->procuringEntity->address->locality}}</li>
						@endif
					</ol>
					@if (!empty($item->description))
						<div class="description-wr{{mb_strlen($item->description)>350?' croped':' open'}}">
							@if ($item->description)
								<div class="description"><p>{{$item->description}}</p></div>
							@endif
							@if (mb_strlen($item->description)>350)
								<a class="search-form--open" href="">
									<i class="sprite-arrow-right"></i>
									<span>{{t('interface.expand')}}</span>
									<span>{{t('interface.collapse')}}</span>
								</a>
							@endif
						</div>
					@endif
					@if (!empty($item->procuringEntity->name))
						<div class="items-list-item-description">
							<strong>{{t('interface.company')}}:</strong> {{$item->procuringEntity->name}}
						</div>
					@endif
					<div class="items-list--tem-id" style="display: inline-block;"><strong>ID:</strong> {{$item->tenderID}}</div>
					@if(!empty($profileAccess) || !empty($user->is_profile_links))
					<div class="items-list--tem-id" style="display: inline-block;margin-left:20px;"><a target="_blank" class="profile-role1" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole1TplId,'role'=>'role1']) }}">{{ t('dozorro_profile') }}</a></div>
					@endif
				</div>
				<div class="col-md-4 relative">
					{{--
					<a href="" title="Add to favorite" class="favorite"><i class="sprite-star">Favorite icon</i></a>
					<a href="" title="Delete" class="price-delete"><i class="sprite-close-blue">Delete</i></a>
					--}}
					@if(!empty($item->value))
						<div class="items-list--item--price">
							<span class="price-description">{{t('tender.wait_sum')}}</span>
							{{number_format($item->value->amount, 0, '', ' ')}}
							<span class="uah">{{$item->value->currency}}</span>
						</div>
					@endif
					@if (!empty($item->enquiryPeriod->startDate))
						<div class="items-list--item--date"><strong>{{t('tender.start_date')}}:</strong> {{date('d.m.Y', strtotime($item->enquiryPeriod->startDate))}}</div>
					@endif
				</div>
			</div>
			{{--
			<div class="breadcrumb_custom flat">
				<a href="#" class="disable"><strong>Створена:</strong> ?27.07 (сб)</a>
				<a href="#" class="active"><strong>Уточнення:</strong> ?до 29.06 (пн)</a>
				<a href="#"><strong>Пропозиції:</strong> ?до 6.07 (пт)</a>
				<a href="#"><strong>Аукціон:</strong> ?12.07 (пт)</a>
				<a href="#"><strong>Кваліфікаця:</strong> ?з 15.07 (пн)</a>
			</div>
			--}}
		</div>
	</div>
</div>
