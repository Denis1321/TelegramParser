<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Telegram\WebClient\TelegramConnect;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    private TelegramConnect $telegram_client;

    public function enabledTelegramClient():bool
    {
        if ($this->telegram_client !== null){
            return true;
        }
        return false;
    }

    public function getTelegramClient():TelegramConnect
    {
        return $this->telegram_client;
    }

    public function setTelegramClient(TelegramConnect $telegramConnect):void
    {
        $this->telegram_client = $telegramConnect;
    }
}
