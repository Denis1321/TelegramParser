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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|void
     */
    public function store(Request $request)
    {
        $code = $request->input('code');
        if (strlen($code)){
            $stop = 1;
        }
        $user = new User();
        $credentials = \Validator::validate(
            ['phone'=>$request->get('phone')],
            ['phone'=> 'numeric | max:999999999999999 | min:70000000000']
        );
        $user->phone = $credentials['phone'];
        $attempt = User::firstWhere('phone','=',$user->phone);
        if (!$attempt){
            $user->save();
        }else{
            $user = $attempt;
        }
        $user = Auth::loginUsingId($user->id);
        $code = $request->input('code');
        if (strlen($code)){
            $user->setTelegramClient(new TelegramConnect());
            $user->getTelegramClient()->logIn('', $code);
        }
        if (strlen($user->phone)){
            $user->setTelegramClient(new TelegramConnect());
            if ($user->getTelegramClient()->logIn($user, '')){
                return redirect('/sendCode');
            }
        }

    }

    public function parseTiksanAuto(Request $request){
        $user = new User();
        $user->phone = '79029201582';
        $user->setTelegramClient(new TelegramConnect());
        $user->getTelegramClient()->logIn($user,'60812');
        $members = $user->getTelegramClient()->parseTiksanAuto();
        $content = '';
        foreach ($members as $member){
            foreach ($member as $item){
                $content .= $item . ' ';
            }
            $content .= PHP_EOL;
        }
        \File::put('membersTiksanAuto.txt', $content, true);
    }
}
