@if(!empty($item->__contract_ongoing) && $item->__contract_ongoing->status=='terminated')
    <div class="container wide-table">
        <div class="row margin-bottom-xl">
		    <h3>{{t('tender.implementation_agreement')}}</h3>

            <div class="row">
                <table class="tender--customer tender--customer--table margin-bottom">
                    <tbody>
                        <tr>
                            <td class="col-sm-8"><strong>{{t('tender.term_of_contract')}}:</strong></td>
                            <td class="col-sm-4"><strong>{{!empty($item->__contract_ongoing->period->startDate) ? date('d.m.Y', strtotime($item->__contract_ongoing->period->startDate)) : 'не вказанa'}} — {{date('d.m.Y', strtotime(!empty($item->__contract_ongoing->period->endDate) ? $item->__contract_ongoing->period->endDate : $item->__contract_ongoing->dateModified))}}</strong></td>
                        </tr>
                        <tr>
                            <td class="col-sm-8">{{t('tender.total_payments_under_agreement')}}:</td>

                            <td class="col-sm-4">
                                {{str_replace('.00', '', number_format($item->__contract_ongoing->amountPaid->amount, 2, '.', ' '))}} 
                                <div class="td-small grey-light">{{$item->__contract_ongoing->amountPaid->currency}}{{$item->__contract_ongoing->amountPaid->valueAddedTaxIncluded?t('tender.vat'):''}}</div>
                            </td>
                        </tr>
                        @if(!empty($item->__contract_ongoing->terminationDetails))
                            <tr>
                                <td class="col-sm-8">{{t('tender.reasons_for_termination_of_contract')}}:</td>
                                <td class="col-sm-4">{{$item->__contract_ongoing->terminationDetails}}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>         
        </div>
    </div>
@endif