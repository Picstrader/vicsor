<?php
use Twilio\Rest\Client;
function sendSMS($phone, $code)
{
    $client = new Client(ACCOUNT_SID, AUTH_TOKEN);
    try {
        $message = $client->messages->create($phone, array('from' => TWILIO_NUMBER, 'body' => 'PicsTrader.com code verification:' . $code));
    } catch (\Throwable $th) {
        //throw $th;
        $message = false;
    }
    if ($message) {
        return true;
    } else {
        return false;
    }
}
?>