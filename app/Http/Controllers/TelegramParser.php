<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Telegram\WebClient\TelegramConnect;
use Illuminate\Support\Facades\Auth;

class TelegramParser extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user === null){
            $user = new User();
            $user->setTelegramClient(new TelegramConnect());
        }
        if (!$user->enabledTelegramClient()){
            $user->setTelegramClient(new TelegramConnect());
        }

        return $user->getTelegramClient()->connect();
    }

    /**
     * @param Request $request
     * @return void
     */
    public function store(Request $request):void
    {
        $phone = $request->input('phone');
        if (strlen($phone)){
            Auth::user()->getTelegramClient()->log_in($phone, '');
        }
        $code = $request->input('code');
        if (strlen($code)){
            Auth::user()->getTelegramClient()->log_in('', $code);
        }
    }
}
