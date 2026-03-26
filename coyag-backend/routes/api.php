<?php

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

use App\Http\Controllers\AddedServiceController;
use App\Http\Controllers\AssignedAdvisorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutonomousCommunityController;
// use App\Http\Controllers\BorrarController;
use App\Http\Controllers\BusiestController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessClientController;
use App\Http\Controllers\BusinessImportController;
use App\Http\Controllers\BusinessMultimediaController;
use App\Http\Controllers\BusinessSectorController;
use App\Http\Controllers\BusinessTimelineController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
// use App\Http\Controllers\ClientControllerOLD;
use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\ClientTimelineController;
use App\Http\Controllers\CompartirController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\ContinentController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\EmployeeController;
// use App\Http\Controllers\EmployeeControllerOLD;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyTypeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FinancialAnalysisController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HeadquarterController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationTypeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RemoveFreeTrialClientsController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\ServiceController;
// use App\Http\Controllers\ServiceControllerOLD;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TitulationController;
use App\Http\Controllers\UpdateDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserCommentController;
use App\Http\Controllers\UserCommentTypeController;
use App\Http\Controllers\VideoCallController;
use App\Http\Controllers\VideoCallTypeController;
use App\Http\Controllers\VisaDocumentTypeController;
use App\Http\Controllers\VisaRequirementController;
use App\Http\Controllers\VisaStepController;
use App\Http\Controllers\VisaStepTypeController;
use App\Http\Controllers\VisaTypeController;
use App\Http\Controllers\WalletTransactionController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ToolSubscriptionsController;
use App\Http\Controllers\StatisticsPropertiesController;
use App\Http\Controllers\BusinessToolsController;
use App\Http\Controllers\SmartlinkController;
use App\Http\Controllers\CollectorController;

use App\Http\Controllers\AreasController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\ContractStepsController;
use App\Http\Controllers\ContractStepFilesController;
use App\Http\Controllers\AiController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    // ── AI Endpoints ─────────────────────────────────────────────
    Route::group([
        'controller' => AiController::class,
        'prefix'     => 'ai',
        'middleware'  => ['auth:api'],
    ], function () {
        Route::get('/status', 'status');
        Route::post('/business-analysis', 'businessAnalysis');
        Route::post('/recommendations', 'recommendations');
        Route::post('/generate-description', 'generateDescription');
        Route::post('/chat', 'chat');
    });

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', ForgotPasswordController::class);
        Route::post('reset-password', [ResetPasswordController::class, 'reset']);
        Route::group(['middleware' => 'auth:api'], function(){
            Route::get('user', [AuthController::class, 'user']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
            Route::get('refresh', [AuthController::class, 'refresh']);
            Route::get('payload', [AuthController::class, 'payload']);
        });
    });

Route::group([
    'controller' => UserController::class,
    'prefix'     => 'user',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::put('/change-my-password', 'changeMyPassword');
    Route::put('/change-my-username', 'changeMyUsername');
    Route::post('/change-my-profile-image', 'changeMyProfileImage');
    Route::get('/image-profile', 'showProfileImage');

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::get('/', 'index');
        Route::get('/{idUser}', 'show')->where('idUser', '\d+');
    });
    Route::group(['middleware' => 'checkMinimumLevel:10'], function () {
        Route::delete('/{idUser}', 'destroy')->where('idUser', '\d+');
        Route::put('/flag-login', 'flagLogin');
    });

    Route::group(['middleware' => 'checkMinimumLevel:7'], function () {
        Route::put('/change-password', 'changePassword');
        Route::put('/change-username', 'changeUsername');
        Route::post('/change-profile-image', 'changeProfileImage');
    });
});


Route::group([
    'controller' => DepartmentController::class,
    'prefix'     => 'department',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get('', 'index');
    Route::get('/{idDepartment}', 'show')->where('idDepartment', '\d+');
});


Route::group([
    'controller' => ContinentController::class,
    'prefix'     => 'continent',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get('', 'index');
    Route::get('/{idContinent}', 'show')->where('idContinent', '\d+');
});


Route::group([
    'controller' => CountryController::class,
    'prefix'     => 'country',
    // 'middleware' => [
    //     'auth:api'
    // ],
], function() {
    Route::get('', 'index');
    Route::get('/{idCountry}', 'show')->where('idCountry', '\d+');
});


