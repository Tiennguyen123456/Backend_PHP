<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\LogSendEmailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        $version = App::version();
        return response()->json(['version' => $version]);
    });

    Route::get('/connect', function () {
        echo "Connected!";
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('throttle:2,1')->group(function () {
    Route::post('/reset-password', [AuthController::class, 'sendMailResetPassword']);
});
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword']);

Route::get('/event/client/import-sample', [ClientController::class, 'sample']);

# Register client from LP
Route::post('/event/register-client', [ClientController::class, 'registerClient']);

# Get post by slug
Route::get('/post/get-by-slug', [PostController::class, 'getBySlug']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/test', [TestController::class, 'testRedis']);

    /* USER */
    Route::get('/self', [UserController::class, 'self']);
    Route::get('/users', [UserController::class, 'list'])->middleware('permission:user:view');
    Route::get('/user/{id}', [UserController::class, 'detail'])->middleware('permission:user:view');
    Route::post('/user/store', [UserController::class, 'store'])->middleware('permission:user:create');
    Route::delete('/user/{id}', [UserController::class, 'remove'])->middleware('permission:user:delete');

    /* COMPANY */
    Route::get('/companies', [CompanyController::class, 'index'])->middleware('permission:company:view');
    Route::get('/company/{id}', [CompanyController::class, 'detail'])->middleware('permission:company:view');
    Route::post('/company/store', [CompanyController::class, 'store'])->middleware('permission:company:create');
    Route::delete('/company/{id}', [CompanyController::class, 'remove'])->middleware('permission:company:delete');

    /* EVENT */
    Route::get('/events', [EventController::class, 'list'])->middleware('permission:event:view');
    Route::get('/event/main-fields', [EventController::class, 'listMainField'])->middleware('permission:event:config');
    Route::post('/event/qr-checkin', [EventController::class, 'qrCheckin'])->middleware('permission:event:create');
    Route::get('/event/dashboard-report', [EventController::class, 'dashboardReport'])->middleware('permission:dashboard:view');

    Route::get('/event/{id}', [EventController::class, 'detail'])->middleware('permission:event:view');
    Route::post('/event/store', [EventController::class, 'store'])->middleware('permission:event:create');
    Route::delete('/event/{id}', [EventController::class, 'remove'])->middleware('permission:event:delete');

    /* CUSTOM FIELDS */
    Route::get('/event/{id}/custom-fields', [EventController::class, 'listCustomField'])->middleware('permission:event:config');
    Route::post('/event/{id}/custom-field/store', [EventController::class, 'storeCustomField'])->middleware('permission:event:create');
    Route::delete('/event/custom-field/{id}', [EventController::class, 'removeCustomField'])->middleware('permission:event:delete');
    /* CLIENT */
    Route::get('/generate-client-qrcode', [ClientController::class, 'generateQrCode'])->middleware('permission:event:view'); // for testing
    Route::get('/event/{id}/clients', [ClientController::class, 'list'])->middleware('permission:client:view');
    Route::get('/event/{id}/client/summary', [ClientController::class, 'summary'])->middleware('permission:client:view');
    Route::post('/event/{id}/client/import', [ClientController::class, 'import'])->middleware('permission:client:import');
    Route::post('/event/{id}/client/store', [ClientController::class, 'store'])->middleware('permission:client:create');
    Route::post('/event/{id}/client/{clientId}/checkin', [ClientController::class, 'checkin'])->middleware('permission:client:check-in');
    Route::get('/event/{id}/client/{clientId}/getQrData', [ClientController::class, 'getQrData'])->middleware('permission:client:view');
    Route::delete('/event/{id}/client/{clientId}', [ClientController::class, 'deleteClient'])->middleware('permission:client:delete');

    /* CAMPAIGN */
    Route::get('/campaigns', [CampaignController::class, 'list'])->middleware('permission:campaign:view');
    Route::get('/campaign/{id}', [CampaignController::class, 'detail'])->middleware('permission:campaign:view');
    Route::post('/campaign/store', [CampaignController::class, 'store'])->middleware('permission:campaign:create');
    // Route::post('/campaign/{id}/updateMailContent', [CampaignController::class, 'updateMailContent'])->middleware('permission:campaign:create');
    Route::post('/campaign/{id}/action', [CampaignController::class, 'handleAction'])->middleware('permission:campaign:create');

    /* LOG SEND EMAIL*/
    Route::get('/campaign/{id}/log-send-email', [LogSendEmailController::class, 'list']);

    /* POST */
    Route::get('/posts', [PostController::class, 'list'])->middleware('permission:post:view');
    Route::get('/post/{id}', [PostController::class, 'detail'])->middleware('permission:post:view');
    Route::post('/post/store', [PostController::class, 'store'])->middleware('permission:post:create');
    Route::delete('/post/{id}/delete-background-img', [PostController::class, 'deleteBackgroundImg'])->middleware('permission:post:create');
    Route::delete('/post/{id}', [PostController::class, 'delete'])->middleware('permission:post:delete');

    /* COUNTRY */
    // Route::get('/countries', [CountryController::class, 'index']);
    // Route::get('/country/default', [CountryController::class, 'getDefaultCountry']);
    // Route::get('/country/fetch-global-countries', [CountryController::class, 'fetchGobalCountry']);

    /* ROLE */
    Route::get('/roles', [RoleController::class, 'list'])->middleware('permission:user_role:view');
    Route::post('/role/store', [RoleController::class, 'store'])->middleware('permission:user_role:create');
    // Route::post('/role/assign', [RoleController::class, 'assign'])->middleware('permission:user_role:assign-to-user');

    /* PERMISSION */
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:user_permission:view');
    Route::get('/permission/self', [PermissionController::class, 'getListFromCurrentUser'])->middleware('permission:user_permission:view');
    Route::get('/permission/role/{roleId}', [PermissionController::class, 'getListFromRole'])->middleware('permission:user_permission:view');
    Route::post('/permission/assign', [PermissionController::class, 'assignToRole'])->middleware('permission:user_permission:assign-to-role');
});
