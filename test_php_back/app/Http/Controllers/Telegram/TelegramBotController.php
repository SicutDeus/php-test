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


    public function send_message()
    {
        $response = $this->telegram->sendMessage([
            'chat_id' => '325577651',
            'text' => 'https://th.bing.com/th/id/R.95f5c24b58cae5d9a7ebcda1fe9716d2?rik=Cgnzv2jnfip%2feA&pid=ImgRaw&r=0'
        ]);
        $messageId = $response->getMessageId();
        return $messageId;
    }
}