Route::group([
    'controller' => TitulationController::class,
    'prefix'     => 'titulation',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get('', 'index');
    Route::get('/{idTitulation}', 'show')->where('idTitulation', '\d+');
});


Route::group([
    'controller' => HeadquarterController::class,
    'prefix'     => 'headquarter',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get('', 'index');
    Route::get('/{idHeadquarter}', 'show')->where('idHeadquarter', '\d+');
});


Route::group([
    'controller' => AutonomousCommunityController::class,
    'prefix' => 'autonomous-community',
    'middleware' => [
        'auth:api'
    ]
], function () {
    Route::get('', 'index');
    Route::get('/{idAutonomousCommunity}', 'show')->where('idAutonomousCommunity', '\d+');
    Route::get('/{idAutonomousCommunity}/provinces', 'provinces')->where('idAutonomousCommunity', '\d+');
    Route::get('/{idAutonomousCommunity}/municipalities', 'municipalities')->where('idAutonomousCommunity', '\d+');
    Route::get('/{idAutonomousCommunity}/districts', 'districts')->where('idAutonomousCommunity', '\d+');
});

Route::group([
    'controller' => ProvinceController::class,
    'prefix'     => 'province',
    'middleware' => [
        'auth:api'
    ]
], function() {
    Route::get('', 'index');
    Route::get('/{idProvince}', 'show')->where('idProvince', '\d+');
    Route::get('/{idProvince}/municipalities', 'municipalities')->where('idProvince', '\d+');
    Route::get('/{idProvince}/districts', 'districts')->where('idProvince', '\d+');
});


Route::group([
    'controller' => MunicipalityController::class,
    'prefix'     => 'municipality',
    'middleware' => [
        'auth:api'
    ]
], function() {
    Route::get('/', 'index');
    Route::get('/{idMunicipality}', 'show')->where('idMunicipality', '\d+');
    Route::get('/{idMunicipality}/districts', 'districts')->where('idMunicipality', '\d+');
    Route::group(['middleware' => ['checkTypeOfUser:empleado']], function () {
        Route::post('/', 'store');
        Route::put('/{idMunicipality}', 'update')->where('idMunicipality', '\d+');
        Route::delete('/{idMunicipality}', 'destroy')->where('idMunicipality', '\d+');
    });
    Route::group(['middleware' => ['checkTypeOfUser:empleado', 'checkMinimumLevel:10']], function () {
        Route::post('/update-from-excel', 'updateFromExcel');
    });
});


Route::group([
    'controller' => DistrictController::class,
    'prefix'     => 'district',
    'middleware' => [
        'auth:api'
    ]
], function() {
    Route::get('/', 'index');
    Route::get('/{idDistrict}', 'show')->where('idDistrict', '\d+');
    Route::group(['middleware' => ['checkTypeOfUser:empleado']], function () {
        Route::post('/', 'store');
        Route::put('/{idDistrict}', 'update')->where('idDistrict', '\d+');
        Route::delete('/{idDistrict}', 'destroy')->where('idDistrict', '\d+');
    });
});


Route::group([
    'controller' => NeighborhoodController::class,
    'prefix'     => 'neighborhood',
    'middleware' => [
        'auth:api'
    ]
], function() {
    Route::get('/', 'index');
    Route::get('/{idNeighborhood}', 'show')->where('idNeighborhood', '\d+');
    Route::group(['middleware' => ['checkTypeOfUser:empleado']], function () {
        Route::post('/', 'store');
        Route::put('/{idNeighborhood}', 'update')->where('idNeighborhood', '\d+');
        Route::delete('/{idNeighborhood}', 'destroy')->where('idNeighborhood', '\d+');
    });
});


Route::group([
    'controller' => BusinessTypeController::class,
    'prefix'    => 'business-type',
    'middleware' => [
        'auth:api'
    ]
], function() {
    Route::get('/', 'index');
    Route::group(['middleware' => ['checkTypeOfUser:empleado', 'checkMinimumLevel:6']], function () {
        Route::post('/', 'store');
        Route::get('/{idBusinessType}', 'show')->where('idBusinessType', '\d+');
        Route::put('/{idBusinessType}', 'update')->where('idBusinessType', '\d+');
        Route::delete('/{idBusinessType}', 'destroy')->where('idBusinessType', '\d+');
    });
});


