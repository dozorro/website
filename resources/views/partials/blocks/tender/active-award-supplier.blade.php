@if(!empty($item->__active_award->suppliers[0]->identifier->legalName))
    @if(!empty($item->__active_award->suppliers[0]->identifier->id) && $item->procurementMethod == 'open' && in_array($item->status, ['active.awarded', 'complete']))
        <a href="https://opendatabot.com/c/{{$item->__active_award->suppliers[0]->identifier->id}}" target="_blank">{{$item->__active_award->suppliers[0]->identifier->legalName}}</a><br>
    @elseif(!empty($item->__active_award->suppliers[0]->identifier->id) && $item->procurementMethod == 'limited' && in_array($item->status, ['active', 'complete']))
        <a href="https://opendatabot.com/c/{{$item->__active_award->suppliers[0]->identifier->id}}" target="_blank">{{$item->__active_award->suppliers[0]->identifier->legalName}}</a><br>
    @else
        {{$item->__active_award->suppliers[0]->identifier->legalName}}<br>
    @endif
@elseif(!empty($item->__active_award->suppliers[0]->name))
    @if(!empty($item->__active_award->suppliers[0]->identifier->id) && $item->procurementMethod == 'open' && in_array($item->status, ['active.awarded', 'complete']))
        <a href="https://opendatabot.com/c/{{$item->__active_award->suppliers[0]->identifier->id}}" target="_blank">{{$item->__active_award->suppliers[0]->name}}</a><br>
    @elseif(!empty($item->__active_award->suppliers[0]->identifier->id) && $item->procurementMethod == 'limited' && in_array($item->status, ['active', 'complete']))
        <a href="https://opendatabot.com/c/{{$item->__active_award->suppliers[0]->identifier->id}}" target="_blank">{{$item->__active_award->suppliers[0]->name}}</a><br>
    @else
        {{$item->__active_award->suppliers[0]->name}}<br>
    @endif
@endif
@if(!empty($profileAccess) || !empty($user->is_profile_links))
    <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$item->__active_award->suppliers[0]->identifier->scheme.'-'.$item->__active_award->suppliers[0]->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
@endif