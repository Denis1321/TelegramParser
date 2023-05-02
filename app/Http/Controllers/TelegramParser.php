<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Telegram\WebClient\TelegramConnect;

class TelegramParser extends Controller
{
    public function index(Request $request):void
    {
        $client = new TelegramConnect();
    }
}