Route::group([
    'controller' => SectorController::class,
    'prefix'    => 'sector',
    'middleware' => [
        'auth:api'
    ]
], function() {
    Route::get('/', 'index');
    Route::get('/{idSector}', 'show')->where('idSector', '\d+');
    Route::group(['middleware' => ['checkTypeOfUser:empleado', 'checkMinimumLevel:6']], function () {
        Route::post('/', 'store');
        Route::put('/{idSector}', 'update')->where('idSector', '\d+');
        Route::delete('/{idSector}', 'destroy')->where('idSector', '\d+');
    });
});


Route::group([
    'controller' => RoleController::class,
    'prefix' => 'role',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ]
], function () {
    Route::get('/', 'index');
    Route::get('/{idRole}', 'show')->where('idRole', '\d+');
});


Route::group([
    'controller' => BusinessController::class,
    'prefix' => 'business',
], function () {

    Route::get('/wordpress', 'publicWordPress');
    Route::get('/{idBusiness}', 'show')->where('idBusiness', '\d+');
    
    Route::get('/indexforshare', 'indexforshare');
    
    Route::get('/exportexcel', 'exportexcel');

    Route::group(['middleware' => ['auth:api', 'checkActiveService:videoportal']], function () {
        Route::get('/index', 'publicIndex');
        Route::get('/show/{idCodeBusiness}', 'publicShow')->where('idCodeBusiness', '\d+');
    });

    Route::group(['middleware' => ['auth:api', 'checkTypeOfUser:empleado']], function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{idBusiness}', 'update')->where('idBusiness', '\d+');
        Route::post('/{idBusiness}/add-recommendation', 'addRecommendation')->where('idBusiness', '\d+');
    });

    Route::group(['middleware' => ['auth:api', 'checkTypeOfUser:empleado', 'checkMinimumLevel:7']], function () {
        Route::post('/scrap-list', 'listUrls');
        Route::delete('/{idBusiness}', 'destroy')->where('idBusiness', '\d+');
        Route::post('/activate-from-excel', 'activateFromExcel');
        Route::post('/deactivate-activate-from-excel', 'deactivateAndActivateFromExcel');
    });
});

Route::group([
    'controller' => SmartlinkController::class,
    'prefix' => 'smartlink',
], function () {
    Route::get('/', [SmartlinkController::class, 'index']);
    Route::get('/{id}', [SmartlinkController::class, 'show']);
    Route::post('/', [SmartlinkController::class, 'store']);
    Route::post('/{id}', [SmartlinkController::class, 'update']);
    Route::delete('/{id}', [SmartlinkController::class, 'destroy']);
});

Route::group([
    'controller' => CollectorController::class,
    'prefix' => 'collectors',
], function () {
    Route::get('/', [CollectorController::class, 'index']);
    Route::get('/{id}', [CollectorController::class, 'show']);

    // Route::get('/collectors', 'indexCollector');
    // Route::get('/collectors/{collector_id}', 'showCollector');   
});




Route::group([
    'prefix' => 'businesstools',
    'controller' => BusinessToolsController::class,
    'middleware' => ['auth:api', 'checkTypeOfUser:empleado']
], function () {
    Route::post('/linker', 'linker');
    Route::post('/ondemand', 'onDemand');
});



Route::group([
    'controller' => BusinessMultimediaController::class,
    'prefix' => 'business-multimedia',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ]
], function () {
    Route::get('/download/print/{idBusinessMultimedia}', 'downloadPrintScreenFile')->where('idBusinessMultimedia', '\d+');
    Route::group(['middleware' => ['checkMinimumLevel:7']], function () {
        Route::post('/', 'store');
        Route::delete('/{idBusinessMultimedia}', 'destroy')->where('idBusinessMultimedia', '\d+');
    });
});


Route::group([
    'prefix' => 'business-timeline',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ]
], function () {
    Route::get('/', BusinessTimelineController::class);
});


Route::group([
    'controller' => ContactController::class,
    'prefix' => 'contact'
], function () {
    Route::post('/', 'store');
    Route::group(['middleware' => ['auth:api', 'checkTypeOfUser:empleado']], function () {
        Route::get('/', 'index');
        Route::get('/{idContact}', 'show')->where('idContact', '\d+');
        Route::put('/{idContact}', 'update')->where('idContact', '\d+');
    });
    Route::group(['middleware' => ['auth:api', 'checkTypeOfUser:empleado', 'checkMinimumLevel:7']], function () {
        Route::delete('/{idContact}', 'destroy')->where('idContact', '\d+');
    });
});


