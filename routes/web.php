<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.order-applications.index')->with('status', session('status'));
    }

    return redirect()->route('admin.order-applications.index');
});

Auth::routes();
// Admin

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Statuses
    Route::delete('statuses/destroy', 'StatusesController@massDestroy')->name('statuses.massDestroy');
    Route::resource('statuses', 'StatusesController');

    // Order Applications
    Route::delete('order-applications/destroy', 'OrderApplicationsController@massDestroy')->name('order-applications.massDestroy');
    Route::get('order-applications/{order_application}/analyze', 'OrderApplicationsController@showAnalyze')->name('order-applications.showAnalyze');
    Route::post('order-applications/{order_application}/analyze', 'OrderApplicationsController@analyze')->name('order-applications.analyze');
    Route::get('order-applications/{order_application}/send', 'OrderApplicationsController@showSend')->name('order-applications.showSend');
    Route::post('order-applications/{order_application}/send', 'OrderApplicationsController@send')->name('order-applications.send');
    Route::resource('order-applications', 'OrderApplicationsController');

    // Comments
    Route::delete('comments/destroy', 'CommentsController@massDestroy')->name('comments.massDestroy');
    Route::resource('comments', 'CommentsController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
    }
});
