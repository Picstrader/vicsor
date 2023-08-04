<?php
session_start();
if(!$_SESSION['admin']) {
    header('Location: ' . '/admin.php');
}
include_once '../config.php';
include_once '../helpers/FileCommander.php';
include_once '../helpers/DbQueries.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'set_referral_percent':
            $percent = $_POST['referral_percent'];
            if((float) $percent < 0) {
                break;
            }
            setReferralData($percent);
            break;
    }
}
$referral_data = getReferralData();
if(count($referral_data) <= 0) {
    setReferralDefault(); // 10%
    $referral_data = [
        'value' => 0
    ];
} else {
    $referral_data = $referral_data[0];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PicsTrader</title>
    <style>
    </style>
</head>

<body>
    <section class="referral-program">
        <div>
            <span>Current referral percent</span>
            <span id="percent"><?= $referral_data['value'] ?></span>
            <spam>%</span>
        </div>
        <div class="referral-program-form-cover">
            <form method="POST">
                <label class="referral-program-percent-title" for="referral-program-percent">Set percent of referral program</label>
                <input type="number" step="any" id="referral-program-percent" class="referral-program-percent" name="referral_percent">
                <input type="hidden" name="action" value="set_referral_percent">
                <input type="submit" value="Enter">
            </form>
        </div>
    </section>
</body>

</html>