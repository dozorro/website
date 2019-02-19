<?php

if (env('FORCE_HTTPS', false)) {
    URL::forceSchema('https');
}

//Get locales from database
$locales = \App\Classes\Lang::getLocales();

foreach($locales as $language)
{
    if(!$language->is_current) { continue; }
    $prefix=($language->is_default ? '' : $language->code.'/');

    Route::group(['prefix' => $prefix], function()
    {
        $pages = \App\Page::where('is_disabled', false)->get();

        foreach ($pages as $page) {
            if(!strpos($page->url, 'tools') && !strpos($page->url, 'reports')) {
                Route::get($page->url, [
                    'as' => $page->url == '/' ? 'homepage' : 'inside',
                    'uses' => 'PageController@page'
                ]);
            }
        }

        Route::get('/search', [
            'as' => 'page.search',
            'uses' => 'SearchController@index'
        ]);
        Route::post('/search/tenders', [
            'as' => 'search.tenders',
            'uses' => 'SearchController@search'
        ]);

        Route::post('/feedback/save', [
            'as' => 'feedback.save',
            'uses' => 'FeedbackController@save'
        ]);
        Route::match(['get', 'post'], '/ngo/reviews/{formType?}', [
            'as' => 'ngo.reviews',
            'uses' => 'NgoReviewsController@index'
        ]);
        Route::post('/ngo/reviews/submit', [
            'as' => 'ngo.reviews.submit',
            'uses' => 'NgoReviewsController@submit'
        ]);
        Route::post('/ngo/review/save', [
            'as' => 'ngo.review.save',
            'uses' => 'NgoReviewsController@save'
        ]);

        Route::get('/ratings', [
            'as' => 'page.ratings',
            'uses' => 'PageController@page'
        ]);
        Route::get('/ratings/{slug}/{ngo?}', [
            'as' => 'page.rating',
            'uses' => 'BadgeController@badge'
        ]);
        Route::get('/tools', [
            'as' => 'page.tools',
            'uses' => 'PageController@page'
        ]);
        Route::group(['prefix' => 'profile'], function() {
            Route::get('/', [
                'as' => 'page.profile',
                'uses' => 'MvpController@profileExample'
            ]);
            Route::post('/saveTemplate', [
                'as' => 'template_save',
                'uses' => 'MvpController@saveTemplate'
            ]);
            Route::post('/saveCustomTemplate', [
                'as' => 'template_save_custom',
                'uses' => 'MvpController@saveCustomTemplate'
            ]);
            Route::any('/{scheme}/table/{code}/{setting_id}/{export?}', [
                'as' => 'page.profile.table',
                'uses' => 'MvpController@profileTable'
            ]);
            Route::match(['get', 'post'], '/{scheme}/{tpl?}/{role?}', [
                'as' => 'page.profile_by_id',
                'uses' => 'MvpController@profile'
            ]);
        });

        Route::get('/tools/{slug}', [
            'as' => 'page.tool',
            'uses' => 'ToolController@tool'
        ]);
        Route::get('/reports', [
            'as' => 'page.reports',
            'uses' => 'PageController@page'
        ]);
        Route::get('/reports/{slug}', [
            'as' => 'page.report',
            'uses' => 'ReportController@report'
        ]);

        Route::get('/customers_search', 'SearchCustomerController@search');
        Route::match(['get', 'post'], '/monitoring/{type}/search', [
            'as' => 'page.monitoring_search',
            'uses' => 'MonitoringController@searchClassifier'
        ]);
        Route::match(['get', 'post'], '/monitoring/{type}/search/products', [
            'as' => 'page.monitoring_search_products',
            'uses' => 'MonitoringController@searchProducts'
        ]);
        Route::post('/monitoring_violation/save', [
            'as' => 'monitoring_violation.save',
            'uses' => 'MonitoringController@saveViolation'
        ]);
        Route::get('/medical_contracts', [
            'as' => 'page.medical_contracts',
            'uses' => 'MonitoringController@medical'
        ]);
        Route::post('/medical_contracts/search', [
            'as' => 'page.medical_contracts.search',
            'uses' => 'MonitoringController@search'
        ]);

        //Route::get('search', 'PageController@search_redirect');

        Route::get('{search}/search', [
            'as' => 'search',
            'uses' => 'PageController@search'
        ]);

        Route::get('edrpou', 'EdrpouController@results');

        Route::get('plan/search/print/{print}', 'PrintController@plan_list')->where('print', '(html)');;

        Route::group(['prefix' => 'tender'], function() {
            Route::get('/{id}', [
                'as' => 'page.tender_by_id',
                'uses' => 'PageController@tender'
            ]);
            Route::get('/{id}/form/{form}/{parentForm?}', [
                'as' => 'page.tender_form',
                'uses' => 'PageController@tender_form'
            ]);
            Route::match(['get', 'post'], '/{id}/monitoring/{_type?}', [
                'as' => 'page.monitoring_tender',
                'uses' => 'PageController@monitoring_tender'
            ]);
        });

        Route::get('/form/{form}/{tender_ids}', [
            'as' => 'page.ngo_multy_form',
            'uses' => 'PageController@multy_form'
        ]);

        Route::get('/reviews', [
            'as' => 'page.tenders',
            'uses' => 'PageController@tenders'
        ]);

        Route::match(['get', 'post'], '/monitoring/{slug}/{formType?}', [
            'as' => 'page.monitoring',
            'uses' => 'PageController@monitoring'
        ]);
        Route::get('/ngo/{slug}/stats', [
            'as' => 'page.ngo.stats',
            'uses' => 'NgoReviewsController@stats'
        ]);
        Route::match(['get', 'post'], '/ngo/{slug}/{formType?}', [
            'as' => 'page.ngo',
            'uses' => 'PageController@ngo'
        ]);

        Route::get('plan/{id}', 'PageController@plan');

        Route::post('form/data/{type}', 'FormController@data');
        Route::post('{search}/form/check/{type}', 'FormController@check');
        Route::post('{search}/form/search', 'FormController@search');
        Route::post('form/autocomplete/{type}', 'FormController@autocomplete');

        Route::get('tender/{id}/print/{type}/{print}', 'PrintController@one')->where('print', '(pdf|html)');
        Route::get('tender/{id}/print/{type}/{print}/{lot_id?}', 'PrintController@one')->where('print', '(pdf|html)');

        Route::get('error/404', 'ErrorController@notfound');
        #Route::get('error/500', 'ErrorController@systemerror');

        Route::group(['prefix' => 'community'], function() {
            Route::match(['get', 'post'], '/', [
                'as' => 'page.community',
                'uses' => 'CommunityController@index'
            ]);
            Route::match(['get', 'post'], 'ngo', [
                'as' => 'page.community.ngo',
                'uses' => 'CommunityController@ngo'
            ]);
            Route::match(['get', 'post'], 'customers', [
                'as' => 'page.customers.ngo',
                'uses' => 'CommunityController@customers'
            ]);
            Route::match(['get', 'post'], 'monitoring', [
                'as' => 'page.customers.monitoring',
                'uses' => 'CommunityController@monitoring'
            ]);
        });

        Route::group(['prefix' => 'customers'], function() {
            Route::match(['get', 'post'], '/{edrpou}', [
                'as' => 'page.customers',
                'uses' => 'CustomerController@index'
            ]);
            Route::match(['get', 'post'], '/{edrpou}/{type}', [
                'as' => 'page.customers.tenders',
                'uses' => 'CustomerController@tenders'
            ]);
        });

        Route::group(['prefix' => 'pairs'], function() {

            Route::get('/review', [
                'as' => 'page.pairs.review',
                'uses' => 'PairsController@review'
            ]);
            Route::post('/review/update', [
                'as' => 'page.pairs.review.update',
                'uses' => 'PairsController@reviewUpdate'
            ]);
            Route::post('/ajax', [
                'as' => 'page.pairs.ajax',
                'uses' => 'PairsController@onAjax'
            ]);
            Route::match(['get', 'post'], '/update/{id}', [
                'as' => 'page.pairs.update',
                'uses' => 'PairsController@update'
            ]);
            Route::get('/{id?}/{type?}', [
                'as' => 'page.pairs',
                'uses' => 'PairsController@index'
            ]);
        });

        Route::get('/', [
            'as' => 'page.home',
            'uses'=>'PageController@page'
        ]);

        Route::group(['prefix' => 'news'], function() {
            Route::get('/', [
                'as' => 'page.news',
                'uses' => 'BlogController@news'
            ]);

            Route::get('/{slug}', [
                'as' => 'page.news.post',
                'uses' => 'BlogController@news_page'
            ]);
        });

        Route::group(['prefix' => 'blog'], function() {
            Route::get('/', [
                'as' => 'page.blog',
                'uses' => 'BlogController@blog'
            ]);

            Route::get('/{slug}', [
                'as' => 'page.blog.post',
                'uses' => 'BlogController@blog_page'
            ]);

            Route::get('/tag/{slug}', [
                'as' => 'page.blog.by_tag',
                'uses' => 'BlogController@byTag'
            ]);

            Route::get('/author/{slug}', [
                'as' => 'page.blog.by_author',
                'uses' => 'BlogController@byAuthor'
            ]);

            Route::get('/ngo/{slug}', [
                'as' => 'page.blog.by_ngo',
                'uses' => 'BlogController@byNgo'
            ]);
        });

        Route::get('/ajax/blog', [
            'as' => 'page.blog.by_ajax',
            'uses' => 'BlogController@ajax_blog_longread'
        ]);

        Route::post('/ajax/ngo', [
            'as' => 'ngo.by_ajax',
            'uses' => 'CommunityController@ajaxNgoHeader'
        ]);

        Route::get('/sources/forms/{slug}.json', [
            'as' => 'jsonforms.json',
            'uses' => 'JsonFormController@json'
        ]);

        Route::get('/forms/{slug}/{edrpou?}', [
            'as' => 'jsonforms',
            'uses' => 'JsonFormController@jsonForms'
        ]);

        Route::get('/complaints/{type?}', [
            'as' => 'page.complaints.type',
            'uses' => 'ComplaintsController@item'
        ])->where('type', '(above|below)');

        Route::get('/complaints/{type}/{slug}', [
            'as' => 'page.complaints.item',
            'uses' => 'ComplaintsController@item'
        ])->where('type', '(above|below)');

        Route::get('/amku-practice/{slug}', [
            'as' => 'page.amkupractice.item',
            'uses' => 'AmkuPracticesController@item'
        ]);
        Route::get('/judge-practice/{slug}', [
            'as' => 'page.judgepractice.item',
            'uses' => 'JudgePracticesController@item'
        ]);
        Route::get('/indicators/{id?}', [
            'as' => 'page.indicators',
            'uses' => 'IndicatorsController@index'
        ]);
        Route::post('/indicators/search', [
            'as' => 'page.indicators.search',
            'uses' => 'IndicatorsController@search'
        ]);        
    });
}