Route::group([
    'prefix' => 'contact-form',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:cliente'
    ]
], function () {
    Route::post('/', ContactFormController::class);
});


Route::group([
    'controller' => PaymentController::class,
    'prefix' => 'payment',
    'middleware' => [
        'auth:api'
    ]
], function () {
    Route::group(['middleware' => ['checkTypeOfUser:empleado']], function () {
        Route::get('/', 'index');
        Route::get('/{idPayment}', 'show')->where('idPayment', '\d+');
    });
    Route::group(['middleware' => ['checkTypeOfUser:empleado', 'checkEmployeeTypesForAdvisory']], function () {
        Route::post('/', 'store');
        Route::put('/{idPayment}', 'update')->where('idPayment', '\d+');
        Route::post('/{idPayment}/file', 'updateFilePayment')->where('idPayment', '\d+');
        Route::get('/payments-without-associated-service', 'paymentsWithoutAssociatedService');
    });
    Route::group(['middleware' => ['checkTypeOfUser:empleado', 'checkEmployeeTypesForAdvisory', 'checkMinimumLevel:10']], function () {
        Route::delete('/{idPayment}', 'destroy')->where('idPayment', '\d+');
    });
    Route::group(['middleware' => ['checkTypeOfUser:cliente']], function () {
        Route::get('/my-payments', 'paymentIndexClient');
        Route::get('/my-payments/{idPayment}', 'paymentShowClient')->where('idPayment', '\d+');
    });
});


Route::group([
    'controller' => ClientRequestController::class,
    'prefix' => 'client-request',
    'middleware' => [
        'auth:api',
        'checkUserTypesForRequests'
    ]
], function () {
    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::get('/', 'index');
        Route::get('/{idClientRequest}', 'show')->where('idClientRequest', '\d+');
        Route::put('/{idClientRequest}', 'update')->where('idClientRequest', '\d+');
    });
    Route::group(['middleware' => 'checkTypeOfUser:cliente'], function () {
        Route::post('/', 'store');
        Route::get('/my-request', 'indexMyRequest');
    });
});


Route::group([
    'prefix'     => 'favorite',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:cliente'
    ],
], function() {
    Route::post('/', [FavoriteController::class, 'storeFavorite']);
});


Route::group([
    'controller' => FinancialAnalysisController::class,
    'prefix'     => 'financial-analysis',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ],
], function() {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{idFinancialAnalysis}', 'show')->where('idFinancialAnalysis', '\d+');
    Route::get('/client/{idClient}', 'financialAnalysesSummaryAboutAClient')->where('idFinancialAnalysis', '\d+');
    Route::put('/{idFinancialAnalysis}/add-one', 'addOne')->where('idFinancialAnalysis', '\d+');
    Route::group(['middleware' => 'checkMinimumLevel:10'], function () {
        Route::delete('/{idFinancialAnalysis}', 'destroy')->where('idFinancialAnalysis', '\d+');
    });
});


Route::group([
    'prefix'     => 'file',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ],
], function() {
    Route::get ('/download/{idFile}', [FileController::class, 'download'])->where('idFile', '\d+');
});


Route::group([
    'controller' => NotificationTypeController::class,
    'prefix'     => 'notification-type',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ],
], function() {
    Route::get ('/', 'index');
    Route::get ('/{idNotificationType}', 'show')->where('idNotificationType', '\d+');
    Route::group(['middleware' => 'checkMinimumLevel:10'], function () {
        Route::post('/', 'store');
        Route::put('/{idNotificationType}', 'update');
        Route::delete('/{idNotificationType}', 'destroy');
    });
});


Route::group([
    'controller' => NotificationController::class,
    'prefix'     => 'notification',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idNotification}', 'show')->where('idNotification', '\d+');
    Route::put('/{idNotification}/change-flag-read', 'changeFlagRead')->where('idNotification', '\d+');
});


Route::group([
    'controller' => AssignedAdvisorController::class,
    'prefix'     => 'assigned-advisor',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idAssignedAdvisor}', 'show')->where('idAssignedAdvisor', '\d+');
    Route::put('/{idAssignedAdvisor}', 'update')->where('idAssignedAdvisor', '\d+');
});


