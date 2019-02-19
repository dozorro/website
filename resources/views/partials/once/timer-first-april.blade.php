@if(!Cookie::get('popup'))
    <div class="overlay overlay-counter open" data-js="openpopup">
        <div class="overlay-close overlay-close-layout"></div>
        <div class="overlay-box">
            <div class="counter-inner">
                <h4 class="overlay-title">{{t('time_block.before_start_law')}}<br/>"{{t('time_block.on_public_procurement')}}"<br/>{{t('time_block.remained')}}</h4>
                <div class="counter-box clearfix">
                    <?php
                        $now = new DateTime();
                        $day = new DateTime('2016-04-01 00:00:00');
                        $interval = $now->diff($day);                    
    
                        $n=$interval->format('%a');
                        $type=($n%10==1 && $n%100!=11 ? 0 : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2));

                        Cookie::queue('popup', '1', ($n+1)*60*24);
                    ?>
                    @foreach(str_split($n) as $num)
                        <div class="counter-number">{{$num}}</div>
                    @endforeach
                </div>
                <div class="counter-days">
                    {{t('interface.days.'.$type)}}
                </div>
                <a href="{{href('news/shhodo-zakonu-ukrayiny-pro-publichni-zakupivli')}}" class="counter-more">{{t('longreads.more')}}</a>
            </div>
            <div class="overlay-close"></div>
        </div>
    </div>
@endif