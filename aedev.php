<?php

/*
 * https://github.com/akalankauk
 * PHP Files Locker
 */


if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
    //If the session is not started yet, it will be started
    @session_start();
}

function lock($password = "", $skin = 2, $options = array()) {
    global $tm;

    $locked = 1;
    $tries = 30;
    $page = "login";

    if (array_key_exists('skin', $options) && intval($options['skin']) > 1) {
        $skin = $options['skin'];
    }

    if (array_key_exists('password', $options)) {
        $password = $options['password'];
    }

    //If there is set a max num of tries
    if (array_key_exists('tries', $options) && intval($options['tries']) > 1) {
        $tries = $options['tries'];
    }


    if (array_key_exists('bypass', $options) && is_array($options['bypass']) && in_array($_SERVER['REMOTE_ADDR'], $options['bypass'])) {
        $_SESSION['easylock'] = $password;
        $locked = 0;
    }


    if (array_key_exists('deny', $options) && is_array($options['deny']) && in_array($_SERVER['REMOTE_ADDR'], $options['deny'])) {
        $_SESSION['easylock'] = $_POST['password'] = false;
        $_SESSION['tries'] = $tries + 1;
        $page = "block";
    }


    if (isset($_SESSION['tries']) && $_SESSION['tries'] > $tries) {
        //If the user exceed the maximum number of tries
        $_SESSION['easylock'] = $_POST['password'] = false;
        $page = "block";
    }


    //If the user typed the password before, and there's an active session
    if (isset($_SESSION['easylock']) && $_SESSION['easylock'] == $password && $page != "block") {
        $locked = 0;
    }
    //If the user submited the password form
    elseif (isset($_POST['password']) && $page != "block") {
        //If the md5 mode is active
        if (array_key_exists('md5', $options) && $options['md5']) {
            $_POST['password'] = md5($_POST['password']);
        }

        if ($_POST['password'] == $password) {
            //If the user typed the password correctly
            $_SESSION['easylock'] = $_POST['password'];
            $_SESSION['tries'] = 0;
            $locked = 0;
        } else {
            if (isset($_SESSION['tries'])) {
                $_SESSION['tries'] ++;
            } else {
                $_SESSION['tries'] = 1;
            }
            $page = "wrong";
        }
    }
    if ($locked) {
        $err = "";
        if ($page == "wrong" || $page == "block") {
            //Class name for input and message
            $err = " wrong";
        }
        //Skin data (CSS)
        $form = $header = $footer = $head = $formheader = $bg = $style = '';
        $favicon = '<link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAASpSURBVHja7JhNaFxVFIC/e997ySSvmUwmk2kDSTojwUz6GkrBunGjuGgrUgVBsMWVGwUXrrSoKLpUcFXcKG6EqK0/VHFZqIh/SAv9IaVRmmkVQojGplOdn3d/XHQyfZlJm5nONBbpgbc499133vfOueeec5+w1hKVw9PTtCDDwGPAHmACGAAcoABcBL4FPgNONWvwyf37V+kutyYJ4CDwLNC/xv0UkAUeBF4GvgZeAc62+iJ5C3C7gJ+Al24AVy8OsA/4GXiu1Ze16sGHgC/WALPAJeA3QAFDwDjQHZkTA96t3nvzdgDmgI/r4AxwGDgEnASKEbv3AAeA54Fk5Jk3gHngvU6H+BCQjuh/Ao8DTwHfReCoenEWeB24D/i+ztbb1Q/uGOAB4OGIfrWavV818ewc8AjwY2SsH3i1k4D1i/tg1WvNyjLwDHAlMvYEMNkJwO3A/RH9LPB+dIIxBq11w1UnM8AHdUmztxNJsgvwIvrnQHlF0Vqzqa+PLcPDSCmx1iKE4K+lJf5YXETKVT74BHghoj8AvNMSoDEGAYjrhuvD8EMtE5QiMTBALgjo6uoiWpU2Dw+Tv3CBS/k8juMghAA4B/wOjFSnjVejaKy1mEavN4Z4cvt2enw/GqJE3ZT5Fc8NplLkggDXdVFKNYR4azbL1mz22kZ5DX4ZuByxFQdcay1SSoZHRtb3YGpoiF7f59yZM/x99SqO64rVW7I1WmtGMxm2ZrMYazHGNBi21tYg++Jxzs/MoLVGShkt/tIaIxCC8YkJNm/Zsr4HwzAkFosxOTWFv2kTWmustbVLG8NY1TNaa+wacFFRSpEcHCQXBDiOgzGmZssYg5CSiclJhtJpKpVKc0mite6NxWKxyampy+dnZrrLpRJCCIwxjIyNxcey2ZhWqqfZPUYpxUAyaXLbtvHr7KxjjFlZkzI7Pp5IpdMlpVQI/FP/rKhvt/L5/NPAW0CPEEIbY3ytda2mep53xVqrANFyZyIlYRjGrbUOgBDCeJ63bIyR1Ur0YiaT+XA9wDkgU5sgRMPaakfWsZfPZDLZ9UIc7yTQWslzE4l3oh/cULkL2K64nTTmOA5SylrzcMcAOo6D4zgsLi5SKBToj8cZTKVQSq1ZZTYU0HEcisUiXx49yulTpyiVSvT09LBz504e3beP7u7utrzZ1hoUQmCt5aPpab45fpxyuYyUkmKxyLFjx/j0yBGEEA1734YBep7HL7OznDl9Gt/3kVIihMBxHHzf5+SJE8zNzeG6bucAV764mct1Xebn51FKNXhJCEGlUmFhYQHXdZu2ue4aXKujuFmIu7q6bhhCKSWe51Eul1uye1PApaWllor/UDpNIpGgUCjgeddPBmGlQiqVIplMsrCwcMvZ3NYaNMbQ39/P7r176e3trUEYY+iLx9m9Zw++77e11bRdScIwJAgCgiAgDMPa2I4dO7g3l6uN/aelTlc740jLAkJ0pJp0phZXW/hI9tztZv5f3YwQAqUUpVIJgFKpdG3zvlMAlVJkstladVFKMTo6ilLqzgHM5XIEQRA9ut42wCt1f0SbhuwA0HIzgK+tnIurv3g3KlmL1T+yq+TfAQBtouoCd5kXNwAAAABJRU5ErkJggg==">';
        if ($skin == 1) {
            //AE DEV PHP CSS Here
            $font = "Iceland"; //Google Fonts
            $bg = 'background-color: #000000;';
            $head = '<link href="http://fonts.googleapis.com/css?family=' . $font . ':500,300,400" rel="stylesheet" type="text/css">';
            $font = "'" . $font . "'";

            $style.='body{color:#00DC00;font-family:' . $font . ',sans-serif;}*{box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;}';
            $style.='p{font-size:40px;text-align:center;margin-top:80px;display:block;}';
            $style.='label,.error{display:none;}';
            $style.='#password{-webkit-appearance:none;display:inline-block;margin:auto;width:180px;background:black;color:#00DC00;border:none;font-family:' . $font . ',sans-serif;outline:none!important;font-size:23px;}';
            $style.='.box{display:block;width:200px;text-align;margin:auto;font-size:20px;}';
            $style.='.submit{display:none;}';
            $style.=".box:before{content:'[';}.box:after{content:']';}";
            $style.='.wrong.box{color:red;}';

            $header = '<p>Access denied! By AE DEV</p>';
        }
        //Skin data
        $sub1 = '';
        $sub2 = ' autofocus';
        if ($page == "block") {
            $sub1 = $sub2 = ' disabled';
        }
        $form = '<form action="" method="POST" class="box' . $err . '">';
        $form.=$formheader;
        $form.='<label for="password">Password:</label>';
        $form.='<input type="password" name="password" id="password" placeholder="Type your password..." ' . $sub2 . '>';
        if ($page == "block") {
            $form.='<span class="error">You are blocked!</span>';
        } else {
            $form.='<span class="error">Wrong password! Please try again!</span>';
        }
        $form.='<input type="submit" value="Unlock" class="submit"' . $sub1 . '>';
        $form.='</form>';

        $style = "body{ $bg background-attachment:fixed;background-position:center top;}" . $style;
        $body = $header . $form . $footer;
        //Form Template
        echo '<!DOCTYPE html><html><head>' . $favicon . '<title>Page Locked</title><meta name="viewport" content="width=device-width, user-scalable=no"><style>' . $style . '</style>' . $head . '</head><body>' . $body . '</body></html>';
        exit();
    }
}
