<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Telegram\TelegramBotController;
use App\Models\User;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [function () {
    return 'Всё будет хорошо! Видишь на горизонте - сплошные удачи.';
}]);

Route::get('/puxan/popigr/pupil/review', [function () {
    return '<img src="https://igroutka.ru/uploads/posts/2022-03/3Pandas2Night_16468507356228f2afd2d7e5.42185879.jpeg">';
}]);

Route::get('/seed/', [function () {
    $faker = Faker\Factory::create();

    for ($i = 0; $i < 25; $i++) {
        User::create([
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => '5',
        ]);
    }
}]);


Route::group(['namespace' => 'telegram-bot'], function () {
    Route::get('/bot', [TelegramBotController::class, 'show']);
    Route::get('/bot/send-message', [TelegramBotController::class, 'sendMessage']);
});

Route::get('/test', [TestController::class, 'testFunc']);
