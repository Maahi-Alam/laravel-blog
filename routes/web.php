<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/hide-sidebar', 'Admin\SettingController@hideSidebar')->name('hide-sidebar');

Route::get('/', 'Frontend\HomeController@index')->name('frontend.home');

// subscriber route
Route::post('subscribers', 'Frontend\SubscriberController@store')->name('subscribers.store');






Auth::routes();

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']],
    function (){

        Route::get('dashboard', 'DashboardController@index')->name('dashboard');

        //Setting route
        Route::get('settings', 'SettingController@index')->name('settings.update');
        Route::post('settings', 'SettingController@profileUpdate')->name('settings.profile.update');
        Route::post('settings/change-password', 'SettingController@changePassword')->name('settings.password.change');

        //Tag route
        Route::get('tags/change-status/{tag}', 'TagController@changeStatus')->name('tags.change.status');
        Route::resource('tags', 'TagController');

        //Category route
        Route::get('categories/change-status/{category}', 'CategoryController@changeStatus')->name('categories.change.status');
        Route::resource('categories', 'CategoryController');

        //Post route
        Route::get('posts/change-status/{post}', 'PostController@changeStatus')->name('posts.change.status');
        Route::get('posts/change-approve/{post}', 'PostController@changeApproveStatus')->name('posts.change.approve-status');
        Route::get('posts/pending', 'PostController@pendingList')->name('posts.pending');
        Route::resource('posts', 'PostController');

        //Subscriber route
        Route::get('subscribers', 'ManageSubscriberController@index')->name('subscribers.index');
        Route::delete('subscribers/{subscriber}', 'ManageSubscriberController@destroy')->name('subscribers.destroy');
    }
);

Route::group(['as' => 'author.', 'prefix' => 'author', 'namespace' => 'Author', 'middleware' => ['auth', 'author']],
    function (){

        Route::get('dashboard', 'DashboardController@index')->name('dashboard');

        //Post route
        Route::get('posts/change-status/{post}', 'PostController@changeStatus')->name('posts.change.status');
        Route::resource('posts', 'PostController');

        //Setting route
        Route::get('settings', 'SettingController@index')->name('settings.update');
        Route::post('settings', 'SettingController@profileUpdate')->name('settings.profile.update');
        Route::post('settings/change-password', 'SettingController@changePassword')->name('settings.password.change');

    }
);
