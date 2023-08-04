<?php
function sendMessage($chat_id, $message) {
    $send_data = [
        'method' => 'sendMessage',
        'text' => $message,
        'chat_id' => $chat_id
    ];
    sendTelegram($send_data['method'], $send_data);
}

function sendTelegram($method, $data)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        //CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/' . $method,
        CURLOPT_POSTFIELDS => $data,
        //CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), [])
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
function saveLastCommand($data) {
    $fields = [];
    $fields['last_command'] = strtolower($data['text'] ? $data['text'] : $data['data']);
    $fields['telegram_id'] = $data['chat']['id'];
    $is_exists = getLastTelegramCommand($fields);
    if(count($is_exists) > 0) {
        updateLastTelegramCommand($fields);
    } else {
        setLastTelegramCommand($fields);
    }
}

function clearLastCommand($data) {
    $fields = [];
    $fields['last_command'] = '';
    $fields['telegram_id'] = $data['chat']['id'];
    $is_exists = getLastTelegramCommand($fields);
    if(count($is_exists) > 0) {
        updateLastTelegramCommand($fields);
    } else {
        setLastTelegramCommand($fields);
    }
}

function getLastCommand($data) {
    $fields = [];
    $fields['telegram_id'] = $data['chat']['id'];
    $last_command = getLastTelegramCommand($fields);
    if(count($last_command) > 0) {
        return $last_command[0];
    } else {
        return [];
    }
}

function updateLoginData($data) {
    $fields = [];
    $fields['telegram_id'] = $data['chat']['id'];
    $fields['data'] = $data['new_data'];
    updateCommandData($fields);
}

function processLogin($last_command_data, $data) {
    if ($last_command_data['data']) {
        $login_data = json_decode($last_command_data['data'], true);
    } else {
        $login_data = [];
    }
    if (!$login_data['login']) {
        $login_data['login'] = strtolower($data['text'] ? $data['text'] : $data['data']);
        $data['new_data'] = json_encode($login_data);
        updateLoginData($data);
        sendMessage($data['chat']['id'], 'Enter your password');
    } else {
        $login_data['password'] = strtolower($data['text'] ? $data['text'] : $data['data']);
        $respond = checkLogIn($login_data);
        if($respond) {
            $data['user_id'] = $respond[0]['id'];
            telegramLogin($data);
            clearLastCommand($data);
            sendMessage($data['chat']['id'], 'You are authrized');
        } else {
            clearLastCommand($data);
            sendMessage($data['chat']['id'], 'Wrong password or login');
        }
    }
}

function telegramLogin($data) {
    $fields = [];
    $fields['user_id'] = $data['user_id'];
    $fields['telegram_id'] = $data['chat']['id'];
    setUserTelegramId($fields);
}

function telegramLogout($data) {
    $fields = [];
    $fields['telegram_id'] = $data['chat']['id'];
    unsetUserTelegramId($fields);
}

function checkTelegramLogin($data) {
    $fields = [];
    $fields['telegram_id'] = $data['chat']['id'];
    $respond = checkTelegramId($fields);
    if(count($respond) > 0) {
        return true;
    }
    return false;
}