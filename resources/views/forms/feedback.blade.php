<?php
    /*
    $id='';
    $is_plan=false;
    $is_tender=false;
    $is_centerpage='';

    if(in_array(head(explode('/', Request::path())), ['tender', 'plan']))
        $id=last(explode('/', Request::path()));
        
    if(head(explode('/', Request::path()))=='tender')
        $is_tender=true;

    if(head(explode('/', Request::path()))=='plan')
        $is_plan=true;
    
    if(strpos(Request::path(), '404')!==false)
        $is_centerpage=' center-page-form';

    if(strpos(Request::path(), '500')!==false)
        $is_centerpage=' center-page-form';
    
    $is_thanks=$errors->first('done');
?>
<div class="form-right-fixed{{$is_centerpage}}{{$is_thanks?' form-thanks':''}}">
    <div class="form-container form-fixed{{$is_thanks?'':' none'}}" data-js="feedback_thanks">
        <div class="form-fixed-inner">
            <p>Дякуємо за повідомлення</p>

            <div class="main-menu"><input type="button" value="Надіслати ще" class="btn green-btn registration send-more"></div>
    
            <div class="close sprite-close-blue"></div>
        </div>
    </div>

    <div class="form-container form-fixed{{$errors->isEmpty() || $is_thanks?' none':''}}" data-js="feedback">
        <form method="post" action="/feedback/" class="form-fixed-inner">
            <div class="relative{{$errors->has('email') ? ' error' : ''}}">
                <input type="text" name="email" value="{{ old('email') }}" placeholder="Контактний email" class="required">
                <p class="error-text">{!!$errors->first('email')!!}</p>
            </div>
            <div class="relative{{$errors->has('phone') ? ' error' : ''}}">
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Контактний телефон" class="required">
                <p class="error-text">{!!$errors->first('phone')!!}</p>
            </div>
            
            <div class="relative{{$errors->has('name') ? ' error' : ''}}">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Як до Вас звертатись" class="required">
                <p class="error-text">{!!$errors->first('name')!!}</p>
            </div>
            
            <div class="relative{{$errors->has('subject') ? ' error' : ''}}">
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Тема" class="required">
                @if ($errors->has('subject'))<p class="error-text">{!!$errors->first('subject')!!}</p>@endif
            </div>
    
            <div class="relative{{$errors->has('message') ? ' error' : ''}}">
                <textarea name="message" placeholder="Опис помилки">{{ old('message') }}</textarea>
                @if ($errors->has('message'))<p class="error-text" style="margin-top:20px">{!!$errors->first('message')!!}</p>@endif
            </div>
            
            <div class="relative{{$errors->has('type') ? ' error' : ''}}">
                <select name="type">
                    <option value="">Оберіть тип помилки</option>
                    @foreach(trans('feedback.type') as $type_id=>$type)
                        <option value="{{$type_id}}"{{ (!old('type') && $is_tender && $type_id==1) || (!old('type') && $is_plan && $type_id==2) || (old('type')==$type_id) ? ' selected' : ''}}>{{$type}}</option>
                    @endforeach
                </select>
                <p class="error-text" style="margin-top:-7px">{!!$errors->first('type')!!}</p>
            </div>
    
            <div class="relative{{$errors->has('id') ? ' error' : ''}}">
                <input type="text" name="id" value="{{ old('id') ? old('id') : $id }}" placeholder="Номер документу">
                <p class="error-text">{!!$errors->first('id')!!}</p>
            </div>
            
            <div class="relative{{$errors->has('g-recaptcha-response') || $errors->has('api') ? ' error' : ''}}">
                {!! Recaptcha::render(['lang'=>'uk']) !!}
                @if ($errors->has('g-recaptcha-response'))<p class="error-text">{!!$errors->first('g-recaptcha-response')!!}</p>@endif
                @if ($errors->has('api'))<p class="error-text error-recaptcha">{!!$errors->first('api')!!}</p>@endif
            </div>
            
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="main-menu"><input type="submit" value="Відправити повідомлення" class="btn green-btn registration"></div>
    
            <div class="close sprite-close-blue"></div>
        </form>
    </div>
    
    <div class="form-container main-menu form-button{{$is_thanks?' none':''}}">
        <input type="submit" value="Повідомити про помилку" class="btn btn-fixed-form green-btn registration send-button">
    </div>
</div>
*/