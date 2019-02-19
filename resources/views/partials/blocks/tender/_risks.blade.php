<div class="container" risk_tender>
    <div class="row">
        <div class="col-md-9 bg_white">

           {{-- <h3>{{ t('indicators.risks_count') }} {{ $item->__risks->risks_count }}/{{ $item->__risks->risks_total }}
                <span class="info">
                    <span class="info_icon"></span>
                    <div class="info_text">
                        <div>
                            <p>
                                {{ t('indicators.risks_count_help') }}
                            </p>
                        </div>
                    </div>
                </span>
            </h3>
            --}}

            <h3>{{ t('indicators.risk_title') }}</h3>

            @foreach($item->__risks->risks as $riskType => $risks)
            <div class="item_risk" style="padding: 0px 25px;">
               <!--<h4>
                    <img src="/assets/images/{{ $risks['type'] }}.png" />
                    {{ t('indicators.risks_type_'.$riskType) }}{{-- - {{ $risks['count'] }}/{{ $risks['total'] }}:--}}
                </h4>
               -->

                <table>
                <!--<tr>
                        <th width="100%">{{ t('indicators.risk_title') }}</th>
                        <th width="20%">{{ t('indicators.risk_date') }}</th>
                    </tr>-->


                    @foreach($risks['data'] as $risk)

                        @if(!empty($risk->groupTitle))
                            <tr width="100%">
                                <td>
                                    <h4 style="padding-top: 20px;margin: 0 auto;padding-bottom: 20px;">{{ $risk->groupTitle }}</h4>
                                </td>
                            </tr>
                        @endif

                    <tr>
                        <td>
                            @if(!empty($risk->lot))
                                <p>
                                    <a href="/tender/{{ $item->tenderID }}?lot_id={{ $risk->lot_id }}#lot">{{ $risk->lot }}</a>
                                </p>
                            @endif
                            <span @if($risk->important == 'true'){{'class=name_risk'}}@endif>{{ $risk->title }} - {{ $risk->risk_value }}</span>
                            @if(!empty($risk->url))
                                <span style="margin-left: 10px;">
                                    <a style="text-decoration: none;" href="{{ $risk->url }}" target="_blank">
                                        <span class="risk-icon profile-role1">{{ t('indicators.risks.externalurl') }}</span>
                                    </a>
                                </span>
                            @endif
                            @if(!empty($risk->desc))
                            <span class="info">
                                <span class="info_icon"></span>
                                <div class="info_text">
                                    <div>
                                        <p style="font-size: 12px;">
                                            {{ $risk->desc }}
                                        </p>
                                    </div>
                                </div>
                            </span>
                            @endif
                        </td>
                        <!--<td>23.03.2018</td>-->
                    </tr>
                    @endforeach

                </table>

            </div>
            @endforeach

        </div>

    </div>

</div>