<?php
include_once 'config.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/Validation.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/PersonalAccountFunctions.php';
// $countries = getCountries();

$user_data_arr = getUserData(getLoginUserId());
$user_data = $user_data_arr[0];
$_SESSION['current_phone'] = $user_data['phone'];

if (!isLogin()) {
    header('Location: ' . '/login.php');
}
$avatar_data = getUserAvatar(getLoginUserId());
$avatar = $avatar_data[0]['avatar'];

if ($avatar == '') {
    $avatar = AVATAR_DEFAULT;
} else if (file_exists('inc/assets/img/' . $avatar_data[0]['avatar'])) {
    $avatar = 'inc/assets/img/' . $avatar;
} else if ((strpos($avatar, "http:") === 0) || (strpos($avatar, "https:") === 0)) {

} else {
    $avatar = AVATAR_DEFAULT;
}
$referral_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/referral.php?referrer=" . $user_data['id'];
$referral_data = getReferralData();
if(count($referral_data) > 0) {
    $referral_data = $referral_data[0];
}
$err_message = false;
$err_field = false;
if ($_SESSION['error_type']) {
    $err_field = Validation::$errors[$_SESSION['error_type']]['field'];
    $err_message = Validation::$errors[$_SESSION['error_type']]['message'];
    $err_message = $fs[$err_message];
}
?>
<script>
    var user_birth_data = {
        year: Number('<?= $date_year ?>'),
        month: Number('<?= $date_month ?>'),
        day: Number('<?= $date_day ?>')
    };
</script>
<section class="trade__heading_section">
    <div class="breadcrumbs">
        <div class="breadcrumbs-home" onClick="location.href='/'"></div>
        <div class="breadcrumbs-next"><img class="breadcrumbs-home-next"></div>
        <div class="breadcrumbs-page">
            <?= $fs['Profile'] ?>
        </div>
    </div>
</section>
<section class="profile-block">
    <div class="profile-block-main">
        <div class="profile-block-inner">
            <form class="pers_acc__inf-general-photo" method="post" enctype="multipart/form-data"
                style="background-image: url('<?= $avatar ?>')">
                <input style="position:sticky;" type="file" class="form-control-avatar" name="fileToUpload"
                    id="fileToUpload" onchange="avatarFileSelected()">
                <input type="hidden" name="action" value="avatar">
                <input type="hidden" name="tab" value="">
                <div class="pers_acc__inf-general-photo-add" name="fileToUpload" id="fileToUpload"
                    title="Select image to upload">
                </div>
                <input type="submit" value="Upload" name="submit" class="pers_acc__inf-general-photo-submit"
                    id="avatar_send_submit">
                <div class="invalid-feedback">
                    <?= $err_field == 'all' ? $err_message : ''; ?>
                </div>
            </form>
            <div class="personal-account-profile-referral">
                <a href="javascript:void(0)" class="personal-account-profile-referral-link" onclick="copyReferralLink()"><?= $fs['copy your referral link'] ?></a>
                <div class="personal-account-profile-referral-description"><?= str_replace("{{percent}}", isset($referral_data['value']) ? $referral_data['value'] : '0', $fs['referral description']); ?></div>
                <input type="hidden" id="referral_link" value="<?= $referral_link ?>">
            </div>
            <form class="registration__form" method="post">
            <div class="form-group-registration-nickname">
                <label class="form-label-registration" for="nickname">
                    <?= $fs['Nickname'] ?><span class="span__ruquired">*</span>
                </label>
                <input type="text" id="nickname"
                    class="form-control-registration-name <?= $err_field == 'nickname' ? 'is-invalid' : '' ?>" name="nickname"
                    value="<?= $_SESSION['fields']['nickname'] ? $_SESSION['fields']['nickname'] : $user_data['nickname'] ?>" required>
                <div class="invalid-feedback">
                    <?= $err_field == 'nickname' ? $err_message : '' ?>
                </div>
            </div>
            <div class="form-group-registration-email" style="<?= $is_google_aoth ? 'display:none;' : '' ?>">
                <label class="form-label-registration" for="email">
                    <?= $fs['Email'] ?><span class="span__ruquired">*</span>
                </label>
                <input type="email" id="email" class="form-control <?= $err_field == 'email' ? 'is-invalid' : '' ?>"
                    name="email" value="<?= $_SESSION['fields']['email'] ? $_SESSION['fields']['email'] : $user_data['email'] ?>" <?=
                          $is_google_aoth ? '' : 'required' ?>>
                <?php if (!(bool) $user_data['verification']) { ?>
                        <div class="personal-account-email-confirm-container">
                            <span id="email-confirm-notice" class="personal-account-email-confirm-notice">
                                <?= $fs['Email not confirmed'] ?>
                            </span>
                            <a href="javascript:void(0)" onclick="sendConfirmEmail()"
                                class="personal-account-email-confirm-button">
                                <?= $fs['Confirm'] ?>
                            </a>
                        </div>
                    <?php } ?>
                <div class="invalid-feedback">
                    <?= $err_field == 'email' ? $err_message : '' ?>
                </div>
            </div>
            <div class="form-group-registration-phone">
                <label class="form-label-registration" for="phone"><?= $fs['Phone number'] ?><span class="span__ruquired">*</span></label>
                <input type="tel" id="phone" class="form-control-registration-phone <?= $err_field == 'phone' ? 'is-invalid' : '' ?>"
                    name="phone" value="<?= $_SESSION['fields']['phone'] ? $_SESSION['fields']['phone'] : $user_data['phone'] ?>" required>
                    <?php if (!(bool) $user_data['phone_verification']) { ?>
                        <div class="personal-account-email-confirm-container">
                            <span id="phone-confirm-notice" class="personal-account-email-confirm-notice">
                                <?= $fs['Phone not confirmed'] ?>
                            </span>
                            <a href="javascript:void(0)" id="phone-confirm-button" onclick="sendConfirmPhone(this, '<?= $user_data['phone'] ?>')"
                                class="personal-account-email-confirm-button">
                                <?= $fs['Confirm'] ?>
                            </a>
                        </div>
                    <?php } ?>
                <div class="invalid-feedback">
                    <?= $err_field == 'phone' ? $err_message : '' ?>
                </div>
            </div>
            <input type="hidden" id="full-phone" name="phone" value="<?= $user_data['phone'] ?>">
            <input type="hidden" name="action" value="new_data">
            <input type="hidden" name="tab" value="profile">
            <input type="submit" value="<?= $fs['Update'] ?>" name="submit" class="pers_acc__profile-data-submit">
        </form>
        </div>
    </div>
</section>
<?php
$_SESSION['error_type'] = false;
$_SESSION['fields'] = [
    'nickname' => '',
    'email' => '',
    'password' => '',
    'phone' => '',
    'second_wallet' => '',
];
?>