Route::group([
    'controller' => VideoCallTypeController::class,
    'prefix'     => 'video-call-type',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get ('/', 'index');
    Route::get ('/{idVideoCallType}', 'show')->where('idVideoCallType', '\d+')->middleware('checkTypeOfUser:empleado');
    Route::group(['middleware' => ['checkTypeOfUser:empleado', 'checkMinimumLevel:10']], function () {
        Route::post('/', 'store');
        Route::put('/{idVideoCallType}', 'update')->where('idVideoCallType', '\d+');
        Route::delete('/{idVideoCallType}', 'destroy')->where('idVideoCallType', '\d+');
    });
});


Route::group([
    'controller' => VideoCallController::class,
    'prefix'     => 'video-call',
    'middleware' => [
        'auth:api',
        'checkUserTypesForRequests'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idVideoCall}', 'show')->where('idVideoCall', '\d+');
    Route::group(['middleware' => 'checkTypeOfUser:cliente'], function () {
        Route::post ('/', 'store');
    });

    Route::group(['middleware'  =>  'checkTypeOfUser:empleado'], function() {
        Route::put('/{idVideoCall}/change-status-report', 'changeStatusAndReport')->where('idVideoCall', '\d+');
        Route::put('/{idVideoCall}/restart-status-report', 'restartStatusAndReport')->where('idVideoCall', '\d+');
    });

});


/*
Route::group([
    'controller' => ServiceControllerOLD::class,
    'prefix'     => 'service',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idService}', 'show')->where('idService', '\d+');

    Route::group(['middleware' => 'checkMinimumLevel:10'], function () {
        Route::post ('/', 'store');
        Route::put('/{idService}', 'update')->where('idService', '\d+');
    });
});
*/


Route::group([
    'controller' => AddedServiceController::class,
    'prefix'     => 'added-service',
    'middleware' => [
        'auth:api',
        'checkEmployeeTypesForAdvisory'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idAddedService}', 'show')->where('idAddedService', '\d+');
    Route::put('/{idAddedService}/add-payment', 'addPayment')->where('idAddedService', '\d+');
    Route::put('/{idAddedService}/update-payment-completed', 'updatePaymentCompleted')->where('idAddedService', '\d+');
    Route::post('/{idAddedService}/add-contract', 'addContract')->where('idAddedService', '\d+');
    Route::delete('/{idAddedService}/remove-contract', 'removeContract')->where('idAddedService', '\d+');
    Route::get('/{idAddedService}/download-contract', 'downloadContract')->where('idAddedService', '\d+');
});


Route::group([
    'controller' => UserCommentTypeController::class,
    'prefix'     => 'user-comment-type',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idUserCommentType}', 'show')->where('idUserCommentType', '\d+');

    Route::group(['middleware' => 'checkUserTypesForUserComment'], function () {
        Route::post('/', 'store');
        Route::put('/{idUserCommentType}', 'update')->where('idUserCommentType', '\d+');
        Route::delete('/{idUserCommentType}', 'destroy')->where('idUserCommentType', '\d+');
    });
});


Route::group([
    'controller' => UserCommentController::class,
    'prefix'     => 'user-comment',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idUserComment}', 'show')->where('idUserComment', '\d+');
    Route::post('/', 'store');

    Route::group(['middleware' => 'checkUserTypesForUserComment'], function () {
        Route::put('/{idUserComment}', 'update')->where('idUserComment', '\d+');
        Route::delete('/{idUserComment}', 'destroy')->where('idUserComment', '\d+');
    });
});


Route::group([
    'controller' => FamilyTypeController::class,
    'prefix'     => 'family-type',
    'middleware' => [
        'auth:api',
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idFamilyType}', 'show')->where('idFamilyType', '\d+');

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::put('/{idFamilyType}', 'update')->where('idFamilyType', '\d+');
        Route::delete('/{idFamilyType}', 'destroy')->where('idFamilyType', '\d+');
    });
});


Route::group([
    'controller' => FamilyController::class,
    'prefix'     => 'family',
    'middleware' => [
        'auth:api',
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idFamily}', 'show')->where('idFamily', '\d+');

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::put('/{idFamily}', 'update')->where('idFamily', '\d+');
        Route::delete('/{idFamily}', 'destroy')->where('idFamily', '\d+');
    });
});

Route::group([
    'controller' => VisaTypeController::class,
    'prefix'     => 'visa-type',
    'middleware' => [
        'auth:api',
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idVisaType}', 'show')->where('idVisaType', '\d+');

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::put('/{idVisaType}', 'update')->where('idVisaType', '\d+');
    });
});


