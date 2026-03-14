<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/login', [Api\AuthController::class, 'login'])->middleware(['custom.throttle:10,1']);
Route::post('/auth/register', [Api\AuthController::class, 'register'])->middleware(['custom.throttle:10,1']);

Route::get('/auth/2fa', [Api\AuthController::class, 'method2fa'])->middleware(['auth:2fa', 'custom.throttle:10,1']);
Route::post('/auth/2fa/challenge', [Api\AuthController::class, 'challenge2fa'])->middleware(['auth:2fa', 'custom.throttle:10,1']);
Route::post('/auth/2fa/verify', [Api\AuthController::class, 'verify2fa'])->middleware(['auth:2fa', 'custom.throttle:10,1']);
Route::get('/auth/email/verify/{id}/{hash}', [Api\AuthController::class, 'emailVerify'])->whereUuid('id')->middleware('signed')->name('api.auth.verify_email');

Route::group(['middleware' => 'auth:sanctum'], function () {

    // App Version
    Route::group([
        'prefix' => 'app-version',
    ], function () {
        Route::get('/', [Api\AppVersionController::class, 'index'])->middleware(['role:developer']);
        Route::post('/', [Api\AppVersionController::class, 'store'])->middleware(['role:developer']);
        Route::get('/{id}', [Api\AppVersionController::class, 'show'])->middleware(['role:developer'])->whereUuid('id');
        Route::put('/{id}', [Api\AppVersionController::class, 'update'])->middleware(['role:developer'])->whereUuid('id');
        Route::delete('/{id}', [Api\AppVersionController::class, 'destroy'])->middleware(['role:developer'])->whereUuid('id');
    });

    // Notification
    Route::group(['prefix' => 'notification'], function () {
        Route::get('/', [Api\NotificationController::class, 'index']);
        Route::get('/read/{id}', [Api\NotificationController::class, 'read'])->whereUuid('id');
        Route::get('/read-all', [Api\NotificationController::class, 'readAll']);
        Route::get('/unread-count', [Api\NotificationController::class, 'unreadCount']);
    });

    // Auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/logout', [Api\AuthController::class, 'logout']);
        Route::get('/me', [Api\AuthController::class, 'me']);
        Route::put('/me', [Api\AuthController::class, 'update']);
        Route::put('/change-password', [Api\AuthController::class, 'changePassword']);
        Route::get('/account-security-level', [Api\AuthController::class, 'accountSecurityLevel']);
        Route::post('/add-email', [Api\AuthController::class, 'addEmail']);
        Route::post('/enable-google2fa', [Api\AuthController::class, 'enableGoogle2fa']);
        Route::get('/qrcode-url-google2fa', [Api\AuthController::class, 'qrcodeUrlGoogle2fa']);
    });

    // Categories
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [Api\CategoryController::class, 'index']);
        Route::post('/', [Api\CategoryController::class, 'store']);
        Route::get('/{id}', [Api\CategoryController::class, 'show'])->whereUuid('id');
        Route::put('/{id}', [Api\CategoryController::class, 'update'])->whereUuid('id');
        Route::delete('/{id}', [Api\CategoryController::class, 'destroy'])->whereUuid('id');
    });

    // Accounts
    Route::group(['prefix' => 'accounts'], function () {
        Route::get('/', [Api\AccountController::class, 'index']);
        Route::post('/', [Api\AccountController::class, 'store']);
        Route::get('/{id}', [Api\AccountController::class, 'show'])->whereUuid('id');
        Route::put('/{id}', [Api\AccountController::class, 'update'])->whereUuid('id');
        Route::delete('/{id}', [Api\AccountController::class, 'destroy'])->whereUuid('id');
    });

    // Transactions
    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/', [Api\TransactionController::class, 'index']);
        Route::post('/', [Api\TransactionController::class, 'store']);
        Route::get('/{id}', [Api\TransactionController::class, 'show'])->whereUuid('id');
        Route::put('/{id}', [Api\TransactionController::class, 'update'])->whereUuid('id');
        Route::delete('/{id}', [Api\TransactionController::class, 'destroy'])->whereUuid('id');
    });

    // Budgets
    Route::group(['prefix' => 'budgets'], function () {
        Route::get('/progress', [Api\BudgetController::class, 'progress']);
        Route::get('/', [Api\BudgetController::class, 'index']);
        Route::post('/', [Api\BudgetController::class, 'store']);
        Route::get('/{id}', [Api\BudgetController::class, 'show'])->whereUuid('id');
        Route::put('/{id}', [Api\BudgetController::class, 'update'])->whereUuid('id');
        Route::delete('/{id}', [Api\BudgetController::class, 'destroy'])->whereUuid('id');
    });

    // Bills
    Route::group(['prefix' => 'bills'], function () {
        Route::get('/', [Api\BillController::class, 'index']);
        Route::post('/', [Api\BillController::class, 'store']);
        Route::get('/{id}', [Api\BillController::class, 'show'])->whereUuid('id');
        Route::put('/{id}', [Api\BillController::class, 'update'])->whereUuid('id');
        Route::delete('/{id}', [Api\BillController::class, 'destroy'])->whereUuid('id');
    });

    // Goals
    Route::group(['prefix' => 'goals'], function () {
        Route::get('/', [Api\GoalController::class, 'index']);
        Route::post('/', [Api\GoalController::class, 'store']);
        Route::get('/{id}', [Api\GoalController::class, 'show'])->whereUuid('id');
        Route::put('/{id}', [Api\GoalController::class, 'update'])->whereUuid('id');
        Route::delete('/{id}', [Api\GoalController::class, 'destroy'])->whereUuid('id');
    });

    // Reports
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/monthly', [Api\ReportController::class, 'monthly']);
        Route::get('/yearly', [Api\ReportController::class, 'yearly']);
        Route::get('/category-expense', [Api\ReportController::class, 'categoryExpense']);
    });

    // AI Financial Insights
    Route::group(['prefix' => 'ai-insights'], function () {
        Route::get('/', [Api\FinancialInsightController::class, 'getInsights']);
        Route::delete('/cache', [Api\FinancialInsightController::class, 'clearCache']);
    });

    // Dashboard
    Route::get('/dashboard', [Api\DashboardController::class, 'index']);

    // Province
    Route::group(['prefix' => 'provinces'], function () {
        Route::get('/', [Api\ProvinceController::class, 'index'])->middleware(['role:developer,admin']);
    });

    // Select List
    Route::group(['prefix' => 'select-list'], function () {
        Route::get('/role', [Api\SelectListController::class, 'role']);
        Route::get('/user', [Api\SelectListController::class, 'user']);
        Route::get('/countries', [Api\SelectListController::class, 'countries']);
        Route::get('/provinces', [Api\SelectListController::class, 'provinces']);
        Route::get('/cities', [Api\SelectListController::class, 'cities']);
        Route::get('/districts', [Api\SelectListController::class, 'districts']);
        Route::get('/villages', [Api\SelectListController::class, 'villages']);
    });
});

