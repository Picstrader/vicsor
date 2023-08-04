<?php
session_start();
include_once 'config.php';
include_once 'helpers/DbQueries.php';
include_once 'helpers/ECommerceLogic.php';
include_once 'helpers/FileCommander.php';
include_once 'helpers/Validation.php';
include_once 'helpers/multilang.php';
include_once 'helpers/setParsing.php';
include_once 'helpers/PersonalAccountFunctions.php';
$routes = explode('/', $_SERVER['REQUEST_URI']);
if (isLogin()) {
    ECommerceLogic::checkNewFavoriteImages();
    $favorited_images = getGalleryAmountFavorite(getLoginUserId());
    $favorited_images = (int) $favorited_images[0]['amount'];
} else {
    $favorited_images = 0;
}
$input_favorite_value = 0;
$is_shared = false;
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    $action = $_POST['action'];
    switch ($action) {
        case 'search_hash_rate':
            $input_rate_search_value = $_POST['search-header-global'];
            break;
        case 'redirect':
            $input_rate_search_value = $_POST['search-header-global'];
            header('Location: ' . '/gallery.php?searched_hash=' . $input_rate_search_value);
            break;
        case 'favorite':
            $input_favorite_value = (int) (!(bool) $_POST['favorite']);
            break;
        case 'redirect_favorite':
            header('Location: ' . '/gallery.php?favorite=' . $_POST['favorite']);
            break;
        case 'avatar':
            $image_name = FileCommander::upload_image();
            if ($image_name) {
                $fields['id'] = getLoginUserId();
                $fields['avatar'] = $image_name;
                $respond = setUserAvatar($fields);
                if ($respond) {
                    setLoginUserAvatar($fields['avatar']);
                }
                $fields = [];
            }
            break;
        case 'new_data':
            $fields = [
                'nickname' => $_POST['nickname'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
            ];
            $_SESSION['fields'] = $fields;
            $validation = Validation::validate_fields($fields);
            if (!$validation) {
                break;
            }
            $fields['id'] = getLoginUserId();
            if (!canChangeEmail($fields)) {
                $_SESSION['error_type'] = "email_verification";
                break;
            }
            $is_unique_fields = checkUniqueFieldsRegisteredUser($fields);
            if (!$is_unique_fields) {
                break;
            }
            $respond = updateUserData($fields);
            if($respond) {
                setLoginUserNickname($fields['nickname']);
                if($_SESSION['current_phone'] != $fields['phone']) {
                    $fields['user_id'] = $fields['id'];
                    unsetVerifyPhone($fields);
                }
            }
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['searched_hash'])) {
        $input_rate_search_value = $_GET['searched_hash'];
    } else if (isset($_GET['favorite'])) {
        $input_favorite_value = (int) (!(bool) $_GET['favorite']);
    } else if(isset($_GET['shared_link'])) {
        $is_shared = getImageName(['id' => $_GET['shared_link']]);
        $is_shared = $is_shared[0]['name'];
    }
}
$lang_abbr_current = '';
$bg_image = getRandomBackgroundImage();
if (count($bg_image) > 0) {
    $bg_image = $bg_image[0]['name'];
}

