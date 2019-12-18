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

Route::get('/', function () {
    return view('home');
});
Route::get('/home', function () {
    return view('home');
});

// view track page
Route::get('/track', 'TrackController@index')->name('track.index');
Route::post('/track', 'TrackController@search')->name('track.search');

Auth::routes();

Route::middleware(['auth'])->group(function () {
	// view user page
	Route::get('/approval', 'UserController@approval')->name('user.approval');
	Route::get('/admin/user', 'UserController@index')->name('user.index');
	Route::get('/users/{id}/approve', 'UserController@approve')->name('user.approve');

	Route::middleware(['approved'])->group(function () {
		// view admin page
		Route::get('/admin', 'ProjectController@index')->name('admin.index');

		// view reports page
		Route::get('/admin/reports', 'ReportController@index')->name('report.index');

		// view projects page
		Route::resource('/admin/projects', 'ProjectController');
		Route::post('/admin/projects/update', 'ProjectController@update');
		Route::get('/admin/projects/destroy/{id}', 'ProjectController@destroy');

		// view PraPreparation Page
		Route::resource('/admin/projects/{proj_id}/pp', 'PpController');
		Route::get('/admin/projects/{proj_id}/pp/ajax/get-mt-lvl-2', 'PpController@ajax_get_mt_lvl2')->name('pp.ajax-get-mt-lvl-2');
		Route::get('/admin/projects/{proj_id}/pp/{pp_id}/delete', 'PpController@delete')->name('pp.delete');
		Route::post('/admin/projects/{proj_id}/pp/{pp_id}/update', 'PpController@update')->name('pp.update');
		Route::get('/admin/projects/{proj_id}/pp/all/qrcode', 'PpController@allqrcode')->name('pp.allqrcode');
		Route::get('/admin/projects/{proj_id}/pp/{pp_id}/qrcode', 'PpController@qrcode')->name('pp.qrcode');

		// view preparation Page
		Route::resource('/admin/projects/{proj_id}/pr', 'PrController');
		Route::get('/admin/projects/{proj_id}/pr/{pr_id}/delete', 'PrController@delete')->name('pr.delete');
		Route::post('/admin/projects/{proj_id}/pr/{pr_id}/update', 'PrController@update')->name('pr.update');
		Route::get('/admin/projects/{proj_id}/pr/all/qrcode', 'PrController@allqrcode')->name('pr.allqrcode');
		Route::get('/admin/projects/{proj_id}/pr/{pr_id}/qrcode', 'PrController@qrcode')->name('pr.qrcode');

		// view fabricatrion Page
		Route::resource('/admin/projects/{proj_id}/fa', 'FaController');
		Route::get('/admin/projects/{proj_id}/fa/{fa_id}/delete', 'FaController@delete')->name('fa.delete');
		Route::post('/admin/projects/{proj_id}/fa/{fa_id}/update', 'FaController@update')->name('fa.update');
		Route::get('/admin/projects/{proj_id}/fa/all/qrcode', 'FaController@allqrcode')->name('fa.allqrcode');
		Route::get('/admin/projects/{proj_id}/fa/{fa_id}/qrcode', 'FaController@qrcode')->name('fa.qrcode');

		// view Sub Assembly Page
		Route::resource('/admin/projects/{proj_id}/sa', 'SaController');
		Route::get('/admin/projects/{proj_id}/sa/{sa_id}/delete', 'SaController@delete')->name('sa.delete');
		Route::post('/admin/projects/{proj_id}/sa/{sa_id}/update', 'SaController@update')->name('sa.update');
		Route::get('/admin/projects/{proj_id}/sa/all/qrcode', 'SaController@allqrcode')->name('sa.allqrcode');
		Route::get('/admin/projects/{proj_id}/sa/{sa_id}/qrcode', 'SaController@qrcode')->name('sa.qrcode');

		// view Assembly Page
		Route::resource('/admin/projects/{proj_id}/as', 'AsController');
		Route::get('/admin/projects/{proj_id}/as/{as_id}/delete', 'AsController@delete')->name('as.delete');
		Route::post('/admin/projects/{proj_id}/as/{as_id}/update', 'AsController@update')->name('as.update');
		Route::get('/admin/projects/{proj_id}/as/all/qrcode', 'AsController@allqrcode')->name('as.allqrcode');
		Route::get('/admin/projects/{proj_id}/as/{as_id}/qrcode', 'AsController@qrcode')->name('as.qrcode');

		// view Errection Page
		Route::resource('/admin/projects/{proj_id}/er', 'ErController');
		Route::get('/admin/projects/{proj_id}/er/{er_id}/delete', 'ErController@delete')->name('er.delete');
		Route::post('/admin/projects/{proj_id}/er/{er_id}/update', 'ErController@update')->name('er.update');
		Route::get('/admin/projects/{proj_id}/er/all/qrcode', 'ErController@allqrcode')->name('er.allqrcode');
		Route::get('/admin/projects/{proj_id}/er/{er_id}/qrcode', 'ErController@qrcode')->name('er.qrcode');

		// Report Page
		Route::resource('/admin/projects/{proj_id}/reports', 'ReportController');
		Route::post('/admin/projects/{proj_id}/reports/{report_id}/update', 'ReportController@update')->name('reports.update');

		Route::get('/admin/projects/{proj_id}/process/edit', 'ProcessController@edit')->name('process.edit');
		Route::post('/admin/projects/{proj_id}/process/update', 'ProcessController@update')->name('process.update');
	});
});
