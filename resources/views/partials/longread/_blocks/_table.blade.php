@if(empty($ajax))
<div class="overflow-table">
@endif
    <table id="{{ $data->code }}">
        <thead>
            <tr>
                @foreach($data->table_fields as $item)
                    @if($item->is_all || $single)
                    <th style="@if(!empty($item->is_formatted)){{'text-align:right;'}}@endif">
                        @if(!$item->is_order)
                            {{ $item->field_title }}
                        @else
                            <a href="#" class="order_down">{{ $item->field_title }}</a>
                        @endif
                    </th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($results as $row)
            <tr>
                @foreach($data->table_fields as $item)
                    @if($item->is_all || $single)
                    <td style="@if($item->is_formatted){{'text-align:right;'}}@endif">
                        @if(isset($row[$item->field_name]))
                            @if(!empty($item->link))
                                <a target="_blank" style="color: #e55166;" href="{{ strtr($item->link, ['{'.$item->link_field.'}' => @$row[$item->link_field]]) }}">
                                    {{ !empty($item->is_formatted) ? number_format($row[$item->field_name], $item->decimal, '.', ' ') : $row[$item->field_name] }}
                                </a>
                            @else
                                {{ !empty($item->is_formatted) ? number_format($row[$item->field_name], $item->decimal, '.', ' ') : $row[$item->field_name] }}
                            @endif
                        @endif
                    </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
@if(empty($ajax))
</div>
@endif