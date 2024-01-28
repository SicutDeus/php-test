<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class TelegramBotController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = new Api('6731462485:AAGC7IlzFxqB-POBIVRp9dM9JWB_RCyPYp8');
    }

    public function show()
    {
        $response = $this->telegram->getMe();
        return $response;
    }


    public function sendMessage()
    {
        $response = $this->telegram->sendMessage([
            'chat_id' => '325577651',
            'text' => 'https://risovach.ru/upload/2014/08/mem/krasavchik_59691454_orig_.jpg'
        ]);
        $messageId = $response->getMessageId();
        return $messageId;
    }
}
