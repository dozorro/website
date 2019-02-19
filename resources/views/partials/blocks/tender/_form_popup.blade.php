<div class="add-review-form" id="review_form">
    <div style="width:100%;height:100%;position:absolute; z-index:1; top:0px; left:0px;display:none; background-color: rgba(255,255,255,.7)" loader></div>
    <div class="container" style="position:relative">
        <div class="add-review-form__content">
            @if ($user)
                <button class="add-review-form__close-button review_form_close"></button>
                <h1 class="tender-header__h1" form-title></h1>
                <div class="form-selector" form-selector>
                    <div class="form-selector-button">
                        <a href="" class="form-selector-button__link outline"
                           data-formjs="jsonForm"
                           data-form="F101"
                           data-model="form"
                           data-form-title="{{t('tender.your_review')}}"
                           data-submit-button="{{t('tender.your_review.next')}}"
                           data-thanks=".thanks"
                           data-next="F102-105"
                           data-validate="formF101"
                           data-generate="formF101">
                            {{t('tender.F102-105.title')}}
                        </a>
                    </div>
                    <div class="form-selector-button">
                        <a href="" class="form-selector-button__link outline"
                           data-formjs="jsonForm"
                           data-form="F110"
                           data-model="form"
                           data-validate="formF110"
                           data-form-title="{{t('tender.your_review')}}"
                           data-submit-button="{{t('tender.post_comment')}}">
                            {{t('tender.f110.title')}}
                        </a>
                    </div>
                    <div class="form-selector-button">
                        <a href="" class="form-selector-button__link outline"
                           data-formjs="jsonForm"
                           data-form="F111"
                           data-model="form"
                           data-form-title="{{t('tender.your_review')}}"
                           data-submit-button="{{t('tender.post_comment')}}">
                            {{t('tender.evaluation_training')}}
                        </a>
                    </div>
                    <div class="form-selector-button">
                        <a href="" class="form-selector-button__link outline"
                           data-formjs="jsonForm"
                           data-form="F112"
                           data-model="form"
                           data-form-title="{{t('tender.your_review')}}"
                           data-submit-button="{{t('tender.post_comment')}}">
                            {{t('tender.assessment_customer_contract_terms')}}
                        </a>
                    </div>
                    <div class="form-selector-button hidden" F102-105>
                        <a href="" class="form-selector-button__link"
                           data-formjs="jsonForm"
                           data-model="form"
                           data-validates="formF102"
                           data-form="F102+F103+F104+F105+F106+F107+F108+F109"
                           data-submit-button="{{t('tender.post_comment')}}"
                           data-form-title="{{t('tender.please_provide_details_your_assessment')}}">
                        </a>
                    </div>
                </div>

                <div form-container data-tender-id="{{ @$item->id }}" data-tender-public-id="{{ @$item->tenderID }}"></div>
                <br>
                {{--
                <div class="tender-form-button">
                    <a href="" class="tender-header__link">Виконання договору з переможцем</a>
                </div>
                --}}


                {{--
                <form id="form-f101" class="" data-js="F101" data-is-main="true" action="/jsonforms/form101/" data-id="{{ $item->id }}" data-public-id="{{ $item->tenderID }}">
                </form>
                <form id="form-f102" class="form-review" data-js="F102" data-is-main="false" action="/jsonforms/form102/" data-id="{{ $item->id }}" data-public-id="{{ $item->tenderID }}">
                    <input type="submit" id="submit-f102" class="hidden" value="Залишити відгук">
                </form>
                <form id="form-f103" class="form-review" data-js="F103" data-is-main="false" action="/jsonforms/form103/" data-id="{{ $item->id }}" data-public-id="{{ $item->tenderID }}">
                    <input type="submit" id="submit-f103" class="hidden"  value="Залишити відгук">
                </form>
                <input type="submit" id="submit-f101" form="form-f101" value="Залишити відгук">
                --}}
                <div class="thanks" hidden>
                {{t('tender.thanks.hidden')}}
                <!--Дякуємо! Ваш відгук зараховано.-->
                </div>
                <div class="success" form-success>
                    <!--Дякуємо за відгук-->
                    {{t('tender.thanks.success')}}
                </div>
                <div class="error" form-error>
                {{t('tender.thanks.error')}}
                <!--Під час відправки форми сталася помилка-->
                </div>
            @else
                <div>
                    <a class="btn btn-block btn-social btn-facebook" href="/auth/facebook">
                        <span class="fa fa-facebook"></span> {{t('tender.login.facebook')}}
                    </a>
                    <a class="btn btn-block btn-social btn-google" href="/auth/google">
                        <span class="fa fa-google"></span> {{t('tender.login.google')}}
                    </a>
                    {{--
                    <a class="btn btn-block btn-social btn-twitter" href="/auth/twitter">
                        <span class="fa fa-twitter"></span> Увійти через Twitter
                    </a>
                    --}}
                </div>
            @endif
        </div>
    </div>
</div>