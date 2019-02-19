@foreach($items as $item)
    <tr valign="top">
        <td>{{$item->budget->description}}</td>
        <td>
            @if(!empty($item->additionalClassifications[0]))
                {{trans('tender.dkpp')}}: {{$item->additionalClassifications[0]->id}}
            @endif                
            @if (!empty($item->classification))
                <br>
                {{trans('tender.cpv')}}: {{$item->classification->id}}
            @endif
        </td>
        <td>
            @if($item->__kekv)
                @foreach ($item->__kekv as $kekv)
                    <div>{{$kekv->id}}: {{$kekv->description}}</div>
                @endforeach
            @endif
        </td>
        <td>{{str_replace(',00', '', number_format($item->budget->amount, 2, ',', ' '))}}{{-- {{$item->budget->currency}} <nobr>{{!empty($item->budget->valueAddedTaxIncluded) ? 'з ПДВ' : 'без ПДВ'}}</nobr>--}}</td>
        <td>{{trans('plan.procedure.'.$item->__procedure)}}</td>
        <td>
            @if ($item->__is_first_month)
                {{$item->__is_first_month}}
            @else
                {{date('d.m.Y', strtotime($item->tender->tenderPeriod->startDate))}}
            @endif
        </td>
        <td>
            {!!!empty($item->budget->notes) ? '<p>'.nl2br($item->budget->notes).'</p>' : ''!!}
            <p>Номер плану: {{$item->planID}}</p>
            <p>Опубліковано: {{date('d.m.Y', $item->__planDate)}}</p>
            @if($item->__dateModified)
                <p>Змінено: {{date('d.m.Y'), strtotime($item->dateModified)}}</p>
            @endif
        </td>
    </tr>            
@endforeach