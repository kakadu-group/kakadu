<?php

/**
 * Routes
 */

//Install
// Route::get('install', array('as' => 'install', 'uses' => 'install@index'));
// Route::post('install', array('uses' => 'install@install'));
// Route::get('install/finished', array('as' => 'install/finished', 'uses' => 'install@finished'));
// Route::any('(.*)', function() {
//     return Redirect::to_route('install');
// });
// return;


//Home
Route::get('/', array('as' => 'home', 'uses' => 'home@index'));
Route::get('help', array('as' => 'help', 'uses' => 'home@help'));


//Authentification
Route::get('auth/login', array('as' => 'auth/login', 'uses' => 'authentification@login'));
Route::post('auth/login', array('uses' => 'authentification@login'));
Route::get('auth/logout', array('as' => 'auth/logout', 'uses' => 'authentification@logout'));
Route::get('auth/register', array('as' => 'auth/register', 'uses' => 'authentification@register'));
Route::post('auth/register', array('uses' => 'authentification@register'));
Route::get('auth/activate', array('as' => 'auth/activate', 'uses' => 'authentification@activate'));
Route::get('auth/activate/(:any)/(:any)', array('uses' => 'authentification@activate'));
Route::get('auth/confirmemail', array('as' => 'auth/confirmemail', 'uses' => 'authentification@confirmemail'));
Route::get('auth/forgotpassword', array('as' => 'auth/forgotpassword', 'uses' => 'authentification@forgotpassword'));
Route::post('auth/forgotpassword', array('uses' => 'authentification@forgotpassword'));


//Language
Route::post('language/edit', array('uses' => 'language@edit'));


//Profile
Route::filter('pattern: profile*', 'auth');
Route::get('profile/edit', array('as' => 'profile/edit', 'uses' => 'profile@edit'));
Route::post('profile/edit', array('uses' => 'profile@edit'));
Route::post('profile/changepassword', array('uses' => 'profile@changepassword'));
Route::get('profile/delete', array('as' => 'profile/delete', 'uses' => 'profile@delete'));
Route::delete('profile/delete', array('uses' => 'profile@delete'));


//User
Route::post('users/search', array('uses' => 'search@user'));

//Group
Route::get('groups', array('as' => 'groups', 'uses' => 'group@groups'));
Route::post('groups/search', array('uses' => 'search@group'));

Route::get('group/(:num)', array('as' => 'group', 'uses' => 'group@group'));
Route::get('group/create', array('as' => 'group/create', 'uses' => 'group@create'));
Route::post('group/create', array('uses' => 'group@create'));
Route::get('group/(:num)/edit', array('as' => 'group/edit', 'uses' => 'group@edit'));
Route::post('group/edit', array('uses' => 'group@edit'));
Route::get('group/(:num)/delete', array('as' => 'group/delete', 'uses' => 'group@delete'));
Route::get('group/deleted', array('as' => 'group/deleted', 'uses' => 'group@deleted'));

Route::post('group/user/add', array('uses' => 'group_member@user_add'));
Route::post('group/user/remove', array('uses' => 'group_member@user_remove'));
Route::post('group/admin/add', array('uses' => 'group_member@admin_add'));
Route::post('group/admin/remove', array('uses' => 'group_member@admin_remove'));


//Course
Route::get('courses', array('as' => 'courses', 'uses' => 'course@courses'));
Route::get('courses/search', array('uses' => 'course@search', 'before' => 'csrf'));

Route::get('course/(:num)', array('as' => 'course', 'uses' => 'course@course'));
Route::get('course/create', array('as' => 'course/create', 'uses' => 'course@create'));
Route::post('course/create', array('uses' => 'course@create'));
Route::get('course/(:num)/edit', array('as' => 'course/edit', 'uses' => 'course@edit'));
Route::post('course/edit', array('uses' => 'course@edit'));
Route::get('course/(:num)/delete', array('as' => 'course/delete', 'uses' => 'course@delete'));
Route::get('course/deleted', array('as' => 'course/deleted', 'uses' => 'course@deleted'));


//Course - Create element
Route::get('course/(:num)/catalog/create', array('as' => 'catalog/create', 'uses' => 'catalog@create'));
Route::get('course/(:num)/question/create', array('as' => 'question/create', 'uses' => 'question@create'));


//Course - Import
Route::get('course/(:num)/import', array('as' => 'course/import', 'uses' => 'import@course'));
Route::get('import/check', array('as' => 'import/check', 'uses' => 'import@check'));
Route::post('import/check', array('uses' => 'import@check'));
Route::post('import/save', array('uses' => 'import@save'));


//Catalog
Route::get('catalog/(:num)', array('as' => 'catalog', 'uses' => 'catalog@catalog'));
Route::post('catalog/create', array('uses' => 'catalog@create'));
Route::get('catalog/(:num)/edit', array('as' => 'catalog/edit', 'uses' => 'catalog@edit'));
Route::post('catalog/edit', array('uses' => 'catalog@edit'));
Route::get('catalog/(:num)/delete', array('as' => 'catalog/delete', 'uses' => 'catalog@delete'));
Route::get('catalog/deleted', array('as' => 'catalog/deleted', 'uses' => 'catalog@deleted'));


//Question
Route::get('question/(:num)', array('as' => 'question', 'uses' => 'question@question'));
Route::post('question/create', array('uses' => 'question@create'));
Route::get('question/(:num)/edit', array('as' => 'question/edit', 'uses' => 'question@edit'));
Route::post('question/edit', array('uses' => 'question@edit'));
Route::get('question/(:num)/delete', array('as' => 'question/delete', 'uses' => 'question@delete'));
Route::get('question/deleted', array('as' => 'question/deleted', 'uses' => 'question@deleted'));


//Favorites
Route::get('profile/favorites', array('as' => 'favorites', 'uses' => 'favorite@favorites'));
Route::post('favorites/add', array('uses' => 'favorite@add'));
Route::post('favorites/remove', array('uses' => 'favorite@remove'));


//Learning
Route::get('course/(:num)/learning', array('as' => 'course/learning', 'uses' => 'learning@course'));
Route::get('catalog/(:num)/learning', array('as' => 'catalog/learning', 'uses' => 'learning@catalog'));
Route::get('favorites/learning', array('as' => 'favorites/learning', 'uses' => 'learning@favorites'));
Route::post('learning/next', array('uses' => 'learning@next'));



/**
 * Events
 */
Event::listen('404', function()
{
    return Response::error('404');
});

Event::listen('500', function()
{
    return Response::error('500');
});

Event::listen('laravel.query', function($sql)
{
    //var_dump($sql);
});



/**
 * Filters
 */
Route::filter('before', function()
{

    //Set the language
    $language = 'en';

    if(Cookie::has('language')) {
        $tmp = Cookie::get('language');
        $accepted = Config::get('application.languages_accepted');

        foreach($accepted as $key => $value) {
            if($tmp === $key) {
                $language = $tmp;
                break;
            }
        }
    }

    Config::set('application.language', $language);

});

Route::filter('after', function($response)
{
    // Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
    if (Request::forged()) {
        return Response::error('500');
    }
});

Route::filter('auth', function()
{
    if (Sentry::check() === FALSE) {
        return Redirect::to('auth/login');
    }
});