Route::group([
    'controller' => VisaDocumentTypeController::class,
    'prefix'     => 'visa-document-type',
    'middleware' => [
        'auth:api',
    ],
], function() {

    Route::get('/', 'index');
    Route::get('/{idVisaDocumentType}', 'show')->where('idVisaDocumentType', '\d+');

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::put('/{idVisaDocumentType}', 'update')->where('idVisaDocumentType', '\d+');
        Route::delete('/{idVisaDocumentType}', 'destroy')->where('idVisaDocumentType', '\d+');
    });

});


Route::group([
    'controller' => VisaRequirementController::class,
    'prefix'     => 'visa-requirement',
    'middleware' => [
        'auth:api',
    ],
], function() {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/', 'store');
    });

    Route::group(['middleware' => ['checkActiveService:extranjeria']], function () {
        Route::get('/', 'index');
        Route::get('/{idVisaRequirement}', 'show')->where('idVisaRequirement', '\d+');
        Route::post('/{idVisaRequirement}/add-file', 'addFile')->where('idVisaRequirement', '\d+');
        Route::get('/{idVisaRequirement}/download-file', 'downloadFile');
        Route::delete('/{idVisaRequirement}/remove-file', 'removeFile')->where('idVisaRequirement', '\d+');
    });

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::put('/{idVisaRequirement}', 'update')->where('idVisaRequirement', '\d+');
        Route::delete('/{idVisaRequirement}', 'destroy')->where('idVisaRequirement', '\d+');
        Route::put('/{idVisaRequirement}/update-metadata', 'updateMetadaData')->where('idVisaRequirement', '\d+');
        Route::put('/{idVisaRequirement}/remove-metadata', 'removeMetaData')->where('idVisaRequirement', '\d+');
        Route::put('/{idVisaRequirement}/update-status', 'updateStatus')->where('idVisaRequirement', '\d+');
    });
});


Route::group([
    'controller' => VisaStepController::class,
    'prefix'     => 'visa-step',
    'middleware' => [
        'auth:api',
    ],
], function() {

    Route::group(['middleware' => ['checkActiveService:extranjeria']], function () {
        Route::get('/', 'index');
        Route::get('/{idVisaStep}', 'show')->where('idVisaStep', '\d+');
    });

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::post('/with-type', 'storeWithType');
        Route::put('/{idVisaStep}', 'update')->where('idVisaStep', '\d+');
        Route::delete('/{idVisaStep}', 'destroy')->where('idVisaStep', '\d+');
        Route::put('/{idVisaStep}/update-status', 'updateStatus')->where('idVisaStep', '\d+');
    });
});


Route::group([
    'controller' => VisaStepTypeController::class,
    'prefix'     => 'visa-step-type',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado',
    ],
], function() {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{idVisaStepType}', 'show')->where('idVisaStepType', '\d+');
    Route::put('/{idVisaStepType}', 'update')->where('idVisaStepType', '\d+');
    Route::delete('/{idVisaStepType}', 'destroy')->where('idVisaStepType', '\d+');
});


Route::group([
    'controller' => CalendarController::class,
    'prefix'     => 'calendar',
    'middleware' => [
        'auth:api'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idCalendar}', 'show')->where('idCalendar', '\d+');

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::put('/{idCalendar}', 'update')->where('idCalendar', '\d+');
        Route::put('/{idCalendar}/update-status', 'updateStatus')->where('idCalendar', '\d+');
        Route::delete('/{idCalendar}', 'destroy')->where('idCalendar', '\d+');
    });
});


Route::group([
    'controller' => DossierController::class,
    'prefix'     => 'dossier',
    'middleware' => [
        'auth:api'
    ],
], function() {

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::delete('/{idDossier}', 'destroy')->where('idDossier', '\d+');
    });

    Route::get('/{idDossier}/download-file', 'downloadFile')->where('idDossier', '\d+');
});


Route::group([
    'controller' => BusiestController::class,
    'prefix'     => 'busiest',
    'middleware' => [
        'auth:api'
    ],
], function() {

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::post('/', 'store');
        Route::delete('/{idBusiest}', 'destroy')->where('idBusiest', '\d+');
    });

    Route::get('/{idBusiest}/download-file', 'downloadFile')->where('idBusiest', '\d+');
});


