@if (!empty($item->procuringEntity))
    <div class="col-sm-9 tender--customer--inner margin-bottom-more">
        @if (in_array($item->procurementMethodType, ['aboveThresholdEU', 'competitiveDialogueEU', 'aboveThresholdUA.defense']))
            @if (Lang::getLocale() == 'ua')
                <h3>{{t('tender.information_about_customer')}}</h3>
                <div style="margin-top:-15px;margin-bottom:20px">Purchasing Body</div>

                <div class="row">
                    <table class="tender--customer margin-bottom">
                        <tbody>
                        @if(!empty($item->procuringEntity->identifier->legalName))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_name')}}:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->identifier->legalName}}
                                    @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                        <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @elseif (!empty($item->procuringEntity->name))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_name')}}:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->name}}
                                    @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                        <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->identifier->id))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_code')}}:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->identifier->id}}</td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->contactPoint->url))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_website')}}:</strong></td>
                                <td class="col-sm-6"><a href="{{$item->procuringEntity->contactPoint->url}}" target="_blank">{{$item->procuringEntity->contactPoint->url}}</a></td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->address))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_addr')}}:</strong></td>
                                <td class="col-sm-6">{{!empty($item->procuringEntity->address->postalCode) ? $item->procuringEntity->address->postalCode.', ': ''}}{{$item->procuringEntity->address->countryName}}, {{!empty($item->procuringEntity->address->region) ? $item->procuringEntity->address->region.t('tender.region') : ''}}{{!empty($item->procuringEntity->address->locality) ? $item->procuringEntity->address->locality.', ' : ''}}{{!empty($item->procuringEntity->address->streetAddress) ? $item->procuringEntity->address->streetAddress : ''}}</td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->contactPoint))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_contact')}}:</strong></td>
                                <td class="col-sm-6">
                                    @if (!empty($item->procuringEntity->contactPoint->name))
                                        {{$item->procuringEntity->contactPoint->name}}<br>
                                    @endif
                                    @if (!empty($item->procuringEntity->contactPoint->telephone))
                                        {{$item->procuringEntity->contactPoint->telephone}}<br>
                                    @endif
                                    @if (!empty($item->procuringEntity->contactPoint->email))
                                        <a href="mailto:{{$item->procuringEntity->contactPoint->email}}">{{$item->procuringEntity->contactPoint->email}}</a><br>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if(!empty($item->procuringEntity->identifier->legalName_en))
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="col-sm-4"><strong>Official name:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->identifier->legalName_en}}</td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->identifier->id))
                            <tr>
                                <td class="col-sm-4"><strong>National ID:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->identifier->id}}</td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->contactPoint))
                            <tr>
                                <td class="col-sm-4"><strong>Contact point:</strong></td>
                                <td class="col-sm-6">
                                    @if (!empty($item->procuringEntity->contactPoint->name_en))
                                        {{$item->procuringEntity->contactPoint->name_en}}<br>
                                    @endif
                                    @if (!empty($item->procuringEntity->contactPoint->telephone))
                                        {{$item->procuringEntity->contactPoint->telephone}}<br>
                                    @endif
                                    @if (!empty($item->procuringEntity->contactPoint->email))
                                        <a href="mailto:{{$item->procuringEntity->contactPoint->email}}">{{$item->procuringEntity->contactPoint->email}}</a><br>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            @elseif (Lang::getLocale() == 'en')
                <h3>Purchasing Body</h3>
                <h4>{{t('tender.information_about_customer')}}</h4>

                <div class="row">
                    <table class="tender--customer margin-bottom">
                        <tbody>
                        @if(!empty($item->procuringEntity->identifier->legalName_en))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_name')}}:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->identifier->legalName_en}}
                                    @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                        <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @elseif (!empty($item->procuringEntity->name))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_name')}}:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->name}}
                                    @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                        <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->identifier->id))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_code')}}:</strong></td>
                                <td class="col-sm-6">{{$item->procuringEntity->identifier->id}}</td>
                            </tr>
                        @endif
                        @if (!empty($item->procuringEntity->contactPoint))
                            <tr>
                                <td class="col-sm-4"><strong>{{t('tender.customer_contact')}}:</strong></td>
                                <td class="col-sm-6">
                                    @if (!empty($item->procuringEntity->contactPoint->name_en))
                                        {{$item->procuringEntity->contactPoint->name_en}}<br>
                                    @endif
                                    @if (!empty($item->procuringEntity->contactPoint->telephone))
                                        {{$item->procuringEntity->contactPoint->telephone}}<br>
                                    @endif
                                    @if (!empty($item->procuringEntity->contactPoint->email))
                                        <a href="mailto:{{$item->procuringEntity->contactPoint->email}}">{{$item->procuringEntity->contactPoint->email}}</a><br>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <h3>{{t('tender.information_about_customer')}}</h3>

            <div class="row">
                <table class="tender--customer margin-bottom">
                    <tbody>
                    @if(!empty($item->procuringEntity->identifier->legalName))
                        <tr>
                            <td class="col-sm-4"><strong>{{t('tender.customer_name')}}:</strong></td>
                            <td class="col-sm-6">{{$item->procuringEntity->identifier->legalName}}
                                @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                    <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                @endif
                            </td>
                        </tr>
                    @elseif (!empty($item->procuringEntity->name))
                        <tr>
                            <td class="col-sm-4"><strong>{{t('tender.customer_name')}}:</strong></td>
                            <td class="col-sm-6">{{$item->procuringEntity->name}}
                                @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                    <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if (!empty($item->procuringEntity->identifier->id))
                        <tr>
                            <td class="col-sm-4"><strong>{{t('tender.customer_code')}}:</strong></td>
                            <td class="col-sm-6">{{$item->procuringEntity->identifier->id}}</td>
                        </tr>
                    @endif
                    @if (!empty($item->procuringEntity->contactPoint->url))
                        <tr>
                            <td class="col-sm-4"><strong>{{t('tender.customer_website')}}:</strong></td>
                            <td class="col-sm-6"><a href="{{$item->procuringEntity->contactPoint->url}}" target="_blank">{{$item->procuringEntity->contactPoint->url}}</a></td>
                        </tr>
                    @endif
                    @if (!empty($item->procuringEntity->address))
                        <tr>
                            <td class="col-sm-4"><strong>{{t('tender.customer_addr')}}:</strong></td>
                            <td class="col-sm-6">{{!empty($item->procuringEntity->address->postalCode) ? $item->procuringEntity->address->postalCode.', ': ''}}{{$item->procuringEntity->address->countryName}}, {{!empty($item->procuringEntity->address->region) ? $item->procuringEntity->address->region.t('tender.region') : ''}}{{!empty($item->procuringEntity->address->locality) ? $item->procuringEntity->address->locality.', ' : ''}}{{!empty($item->procuringEntity->address->streetAddress) ? $item->procuringEntity->address->streetAddress : ''}}</td>
                        </tr>
                    @endif
                    @if (!empty($item->procuringEntity->contactPoint))
                        <tr>
                            <td class="col-sm-4"><strong>{{t('tender.customer_contact')}}:</strong></td>
                            <td class="col-sm-6">
                                @if (!empty($item->procuringEntity->contactPoint->name))
                                    {{$item->procuringEntity->contactPoint->name}}<br>
                                @endif
                                @if (!empty($item->procuringEntity->contactPoint->telephone))
                                    {{$item->procuringEntity->contactPoint->telephone}}<br>
                                @endif
                                @if (!empty($item->procuringEntity->contactPoint->email))
                                    <a href="mailto:{{$item->procuringEntity->contactPoint->email}}">{{$item->procuringEntity->contactPoint->email}}</a><br>
                                @endif
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif