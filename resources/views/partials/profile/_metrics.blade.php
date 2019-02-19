<div class="row list_graph block_table">
    <div class="title-container">
        <h3>{{ t('profile.template.metrics') }}</h3>
    </div>

    <div class="overflow-table">
        @if(!empty($groups))
        <table>
            <thead>
            <tr>
                <th>
                    {{ t('profile.template.metrics.type') }}
                </th>
                <th style="text-align: right;">
                    {{ t('profile.template.metrics.value') }}
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($groups as $mtype => $metrics)
                <tr>
                    <td colspan="2">
                        <strong>{{ t($mtype) }}</strong>
                    </td>
                </tr>
                @foreach($metrics as $metric)
                <tr>
                    <td>
                        {{ t($metric->label) }}
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($metric->metric_value, $metric->display_decimals, '.', ' ') }}
                        {{ $metric->suffix ? ' '.$metric->suffix : '' }}
                    </td>
                </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
        @else
            <p style="text-align: center;">{{ t('profile.metrics.empty') }}</p>
        @endif
    </div>
    <div class="clearfix"></div>
</div>