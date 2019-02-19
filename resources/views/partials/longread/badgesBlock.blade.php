@if(!empty($block->data->badges))
    <div class="c-b">
        <div class="container ">
            <div class="bg_white page_badge">
                <h3>{{$block->data->title}}</h3>
                <div class="block_text">
                    <p>
                        {{$block->data->desc}}
                    </p>
                </div>

                    <table>
                        <tbody>
                        <tr>
                            <th width="30%">{{t('badges.name')}}</th>
                            <th width="25%">{{t('badges.icons')}}</th>
                            <th width="45%">{{t('badges.desc')}}</th>
                        </tr>
                        @foreach($block->data->badges as $badge)
                            <tr>
                                <td>
                                    <a href="{{ route('page.rating', ['slug' => $badge->slug]) }}">{{ $badge->badgeName }}</a>
                                </td>
                                <td>
                                    @if(isset($badge->badgesList) && !$badge->badgesList->isEmpty())
                                        @foreach($badge->badgesList as $_badge)
                                        <img src="{{ $_badge->logo }}">
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <p>{{ $badge->badgeDesc }}</p>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <ul class="mobile page_list_badge">
                        @foreach($block->data->badges as $badge)
                        <li>
                            <a href="#">{{ $badge->badgeName }}</a>
                            @if(isset($badge->badgesList) && !$badge->badgesList->isEmpty())
                                <div class="inline-layout">
                                @foreach($badge->badgesList as $_badge)
                                    <img src="{{ $_badge->image }}">
                                @endforeach
                                </div>
                                <p>{{ $badge->badgeDesc }}</p>
                            @endif
                        </li>
                        @endforeach
                    </ul>
            </div>
        </div>
    </div>
@endif