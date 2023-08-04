<?php
include_once "config.php";
include_once "helpers/DbQueries.php";
include_once "helpers/TelegramBotFunctions.php";
$data = json_decode(file_get_contents('php://input'), TRUE);
//file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);
$data = $data['callback_query'] ? $data['callback_query'] : $data['message'];
$message = strtolower($data['text'] ? $data['text'] : $data['data']);
//$message = explode(' ', $message);

include 'app/models/ApiModel.php';
include 'app/controllers/ApiController.php';
switch ($message) {
    case '/start':
        actionStart($data);
        break;
    case '/login':
        actionLogin($data);
        break;
    case '/logout':
        actionLogout($data);
        break;
    case '/menu':
        actionMenu($data);
        break;
    case 'gallery':
        actionGallery($data);
        break;
    case '/actions':
        //actionActions($data);
        break;
    default:
        processData($data);
        break;
}

function actionStart($data)
{
    saveLastCommand($data);
    sendMessage($data['chat']['id'], $_SESSION['log'] . $data['from']['first_name'] . ' ' . $data['from']['last_name']);
}

function actionLogin($data)
{
    saveLastCommand($data);
    if (checkTelegramLogin($data)) {
        sendMessage($data['chat']['id'], 'You are already authrized');
    } else {
        sendMessage($data['chat']['id'], 'Enter your login');
    }
}

function actionLogout($data)
{
    saveLastCommand($data);
    telegramLogout($data);
    sendMessage($data['chat']['id'], 'You are logged out');
}

function processData($data)
{
    $last_command_data = getLastCommand($data);
    switch ($last_command_data['last_command']) {
        case '/login':
            processLogin($last_command_data, $data);
            break;
    }
}

function actionMenu($data)
{
    saveLastCommand($data);
    $send_data = [
        'method' => 'sendMessage',
        'text' => 'Menu',
        'chat_id' => $data['chat']['id'],
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [
                    ['text' => 'Trade'],
                    ['text' => 'Gallery'],
                ],
                [
                    ['text' => 'Rate'],
                    ['text' => 'Personal Account'],
                ]
            ]
        ])
    ];
    sendTelegram($send_data['method'], $send_data);
}

function actionGallery($data)
{
    saveLastCommand($data);
    $send_data = [
        'method' => 'sendMessage',
        'text' => 'Gallery',
        'chat_id' => $data['chat']['id'],
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'more', 'callback_data' => 'someString']
                ]
            ]
        ])
    ];
    sendTelegram($send_data['method'], $send_data);
}
?>