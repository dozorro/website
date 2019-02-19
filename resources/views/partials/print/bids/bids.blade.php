<?php
    usort($item->__bids, function ($a, $b)
    {
        $datea = new \DateTime($a->date);
        $dateb = new \DateTime($b->date);

        return $datea>$dateb;
    });
?>
@foreach($lot->__bids as $bid)
    <tr valign="top">
        <td>
            @if(!empty($bid->tenderers[0]->identifier->legalName))
                {{$bid->tenderers[0]->identifier->legalName}}
            @else
                {{$bid->tenderers[0]->identifier->name}}
            @endif
        </td>
        <td>
            {{$bid->tenderers[0]->identifier->id}}
        </td>
        <td>
            @if (!empty($lot->__initial_bids_dates[$bid->id]))
                {{date('d.m.Y H:i', strtotime($lot->__initial_bids_dates[$bid->id]))}}<br>
            @else
                {{date('d.m.Y H:i', strtotime($bid->date))}}
            @endif
        </td>
    </tr>
@endforeach