?>
<!DOCTYPE html>
<!--[if lt IE 8 ]><html class="no-js ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="no-js ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 8)|!(IE)]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <!--- Basic Page Needs
   ================================================== -->
    <meta charset="utf-8">
    <title>PicsTrader - We will buy your digital images!</title>
    <link rel="icon"href="inc/assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/x-svg" href="inc/assets/img/favicon.svg">
    <link rel="apple-touch-icon" href="inc/assets/img/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="inc/assets/img/favicon.png">
    <meta name="description" content="WE WILL BUY YOUR PHOTOS NOW! PicsTrader is a unique and powerful trading platform that provides people around the world with the opportunity to quickly earn money by selling any photos and images. Your profit depends only on you!">
    <meta name="author" content="PicsTrader LTD">
    <meta name="facebook-domain-verification" content="7saggirhxup6hb1izgwuh8ijzrwsz8" />
    <!-- Mobile Specific Metas
   ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- CSS
    ================================================== -->
    <?php
    if ($_COOKIE['theme'] == "dark") {
        $dark = "-dark";
    } else {
        $dark = "";
    }
    ?>
    <link rel="stylesheet" href="inc/assets/css/header<?= $dark ?>.css" id="header">
    <link rel="stylesheet" href="inc/assets/css/styles<?= $dark ?>.css" id="styles">
    <link rel="stylesheet" href="inc/assets/css/footer<?= $dark ?>.css" id="footer">
    <link rel="stylesheet" href="inc/assets/css/trade<?= $dark ?>.css" id="trade">
    <link rel="stylesheet" href="inc/assets/css/trade-h1<?= $dark ?>.css" id="tradeH1">
    <link rel="stylesheet" href="inc/assets/css/trade-slider<?= $dark ?>.css" id="trade-slider">
    <link rel="stylesheet" href="inc/assets/css/trade-modal<?= $dark ?>.css" id="trade-modal">
    <link rel="stylesheet" href="inc/assets/css/how-it-works-faqs<?= $dark ?>.css" id="how-it-works-faqs">
    <link rel="stylesheet" href="inc/assets/css/personal-account<?= $dark ?>.css" id="personal-account">
    <link rel="stylesheet" href="inc/assets/css/login<?= $dark ?>.css" id="login">
    <link rel="stylesheet" href="inc/assets/css/registration<?= $dark ?>.css" id="registration">
    <link rel="stylesheet" href="inc/assets/css/gallery<?= $dark ?>.css" id="gallery">
    <link rel="stylesheet" href="inc/assets/css/terms<?= $dark ?>.css" id="terms">

    
    <link rel="stylesheet" href="inc/assets/css/admin-styles.css">
    <link rel="stylesheet" href="inc/assets/css/core.min.css">
    <link rel="stylesheet" href="inc/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="inc/assets/css/icomoon.css">
    <link rel="stylesheet" href="inc/assets/css/themes_by_abc.css">
    <link rel="stylesheet" href="inc/assets/css/main-register.css">
    <link rel="stylesheet" href="inc/assets/css/carousel.css">


    <link rel="stylesheet" href="inc/assets/css/account.css">
    <link rel="stylesheet" href="inc/assets/css/active-sets.css">
    <link rel="stylesheet" href="inc/assets/css/how-it-works-h1.css">
    <link rel="stylesheet" href="inc/assets/css/how-it-works-steps.css">
    <link rel="stylesheet" href="inc/assets/css/how-it-works-about-us.css">
    <link rel="stylesheet" href="inc/assets/css/rate-sets.css">
    <link rel="stylesheet" href="inc/assets/css/trade-error-message.css">
    <link rel="stylesheet" href="inc/assets/css/chosen.css">
    <link rel="stylesheet" href="inc/assets/css/rate-slider.css">
    <link rel="stylesheet" href="inc/assets/css/cookie-modal.css">
    <link rel="stylesheet" href="inc/assets/css/main-slider.css">
    <?php if (str_contains($routes[1], 'personal-account.php')) { ?>
        <link rel="stylesheet" href="https://unpkg.com/bootstrap@4/dist/css/bootstrap.min.css" crossorigin="anonymous">
	<link rel="stylesheet" href="inc/assets/css/cropper.css">
	<style>
		.label {
			cursor: pointer;
		}

		.progress {
			display: none;
			margin-bottom: 1rem;
		}

		.alert {
			display: none;
		}

		.img-container img {
			max-width: 100%;
		}
	</style>
    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css"/>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZJLJB7697L"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-ZJLJB7697L');
    </script>
    <!-- Event snippet for Посещение сайта conversion page -->
    <script>
      gtag('event', 'conversion', {'send_to': 'AW-11123097014/LjYGCJGh05MYELb787cp'});
    </script>
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '789623945891341');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=789623945891341&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>

