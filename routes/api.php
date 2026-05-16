<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;



Route::prefix('auth')->group(function () {


Route::post(
        '/register',
        [AuthController::class, 'register']
    );


    Route::post(
        '/login',
        [AuthController::class, 'login']
    );


    Route::middleware('auth:sanctum')
        ->post(
            '/logout',
            [AuthController::class, 'logout']
        );

});


Route::middleware([
    'auth:sanctum',
    'verified',
    'role:admin'
])->get(

    '/admin',

    function () {

        return response()->json([

            'message' => 'Welcome admin'

        ]);

    }

);


Route::middleware([
    'auth:sanctum',
    'verified',
    'role:doctor'
])->get(

    '/doctor',

    function () {

        return response()->json([

            'message' => 'Welcome doctor'

        ]);

    }

);


Route::middleware([
    'auth:sanctum',
    'verified',
    'role:patient'
])->get(

    '/patient',

    function () {

        return response()->json([

            'message' => 'Welcome patient'

        ]);

    }

);


Route::middleware('auth:sanctum')
->get(

    '/email/verify/{id}/{hash}',

    function (
        EmailVerificationRequest $request
    ) {

        $request->fulfill();

        return response()->json([

            'message' => 'Email verified'

        ]);

    }

)->name('verification.verify');


Route::get(

    '/email/verify',

    function () {

        return response()->json([

            'message' => 'Email not verified'

        ], 403);

    }

)->name('verification.notice');


Route::middleware('auth:sanctum')
->post(

    '/email/verification-notification',

    function (Request $request) {

        $request
            ->user()
            ->sendEmailVerificationNotification();


        return response()->json([

            'message' => 'Verification sent'

        ]);

    }

)->name('verification.send');
