<div class="c-find-form">
    <div class="container">
        <form action="/" method="POST">
            <div class="row">
                <div class="col-md-3">
                    <h2>{{t('reviews.find_reviews')}}</h2>
                </div>
                <div class="col-md-6">
                    <div class="c-find-form__inputs-wrap">
                        <div class="c-find-form__input-group">
                            <input type="text" id="tenderNumber" class="jsGetInputVal">
                            <label for="tenderNumber">{{t('reviews.number_tender')}}</label>
                        </div>
                        <span class="c-find-form__or-word">{{t('reviews.or')}}</span>
                        <div class="c-find-form__input-group">
                            <input type="text" id="tenderCustomer" class="jsGetInputVal">
                            <label for="tenderCustomer">{{t('reviews.customer')}}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button>{{t('reviews.button_find')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>
