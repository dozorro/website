<tr>
    @if (isset($item->planID))
        <td>
            <a href="/plan/{{ $item->planID }}">
                @if(!empty($item->procuringEntity->identifier->legalName))
                    {{$item->procuringEntity->identifier->legalName}}<br>
                @elseif(!empty($item->procuringEntity->name))
                    {{$item->procuringEntity->name}}<br>
                @endif
            </a>
            #{{$item->procuringEntity->identifier->id}}
        </td>
    @else
        <td></td>
    @endif

    <td>
        @if(!empty($item->classification))
            <div>{{$item->classification->id}}: {{$item->classification->description}}</div><br>
        @endif
        @if(!empty($item->additionalClassifications))
            @foreach ($item->additionalClassifications as $additionalClassification)
                <div>{{$additionalClassification->id}}: {{$additionalClassification->description}}</div>
            @endforeach
        @endif        
    </td>

    @if (isset($item->budget))
        <td>
            {{$item->budget->description}}
        </td>
        <td>
            {{number_format($item->budget->amount, 0, '', ' ')}} <span class="small val">{{$item->budget->currency}}</span>
        </td>
    @else
        <td></td>
        <td></td>
    @endif
    <td>
        @if (!empty($item->tender->tenderPeriod))
            @if ($item->__is_first_month)
                {{$item->__is_first_month}}
            @else
                {{date('d.m.Y', strtotime($item->tender->tenderPeriod->startDate))}}
            @endif
        @endif
    </td>
</tr>