Route::post('/api/risk_comment', 'ApiController@riskComment');
Route::post('/api/tenders_sidebar', 'ApiController@renderTendersSideBar');
Route::post('/api/user_type', 'ApiController@saveUserType');
Route::post('/api/user_type/defer', 'ApiController@deferUserType');
Route::get('/api/tender', 'ApiController@tender_index');
Route::get('/api/tender/{id}', 'ApiController@tender');
Route::get('/api/forms', 'ApiController@forms');

Route::post('/api/sidebar/{block}', 'ApiController@tendersSidebarBlock');

Route::get('auth/{provider}', 'AuthController@redirectToProvider');
Route::get('auth/{provider}/callback', 'AuthController@handleProviderCallback');

Route::post('feedback', 'FeedbackController@store');

Route::get('json/platforms/{type}', 'JsonController@platforms');
Route::get('json/announced', 'JsonController@announced_tenders');

Route::post('jsonforms/{model}', 'JsonFormController@submit')->where('model', '(form|comment)');

Route::get('/logout', ['as' => 'logout', function(\Illuminate\Http\Request $request){
    session()->forget('user');
    return Redirect::back()->withCookie(\Cookie::forget('user', '/', $request->getHost()));
}]);

Route::post('/helpers/upload', 'HelpersController@upload');
Route::get('/helpers/download', 'HelpersController@download');

if(config('services.localstorage.url')) {
    Route::get(config('services.localstorage.url') . '/{filename}', 'HelpersController@newDownload');
}
