<?php

namespace App\Telegram\WebClient;

use AurimasNiekis\FFI;
use Illuminate\Support\Facades\Auth;

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
    }

    public function connect()
    {
        if ($this->authorizeState()){
            while (($this->answer = $this->iteratorAnswer()) !== null){
                if ($this->checkAuthorize()){
                    return view('components.input-phone');
                }
                else{
                    $this->logOut();
                    return view('components.input-phone');
                }
            }
        }
        return view('welcome');
    }

    private function authorizeState(): bool{
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

    private function checkAuthorize(): bool{
        $return = false;
        foreach ($this->answer = $this->iteratorAnswer() as $answer){
            if ($answer['@type'] === 'updateAuthorizationState' && $answer['authorization_state']['@type'] === 'authorizationStateWaitPhoneNumber'){
                $return = true;
            }
            if ($answer['@type'] === 'updateAuthorizationState' && $answer['authorization_state']['@type'] === 'authorizationStateReady'){
                $return = false;
            }
        }
        return $return;
    }

    /**
     * @param \App\Models\User $user
     * @param string $code
     * @return bool
     * @throws \JsonException
     */
    public function logIn(\App\Models\User $user, string $code): bool{
        if ($user->phone){
            $this->authorizeState();
            if ($this->checkAuthorize()){
                $this->client->send([
                    '@type' => 'setAuthenticationPhoneNumber',
                    'phone_number' => $user->phone,
                ]);

                foreach ($this->answer = $this->iteratorAnswer() as $answer){
                    if ($answer['@type'] === 'updateAuthorizationState'
                        && $answer['authorization_state']['@type'] === 'authorizationStateWaitCode'
                        && $user->phone === $answer['authorization_state']['code_info']['phone_number']){
                        break;
                    }
                }
                if ($code){
                    $this->client->send([
                        '@type' => 'checkAuthenticationCode',
                        'code' => $code,
                    ]);

                    foreach ($this->answer = $this->iteratorAnswer() as $answer){
                        $some = 0;
                        if ($some == 1){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function logOut(): bool{
        $this->client->send([
            '@type' => 'logOut',
        ]);

        $return = false;
        foreach ($this->answer = $this->iteratorAnswer() as $answer){
            if ($some = 1){$return = true;}
        }
        return $return;
    }

    public function parseTiksanAuto(){
        $chat = -1001549973899;//TIKSAN AUTO +7 (391) 986-77- 23
        $test_supergroup = 1549973899;
        $members = [];
        $member_count = 0;
        $offset = 0;
        while ($offset <= $member_count) {
            $this->client->send([
                '@type' => 'getSupergroupMembers',
                'supergroup_id' => $test_supergroup,
                'limit' => 200,
                'filter' => [
                    '@type' => 'supergroupMembersFilterSearch',
                    'query' => '%a'
                ],
            ]);
//            $users = [];

            foreach ($this->iteratorAnswer() as $answer) {
//                if ($answer['@type'] == 'updateUser'){
//                    if ($answer['user']['@type'] == 'user'){
//                        $users[$answer['user']['usernames']['active_usernames'][0]]['id'] = $answer['user']['id'];
//                        $users[$answer['user']['usernames']['active_usernames'][0]]['first_name'] = $answer['user']['first_name'];
//                        $users[$answer['user']['usernames']['active_usernames'][0]]['last_name'] = $answer['user']['last_name'];
//                        $users[$answer['user']['usernames']['active_usernames'][0]]['phone_number'] = $answer['user']['phone_number'];
//                    }
//                }
                if ($answer['@type'] == 'updateSupergroupFullInfo') {
                    if ($answer['supergroup_id'] == $test_supergroup) {
                        $member_count = (int)$answer['supergroup_full_info']['member_count'];
                    }
                }
                if ($answer['@type'] == 'chatMembers') {
                    foreach ($answer['members'] as $member) {
                        if ($member['@type'] == 'chatMember') {
                            $members[] = ['id' => $member['member_id']['user_id'],
                                'joined_date' => $member['joined_chat_date']];
                        }
                    }
                }
            }
            $offset += 200;
        }

        foreach ($members as &$member){
            $result = $this->client->send([
//            '@type' => 'getUserFullInfo',
                '@type' => 'getUser',
                'user_id' => $member['id'],
            ]);
            foreach ($this->iteratorAnswer() as $answer) {
                if ($answer['id'] == $member['id']){
                    $member['first_name'] = $answer['first_name'];
                    $member['last_name'] = $answer['last_name'];
                    $member['phone_number'] = $answer['phone_number'];
                    $member['username'] = $answer['user']['usernames']['active_usernames'][0];
                }
            }
        }
        $members['count'] = $member_count;
        return $members;
    }
    private function iteratorAnswer(): iterable{
        while (($iterate = $this->client->receive(2)) != null)
            yield $iterate;
    }
}
