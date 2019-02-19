@if(!empty($item->__contracts_changes))
    <div class="container wide-table">
        <div class="margin-bottom-xl block_faq">
		    <h4>{{t('tender.changes_contract')}}</h4>

	        @foreach($item->__contracts_changes as $document)
				<div class="item">
					<h5>{!! implode('<br>', $document->rationaleTypes) . ' ' . date('d.m.Y H:i', strtotime($document->date)) !!}</h5>
					<div class="faq_text">
	            		<div class="row">
	                <table class="tender--customer tender--customer--table margin-bottom-xl">
	                    <tbody>
	                       <!-- <tr>
	                            <td class="col-sm-8"><strong>{{t('tender.date_amending_agreement')}}:</strong></td>
	                            <td class="col-sm-4"><strong>{{date('d.m.Y H:i', strtotime($document->date))}}</strong></td>
	                        </tr>
	                        <tr>
	                            <td class="col-sm-8">{{t('tender.cases_for_changes_essential_terms_contract')}}:</td>
	                            <td class="col-sm-4">{!!implode('<br>', $document->rationaleTypes)!!}</td>
	                        </tr> -->
	                        <tr>
	                            <td class="col-sm-8">{{t('tender.description_changes_made_essential_terms_contract')}}:</td>
	                            <td class="col-sm-4">{{$document->rationale}}</td>
	                        </tr>
	                        <tr>
	                            <td class="col-sm-8">{{t('tender.number_purchase_agreement')}}:</td>
	                            <td class="col-sm-4">{{!empty($document->contractNumber) ? $document->contractNumber : t('tender.not_specified')}}</td>
	                        </tr>
	                        <tr>
	                            <td class="col-sm-8">{{t('tender.contract')}}:</td>
	                            <td class="col-sm-4">
    	                                @if(!empty($document->contract))
    	                                    @foreach($document->contract as $contract)
            	                                <div><a href="{{$contract->url}}" target="_blank">{{$contract->title}}</a></div>
        	                                @endforeach
    	                                @else
										{{t('tender.not_specified')}}
    	                                @endif
    	                           </td>
	                        </tr>
	                    </tbody>
	                </table>
	            </div>
					</div>
				</div>
			@endforeach
        </div>
    </div>
@endif