Route::group([
    'controller' => BusinessClientControllerOLD::class,
    'prefix'     => 'business-client', // client business preferences
    'middleware'    =>  [
        'auth:api',
    ],
], function() {

    Route::post('/', 'store');
    Route::put('/{idBusinessClient}', 'update')->where('idBusinessClient', '\d+');
    Route::delete('/{idBusinessClient}', 'destroy')->where('idBusinessClient', '\d+');
    Route::get('/{idBusinessClient}', 'show')->where('idBusinessClient', '\d+');

    Route::group(['middleware' => 'checkTypeOfUser:empleado'], function () {
        Route::get('/', 'index');
    });

    Route::group(['middleware' => 'checkTypeOfUser:cliente'], function () {
        Route::get('/mine', 'mine');
    });

});


Route::group([
    'controller' => BusinessImportController::class,
    'prefix'     => 'business-import',
    'middleware'    =>  [
        'auth:api',
        'checkTypeOfUser:empleado',
        'checkMinimumLevel:9'
    ],
], function() {
    Route::post('/', 'store');
    Route::post('/update-business', 'updateBusiness');
    Route::post('/update-terrace-smoke-outlet', 'updateTerraceSmokeOutlet');
});


Route::group([
    'controller' => MaintenanceController::class,
    'prefix'     => 'maintenance',
    'middleware'    =>  [
        'auth:api',
        'checkTypeOfUser:empleado',
        'checkMinimumLevel:10',
    ],
], function() {
    Route::delete('/clean-disk/old-business', 'oldBusinessCD');
    Route::delete('/clean-disk/max-images', 'maxImagesCD');
});

Route::group([
    'prefix' => 'business-sector',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado',
        'checkMinimumLevel:9'
    ]
], function () {
    Route::post('/update-from-excel', [BusinessSectorController::class, 'updateFromExcel']);
});


Route::group([
    'controller' => StatisticsController::class,
    'prefix'     => 'statistics',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ]
], function () {
    Route::get('/total-clients-by-plan', 'totalClientsByPlan');
    Route::get('/clients-by-plan', 'getListClientsByPlan');
    Route::get('/clients-by-plan-excel', 'getListClientsByPlanExcel');
    Route::get('/most-visited-businesses', 'getMostVisitedBusinesses');
    Route::get('/most-visited-businesses-excel', 'getMostVisitedBusinessesExcel');
    Route::get('/authenticated-clients-by-plan', 'getTotalAuthenticatedClientsByPlan');
    Route::get('/authenticated-clients-by-plan-excel', 'getTotalAuthenticatedClientsByPlanExcel');
});

Route::group([
    'controller' => WalletTransactionController::class,
    'prefix'     => 'wallet-transaction',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ]
], function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{idWalletTransaction}', 'show')->where('idWalletTransaction', '\d+');
    Route::put('/{idWalletTransaction}', 'update')->where('idWalletTransaction', '\d+');
    Route::delete('/{idWalletTransaction}', 'destroy')->where('idWalletTransaction', '\d+');
});


Route::delete('/remove-free-trial-clients', RemoveFreeTrialClientsController::class)->middleware(['auth:api', 'checkTypeOfUser:empleado', 'checkMinimumLevel:10']);


Route::put('/update-data', UpdateDataController::class)->middleware(['auth:api', 'checkTypeOfUser:empleado', 'checkMinimumLevel:10']);

// ── Employee Routes (migrated from EmployeeControllerOLD) ──
Route::group([
    'controller' => EmployeeController::class,
    'prefix'     => 'employee',
    'middleware' => [
        'auth:api',
        'checkTypeOfUser:empleado'
    ],
], function() {
    Route::get('/', 'index');
    Route::get('/{idEmployee}', 'show')->where('idEmployee', '\d+');

    Route::group(['middleware' => 'checkPermissionByRoleType:presidente-director.tecnologia'], function () {
        Route::post('/', 'store');
        Route::put('/{idEmployee}', 'update')->where('idEmployee', '\d+');
    });
});


