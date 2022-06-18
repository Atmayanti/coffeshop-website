<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

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

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/all-menus', function() {
    return view('user.menus');
})->name('user.menus')->withoutMiddleware(['role:admin', 'role:employee']);

Route::get('/all-menus/beverages', [MenuController::class, 'getBeverageData']);

Route::get('/all-menus/foods', [MenuController::class, 'getFoodData']);

Auth::routes(['verify' => true]);

Route::get('logout', [LoginController::class, 'logout']);

// route for buyer
Route::group(['middleware' => ['auth', 'role:buyer']], function() {
    Route::get('/order', function() {
        return view('user.order');
    })->middleware('verified'); // email must verified before accesing this route or page
    Route::get('/profile', function() {
        return view('user.profile');
    })->middleware('verified')->name('user.profile'); // email must verified before accesing this route or page
    Route::get('/edit_profile', function() {
        return view('user.edit_profile');
    })->middleware('verified')->name('user.edit_profile'); // email must verified before accesing this route or page

    // route for user
        Route::resource('user', UserController::class)->middleware('verified');
});


// routes for employee:staff-dapur
Route::group(['middleware' => ['auth', 'role:staff-dapur']], function() {
    Route::prefix('employee')->group( function () {
        Route::get('/staff-dapur', [MenuController::class, 'index']);
        
        // route for menu
        Route::resource('/staff-dapur/menu', MenuController::class);
    });
});

// routes for employee:admin
Route::group(['middleware' => ['auth', 'role:admin']], function() {
    Route::prefix('admin')->group( function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/employee', [EmployeeController::class, 'index']);
        
        // route for employee
        Route::resource('employee', EmployeeController::class);
        
        // route for report
        Route::resource('report', ReportController::class);
        // Route::get('/report', [ReportController::class, 'index']);
        Route::get('/report_print', [ReportController::class, 'print_all'])->name('print');
    });
});


// routes for employee:kasir
Route::group(['middleware' => ['auth', 'role:kasir']], function() {
    Route::prefix('employee')->group( function () {
        Route::get('/kasir', [PaymentController::class, 'index']);

        // route for payment
        Route::resource('/kasir/payment', PaymentController::class);
        Route::get('/kasir/payment/print/{id}', [PaymentController::class, 'print'])->name('print_payment');
        
        Route::get('/ui-features/buttons', function () {
            return view('layouts.partials.ui-features.buttons');
        });
        Route::get('/ui-features/typography', function () {
            return view('layouts.partials.ui-features.typography');
        });
        Route::get('/icons/mdi', function () {
            return view('layouts.partials.icons.mdi');
        });
        Route::get('/forms/basic_elements', function () {
            return view('layouts.partials.forms.basic_elements');
        });
        Route::get('/charts/chartjs', function () {
            return view('layouts.partials.charts.chartjs');
        }); 
        Route::get('/tables/basic-table', function () {
            return view('layouts.partials.tables.basic-table');
        }); 
        Route::get('/samples/blank-page', function () {
            return view('layouts.partials.samples.blank-page');
        }); 
        Route::get('/samples/login', function () {
            return view('layouts.partials.samples.login');
        }); 
        Route::get('/samples/register', function () {
            return view('layouts.partials.samples.register');
        }); 
        Route::get('/samples/error-500', function () {
            return view('layouts.partials.samples.error-500');
        }); 
        Route::get('/samples/error-404', function () {
            return view('layouts.partials.samples.error-404');
        }); 
    });
    
});