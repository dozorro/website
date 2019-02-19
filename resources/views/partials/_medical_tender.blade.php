@if($item)
    <tr>
        <td>
            @if(!empty($item->tender))
                <a href="{{ route('page.tender_by_id', ['id'=>$item->tender->tender_id]) }}">{{ $item->tender->tender_id }}</a>
            @else
                -
            @endif
        </td>
        <td>
            @if(!empty($item->tender))
                {{ $item->tender->entity_id }}
            @else
                -
            @endif
        </td>
        <td>
            {{ $item->name }}
        </td>
        <td>
            {{ $item->form }}
        </td>
        <td>
            {{ $item->quantity }}
        </td>
        <td>
            {{ $item->editPrice() }}
        </td>
    </tr>
@endif
