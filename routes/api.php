<?php

use App\Http\Controllers\Api\LeadController as LeadApiController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/leads', [LeadApiController::class, 'index']);
Route::get('leads/{lead}', [LeadApiController::class, 'show']);
Route::get('closeOldLeads',[LeadApiController::class, 'closeOldLeads']);