<body class="body">

    <!-- Header
   ================================================== -->
    <header class="header">
        <div class="header__inner">

            <a href="/">
                <div class="header__logo"></div>
            </a>
            <div class="header-search-block">
                <div class="header-menu">
                    <a href="../index.php">
                        <?= $fs['Gallery'] ?>
                    </a>
                    <a href="../trade.php" class="active">
                        Sell Wallpapers
                    </a>
                    <a href="../how-it-works.php" class="active">
                        <?= $fs['How it works'] ?>
                    </a>
                    <a href="faq.php">
                        <?= $fs['FAQ'] ?>
                    </a>
                </div>
            </div>


            <div class="header__authorization">
                <?php
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
                if (isLogin()) {
                    if ($_SESSION['user_data']['nickname'] == '') {
                        echo "<div class='header__authorization-login-ico'><a href='./profile.php'><img src='/inc/assets/img/profile.svg'></a></div>";
                    } else {
                        echo "<div class='header__authorization-login-ico'><a href='./login.php'><img class='header__authorization-login-ico' src='" . $avatar . "'></a></div>";
                    }
                    echo "<div class='header__authorization-login'>" . $_SESSION['user_data']['nickname'] . "<p id='header-balance' class='balance'>" . $fs['balance'] . ": <span id='header-balance-span'>" . $_SESSION['user_data']['balance'] . "</span> ". $fs['main_currency'] ."</p>";
                    ?>
                    <div class="dropdown-menu-header">
                        <a class="dropdown-menu-items-first" href="/personal-account.php">
                            Personal Account
                        </a>
                        <a class="dropdown-menu-items" href="account-balance.php">
                            <?= $fs['balance'] ?>
                        </a>
                        <a class="dropdown-menu-items" href="transfer-money.php">
                            <?= $fs['Transfer money'] ?>
                        </a>
                        <a class="dropdown-menu-items-first" href="/profile.php">
                            <?= $fs['Profile'] ?>
                        </a>
                        <a class="dropdown-menu-items" href="/change-password.php">
                            <?= $fs['Change password'] ?>
                        </a>
                        <a class="dropdown-menu-items" href="/purchased-wallpapers.php">
                            Purchased Wallpapers
                        </a>
                        <a class="dropdown-menu-items-last" href="logout.php">
                            <?= $fs['Log_out'] ?>
                        </a>
                    </div>
                    <?php
                    echo "</div>";
                } else {
                    echo ("<div class='header__authorization-registration'><a href='./login.php' class='header__button-register'>" . $fs['Log in'] . "</a></div>");
                }
                ?>
                <div class="header-night-mode" id="switchMode">
                </div>
                <div class="header__lang-mode">
                    <div class="header__lang-img"></div>
                    <form method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>" class="form-language">
                        <select name="cur_ln_id" onchange="this.form.submit()" class="language-select">
                            <?php foreach ($ar_lns as $k => $v) {
                                if (isset($cur_ln_id) && $cur_ln_id == $v['lang_id']) {
                                    $selected = ' selected';
                                    $lang_abbr_current = strtoupper($v['lang_name']);
                                } else {
                                    $selected = '';
                                }
                                ?>
                                <option value="<?= $v['lang_id'] ?>" <?= $selected ?>>
                                    <?= strtoupper($v['lang_name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                        <input type="hidden" name="action" value="set_lang">
                    </form>
                </div>
                <div class="menu-btn">
                    <span></span>
                    <span></span>
                </div>
                <div class="menu" id="burger-menu">
                <a class="dropdown-menu-items" href="../index.php"><?= $fs['Gallery'] ?></a>
                    <a class="dropdown-menu-items" href="../trade.php">Sell Wallpapers</a>
                    <?php
                        if (/*isLogin()*/true) {
                    ?>
                            <a class="dropdown-menu-items" href="/personal-account.php">Personal Account</a>
                            <a class="dropdown-menu-items" href="/purchased-wallpapers.php">Purchased Wallpapers</a>
                            <a class="dropdown-menu-items" href="account-balance.php"><?= $fs['balance'] ?></a>
                            <a class="dropdown-menu-items" href="transfer-money.php"><?= $fs['Transfer money'] ?></a>
                            <a class="dropdown-menu-items" href="/profile.php"><?= $fs['Profile'] ?></a>
                            <!--<a class="dropdown-menu-items" href="purchased-images.php"><?= $fs['Purchased images'] ?></a>-->
                    <?php
                        }
                    ?>
                    <!-- <a class="dropdown-menu-items" href="../rate.php"><?= $fs['Rate_photo'] ?></a> -->
                    <?php
                        if (/*isLogin()*/true) {
                    ?>
                            <a class="dropdown-menu-items" href="/change-password.php"><?= $fs['Change password'] ?></a>
                    <?php
                        } 
                    ?>
                    <!-- <a class="dropdown-menu-items" href="../trade.php?tutorial=1"><?= 'Tutorial' ?></a> -->
                    <a class="dropdown-menu-items-first" href="../how-it-works.php"><?= $fs['How it works'] ?></a>
                    <a class="dropdown-menu-items" href="faq.php" class="mobile-menu"><?= $fs['FAQ'] ?></a>
                    <?php
                        if (isLogin()) {
                    ?>
                            <a class="dropdown-menu-items-last" href="logout.php"><?= $fs['Log_out'] ?></a>
                    <?php
                        } else {
                    ?>
                            <a class="dropdown-menu-items-last" href="login.php"><?= $fs['Log in'] ?></a>
                    <?php
                        }
                    ?>
                </div>

            </div>
            <script>
                let current_url = window.location.href.split('?');
                let url_not_get = current_url[0];
                window.history.pushState(null, '', url_not_get + '<?='?lang=' . $lang_abbr_current ?>');
            </script>

    </header>
    <script src="inc/js/switchmode.js"></script>