// Helper
Route::group(['prefix' => 'helper'], function () {
    Route::get('/latest-version/{type}', [Api\HelperController::class, 'latestVersion']);
});

// Storage
Route::group([
    'middleware' => 'auth:sanctum',
], function () {
    Route::get('/storage/{path}', [Api\StorageController::class, 'show'])
        ->where('path', '(.*)?')
        ->name('api.storage.show');
});

// Hanya untuk keperluan testing. Silakan hapus saat pengembangan.
Route::get('/test-error-500', [Api\ForceErrorController::class, 'testError500']);
Route::get('/test-error-502', [Api\ForceErrorController::class, 'testError502']);
Route::get('/test-error-503', [Api\ForceErrorController::class, 'testError503']);
Route::get('/test-error-504', [Api\ForceErrorController::class, 'testError504']);
Route::get('/test-error-400', [Api\ForceErrorController::class, 'testError400']);
Route::get('/test-error-401', [Api\ForceErrorController::class, 'testError401']);
Route::get('/test-error-403', [Api\ForceErrorController::class, 'testError403']);
Route::get('/test-error-404', [Api\ForceErrorController::class, 'testError404']);
Route::get('/test-error-422', [Api\ForceErrorController::class, 'testError422']);
Route::get('/test-error-429', [Api\ForceErrorController::class, 'testError429'])->middleware('custom.throttle:10,1');
Route::get('/test-swagger-auto-generate', [Api\ForceErrorController::class, 'testSwaggerAutoGenerate']);
Route::get('/test-enum-list', [Api\ForceErrorController::class, 'testEnumList']);
Route::post('/test-file-upload-single', [Api\ExampleFileUploadController::class, 'singleUpload'])->middleware('auth:sanctum');
Route::post('/test-file-upload-multiple', [Api\ExampleFileUploadController::class, 'multipleUpload'])->middleware('auth:sanctum');
Route::get('/test-send-firebase', [Api\ExampleFirebaseHttpV1Controller::class, 'store']);