Route::group([
    'prefix'     => 'client'
], function() {
    Route::group(['middleware' => ['auth:api', 'checkIsSelfClientOrEmployee']], function () {
        Route::get('/{idClient}', [ClientController::class, 'show'])->where('idClient', '\d+');
        Route::put('/{idClient}', [ClientController::class, 'update'])->where('idClient', '\d+');
        Route::get('/{idClient}/browsing-history', [ClientTimelineController::class, 'getBrowsingHistory'])->where('idClient', '\d+');
        Route::get('/{idClient}/search-history', [ClientTimelineController::class, 'getSearchHistory'])->where('idClient', '\d+');
        Route::get('/{idClient}/auth-history', [ClientTimelineController::class, 'getAuthHistory'])->where('idClient', '\d+');
        Route::get('/{idClient}/favorites-list', [FavoriteController::class, 'getFavoritesList'])->where('idClient', '\d+');
        Route::get('/{idClient}/wallet-transactions', [WalletTransactionController::class, 'getClientWalletTransactions'])->where('idClient', '\d+');
        Route::get('/{idClient}/total-wallet-amount', [WalletTransactionController::class, 'getClientTotalWalletAmount'])->where('idClient', '\d+');
    });

    Route::group(['middleware' => ['auth:api', 'checkTypeOfUser:empleado']], function () {
        Route::get('/', [ClientController::class, 'index']);
    });

    Route::post('/', [ClientController::class, 'store']);

});


Route::group([
        'prefix' => 'webhook',
        'middleware' => [
            'auth:api',
            'checkTypeOfUser:empleado'
        ]
    ],
    function () {
        Route::post('/checkerhook'  , [WebhookController::class, 'CheckHook']);
        Route::post('/importerhook' , [WebhookController::class, 'ImporterHook']);
    }
);

Route::prefix('subscription-tool')->group(function () {
    Route::get('/', [ToolSubscriptionsController::class, 'find']);
    Route::post('/', [ToolSubscriptionsController::class, 'save']);
    Route::post('/remove', [ToolSubscriptionsController::class, 'remove']);

    Route::group(['middleware' => ['auth:api', 'checkTypeOfUser:empleado', 'checkEmployeeTypesForAdvisory']], function () {
        Route::get('/trigger', [ToolSubscriptionsController::class, 'trigger']);
    });
});

Route::group([
    'controller' => CompartirController::class,
    'prefix'     => 'compartir',
], function() {
    Route::get('/{user}/{businesses}', 'compartir');
});

Route::group([
    'controller' => StatisticsPropertiesController::class,
    'prefix'     => 'statistics',
], function () {
    Route::get('/{business_id}', 'get');
    Route::get('/', 'index');
});





Route::group([
    'controller' => AreasController::class,
    'prefix'     => 'areas',
    'middleware' => [ 'auth:api', 'checkTypeOfUser:empleado' ],
], function() {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/{id}', 'update')->where('id', '\d+');
    Route::delete('/{id}', 'destroy')->where('id', '\d+');
});

// Contract
Route::group([
    'controller' => ContractsController::class,
    'prefix'     => 'contracts',
    'middleware' => [ 'auth:api' ],
], function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show')->where('id', '\d+');
    Route::post('/', 'store');
    Route::put('/{id}', 'update')->where('id', '\d+');
    Route::delete('/{id}', 'destroy')->where('id', '\d+');
});

// ContractSteps
Route::group([
    'controller' => ContractStepsController::class,
    'prefix'     => 'contract-steps',
    'middleware' => [ 'auth:api' ],
], function() {
    Route::post('/', 'store');
    Route::post('/setup', 'setup');
    Route::put('/{id}', 'update')->where('id', '\d+');
    Route::delete('/{id}', 'destroy')->where('id', '\d+');
});

// ContractStepFiles
Route::group([
    'controller' => ContractStepFilesController::class,
    'prefix'     => 'contract-step-files',
    'middleware' => [ 'auth:api' ],
], function() {
    Route::post('/', 'store');
    Route::delete('/{id}', 'destroy')->where('id', '\d+');
});
 
Route::group([
    'controller' => EmployeeController::class,
    'prefix'     => 'employees',
    'middleware' => [ 'auth:api' ],
], function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show')->where('id', '\d+');
    Route::post('/', 'store');
    Route::put('/{id}', 'update')->where('id', '\d+');
    Route::delete('/{id}', 'destroy')->where('id', '\d+');
});
Route::group([
    'controller' => ClientController::class,
    'prefix'     => 'clients',
    'middleware' => [ 'auth:api' ],
], function() {
    Route::get('/', 'index');
    Route::get('/{id}', 'show')->where('id', '\d+');
    Route::post('/', 'store');
    Route::put('/{id}', 'update')->where('id', '\d+');
    Route::delete('/{id}', 'destroy')->where('id', '\d+');
});
});