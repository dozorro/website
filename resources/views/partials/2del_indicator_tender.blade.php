@if($item)
    <tr data-id="{{ $item->id }}" data-tender-id="{{ $item->tenderID }}">
        <td>
            <a href="#">{{ $item->customer }}</a>
        </td>
        <td>
            {{ $item->item }}
        </td>
        <td>
            {{ $item->price }}
        </td>
        <td>
            @if($item->ngo)
                {{ t('indicator.ngo.yes') }}
            @else
                {{ t('indicator.ngo.no') }}
            @endif
        </td>
        <td>
            {{ $item->rating }}
        </td>
    </tr>
@endif
