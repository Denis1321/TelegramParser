<?php

namespace App\Telegram\WebClient;

use AurimasNiekis\FFI;

class TelegramConnect
{
    private static string $tdlib_path = 'C:\OSPanel\domains\Telegram-Parse\vendor\td\build\Release\tdjson.dll';
    private static int $api_id = 28582477;
    private static string $api_hash = '1491ae4160f5128eeb18c932c8d22743';
    private string $phone;
    private FFI\TdLib $client;
    private $answer = true;

    public function __construct(){
        $this->client = new FFI\TdLib(self::$tdlib_path);//pass custom path to tdjson library

        if ($this->authorize_state()){
            while (($this->answer = $this->iteratorAnswer()) !== null){
                if ($this->check_authorize()){
//                    $this->log_in();
                }
                else{
                    $this->log_out();
//                    $this->log_in();
                }
            }
        }
    }

    private function authorize_state(): bool{
        foreach ($this->answer = $this->iteratorAnswer() as $answer){
            if ($answer['@type'] === 'updateAuthorizationState'){
                $this->client->send([
                    '@type' => 'setTdlibParameters',
                    'use_test_dc' => false,
                    'database_directory' => 'tdlib-db',
                    'file_directory' => 'tdlib-files',
                    'use_file_database' => false,
                    'use_chat_info_database' => false,
                    'use_message_database' => 1,
                    'use_secret_chats' => 1,
                    'api_id' => self::$api_id,
                    'api_hash' => self::$api_hash,
                    'system_language_code' => 'en',
                    'device_model' => 'Desktop',
                    'system_version' => '',
                    'application_version' => '2.0',
                    'enable_storage_optimizer' => 1,
                    'ignore_file_names' => false,
                ]);
                return true;
            }
        }
        return false;
    }

    private function check_authorize(): bool{
        foreach ($this->answer = $this->iteratorAnswer() as $answer){
            if ($answer['@type'] === 'updateAuthorizationState' && $answer['authorization_state']['@type'] === 'authorizationStateWaitPhoneNumber'){
                return true;
            }
            if ($answer['@type'] === 'updateAuthorizationState' && $answer['authorization_state']['@type'] === 'authorizationStateReady'){
                return false;
            }
        }
        return false;
    }

    private function log_in(string $phone, string $code): bool{
        if ($phone){
            $this->client->send([
                '@type' => 'setAuthenticationPhoneNumber',
                'phone_number' => $phone,
            ]);

            foreach ($this->answer = $this->iteratorAnswer() as $answer){
                if ($some = 1){return true;}
            }
        }
        if ($code){
            $this->client->send([
                '@type' => 'checkAuthenticationCode',
                'code' => $code,
            ]);

            foreach ($this->answer = $this->iteratorAnswer() as $answer){
                if ($some = 1){return true;}
            }
        }

        return false;
    }

    private function log_out(): bool{
        $this->client->send([
            '@type' => 'logOut',
        ]);

        foreach ($this->answer = $this->iteratorAnswer() as $answer){
            if ($some = 1){return true;}
        }
        return false;
    }
    private function iteratorAnswer(): iterable{
        while (($iterate = $this->client->receive(2)) != null)
            yield $iterate;
    }
}
