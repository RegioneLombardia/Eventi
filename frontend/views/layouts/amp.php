<?php

use yii\helpers\Html;

/**
 * @var $content
 */
$this->beginPage();
$url = \Yii::$app->params['platform']['frontendUrl'];
?>
<!doctype html>
<html amp lang="en">

<head>
    <meta charset="utf-8">
    <?php if(\Yii::$app->params['dev-mode']){ ?>
        <meta name="robots" content="noindex">
        <meta name="robots" content="nofollow">
    <?php } ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="shortcut icon" href="../favicon.png">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>
    <script async custom-element="amp-font" src="https://cdn.ampproject.org/v0/amp-font-0.1.js"></script>
    <?php if (!\Yii::$app->user->isGuest) {
        $profile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
        $nomeCognome = $profile->nomeCognome;
    ?>
        <script async custom-element="amp-mega-menu" src="https://cdn.ampproject.org/v0/amp-mega-menu-0.1.js"></script>
    <?php } ?>
    <!-- <script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script> -->

    <script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>
    <script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
    <script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>

    <?php if (\Yii::$app->controller->action->id == 'event') { ?>
        <script async custom-element="amp-date-countdown" src="https://cdn.ampproject.org/v0/amp-date-countdown-0.1.js"></script>
        <script async custom-element="amp-animation" src="https://cdn.ampproject.org/v0/amp-animation-0.1.js"></script>
        <script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
    <?php } ?>
    <?php if (in_array(\Yii::$app->controller->id . '/' . \Yii::$app->controller->action->id, ['amp/palazzo-lombardia', 'amp/grattacielo-pirelli'])) { ?>


        <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
        <script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
        <script async custom-element="amp-selector" src="https://cdn.ampproject.org/v0/amp-selector-0.1.js"></script>
        <script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
        <script async custom-element="amp-base-carousel" src="https://cdn.ampproject.org/v0/amp-base-carousel-0.1.js"></script>
    <?php } ?>

    <title><?= $this->title ?></title>
    <?php $currentUrl = \Yii::$app->request->absoluteUrl;
    $currentUrl = str_replace('/amp', '', $currentUrl);
    ?>
    <link rel="canonical" href="<?= $currentUrl ?>">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            animation: -amp-start 8s steps(1, end) 0s 1 normal both
        }

        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-moz-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-ms-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-o-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }
    </style>
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>
    <style amp-custom>
        /*----------------Titillium Web -------------------*/
        @font-face {
            font-family: 'Titillium Web';
            src: local('Titillium Web'), url(../../amp-resources/font/Titillium_Web/TitilliumWeb-Bold.ttf) format('truetype');

            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: 'Titillium Web';
            src: local('Titillium Web'), url(../../amp-resources/font/Titillium_Web/TitilliumWeb-Light.ttf) format('truetype');
            font-weight: 300;
            font-style: normal;
        }

        @font-face {
            font-family: 'Titillium Web';
            font-weight: 400;
            font-style: normal;
            src: local('Titillium Web'), url(../../amp-resources/font/Titillium_Web/TitilliumWeb-Regular.ttf) format('truetype');

        }

        @font-face {
            font-family: 'Titillium Web';
            src: local('Titillium Web'), url(../../amp-resources/font/Titillium_Web/TitilliumWeb-SemiBold.ttf) format('truetype');
            font-weight: 600;
            font-style: normal;
        }
        @font-face {
            font-family: 'Red Hat Display';
            src: local('Red Hat Display'), url(../../amp-resources/font/red_hat_display/RedHatDisplay-Bold.ttf) format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        @font-face {
            font-family: 'Red Hat Display';
            src: local('Red Hat Display'), url(../../amp-resources/font/red_hat_display/RedHatDisplay-Regular.ttf) format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Red Hat Display';
            src: local('Red Hat Display'), url(../../amp-resources/font/red_hat_display/RedHatDisplay-Light.ttf) format('truetype');
            font-weight: 300;
            font-style: normal;
        }

        body {
            background-color: #fff;
            font-family: 'Titillium Web', sans-serif;
            font-size: 16px;
            line-height: 1.5;
        }

        .pagination {
            flex-grow: 1;
            width: 100%;
            padding: 0 15px;
            margin: 56px auto;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
        }

        .pagination>.disabled>span,
        .pagination>.disabled>span:hover,
        .pagination>.disabled>span:focus,
        .pagination>.disabled>a,
        .pagination>.disabled>a:hover,
        .pagination>.disabled>a:focus {
            background: transparent;
            opacity: 0.5;
            border: 1px solid transparent;
        }

        .pagination>li:first-child>a,
        .pagination>li:first-child>span {
            margin-left: 0;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .pagination>li {
            display: inline;
        }

        .pagination>li>a,
        .pagination>li>span {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            height: 48px;
            min-width: 48px;
            border-radius: 4px;
            border: 1px solid transparent;
            font-size: 16px;
            font-weight: 600;
            color: #297A38;
            background-color: transparent;
            text-decoration: none;
        }

        .pagination>.active>a,
        .pagination>.active>span,
        .pagination>.active>a:hover,
        .pagination>.active>span:hover,
        .pagination>.active>a:focus,
        .pagination>.active>span:focus {
            background: transparent;
            border-color: #000;
        }

        @media (min-width:992px) {
            body {
                font-size: 18px;
            }
        }

        * {
            /* -webkit-box-sizing: border-box; */
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        a {
            color: #297A38;
        }

        h2:empty,
        h3:empty {
            display: none;
        }

        @media (min-width: 960px) {
            .uk-container {
                padding-left: 40px;
                padding-right: 40px;
            }
        }


        @media (min-width: 640px) {
            .uk-container {
                padding-left: 30px;
                padding-right: 30px;
            }
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0 0 20px 0;
            line-height: 1;
            letter-spacing: -0.8px;
        }

        .uk-position-relative {
            position: relative;
        }

        .uk-position-cover {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
        }

        [class*=uk-width] {
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
        }

        .uk-overlay {
            padding: 30px 30px;
        }

        .uk-position-bottom {
            bottom: 0;
            left: 0;
            right: 0;
        }

        [class*=uk-position-bottom],
        [class*=uk-position-center],
        [class*=uk-position-left],
        [class*=uk-position-right],
        [class*=uk-position-top] {
            position: absolute;
        }

        .row {
            margin-right: -15px;
            margin-left: -15px;
            display: flex;
            flex-wrap: wrap;
            /* max-width: 100%; */
        }

        .row>* {
            padding-right: 15px;
            padding-left: 15px;
            max-width: 100%;
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
        }

        @media (min-width: 992px) {
            .col-md-4 {
                flex: 0 0 auto;
                width: 33.33333333%;
            }

            .col-md-5 {
                flex: 0 0 auto;
                width: 41.66666667%;
            }

            .col-md-7 {
                flex: 0 0 auto;
                width: 58.33333333%;
            }
        }

        .uk-container {
            box-sizing: content-box;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 15px;
            padding-right: 15px;
        }

        #main-content {
            position: relative;
        }

        /* MENU */
        #megamenu-slim-eventi {
            background-color: #000000;
            position: fixed;
            bottom: 0;
            width: 100%;
            left: 0;
            right: 0;
            z-index: 2;
            font-family: 'Titillium Web', sans-serif;
            top: auto;
        }

        #megamenu-slim-eventi .content-top-megamenu {
            border-top: 1px solid #ffffff30;

            background-color: #000000;
            color: #FFF;
            position: relative;
            min-height: 40px;
        }

        .right-megamenu {
            position: absolute;
            right: 30px;
            top: 15px;
        }

        #megamenu-slim-eventi .content-top-megamenu .icon {
            font-size: 24px;
        }


        .accordion-menu * {
            background-color: transparent;
            border: none;
        }

        .content-accordion {
            padding: 15px 30px;
        }

        #hamburger {
            color: #fff;
            padding: 23px 30px;
        }


        #megamenu-slim-eventi p {
            margin: 0;
        }

        #megamenu-slim-eventi a,
        #megamenu-slim-eventi .link {
            color: #FFF;
            text-decoration: underline;
            font-weight: normal;
            padding-right: 0;
        }

        #megamenu-slim-eventi a:hover {
            text-decoration: none;
        }

        #megamenu-slim-eventi .content-top-megamenu {
            border-top: 1px solid #ffffff30;
            color: #FFF;
            background-color: #000000;
            color: #FFF;
            position: relative;
            min-height: 40px;
        }


        #megamenu-slim-eventi .content-top-megamenu .left-megamenu {
            font-size: 18px;
            color: #FFF;

        }

        #megamenu-slim-eventi .content-top-megamenu .left-megamenu .content-left {
            max-width: calc(100% - 150px);
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            display: flex;
            align-items: center;

        }

        #megamenu-slim-eventi .content-top-megamenu .left-megamenu .title-page-footer {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            font-family: 'Titillium Web', sans-serif;
            font-size: 18px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            white-space: nowrap;
            margin-left: 10px;
        }

        @media (max-width: 568px) {
            #megamenu-slim-eventi .content-top-megamenu .left-megamenu .title-page-footer .uk-margin-small-right {
                display: none;
            }
        }

        #megamenu-slim-eventi .content-top-megamenu .left-megamenu .title-page-footer .material-icons {
            font-size: 18px;
        }

        #megamenu-slim-eventi .content-top-megamenu .btn-megamenu {
            font-size: 18px;
            letter-spacing: 2px;
            color: #FFF;
            text-transform: uppercase;
            font-weight: 600;
            display: flex;
            align-items: center;
            text-decoration: none;
            line-height: 1.3;
        }


        #megamenu-slim-eventi .content-top-megamenu .btn-megamenu:hover {
            text-decoration: underline;
        }


        #megamenu-slim-eventi .content-top-megamenu .right-megamenu {
            display: flex;
            align-items: center;
        }


        #megamenu-slim-eventi .content-top-megamenu .right-megamenu .logo-regione img {
            height: 40px;
        }

        #megamenu-slim-eventi .current-page {
            border-right: 1px solid #6F6F6F;
            margin-right: 24px;
            padding-right: 24px;
            line-height: 1.3;
            font-size: 18px;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
        }


        #megamenu-slim-eventi .content-bottom-megamenu-slim .hedear-content-eventi {
            display: flex;
            flex-wrap: wrap;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .hedear-content-eventi .titoletto {
            font-weight: bold;
            text-transform: uppercase;
            margin-right: auto;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .menu-bottom .link-menu {
            display: flex;
            flex-wrap: wrap;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .menu-bottom .link-menu a {
            margin-bottom: 12px;
            border: none;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .footer-megamenu {
            display: flex;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .footer-megamenu .copyleft {
            margin-right: auto;
            background-color: transparent;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .footer-megamenu .copyleft p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            padding: 0;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .footer-megamenu .link-credits {
            width: 100%;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim .footer-megamenu .link-credits h4 {
            letter-spacing: 0;
            line-height: 1.3;
        }

        @media (min-width: 1060px) {
            #megamenu-slim-eventi .content-bottom-megamenu-slim .footer-megamenu .link-credits h4 {
                margin-top: -25px;
                text-align: right;
            }
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim #linkCredits .list-inline {
            margin: 12px 0;
            padding-left: 0;
            list-style: none;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim #linkCredits .list-inline li {
            padding-left: 0;
            padding-right: 12px;
            display: inline-block;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim #linkCredits .card {
            background: transparent;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim #linkCredits .card-body {
            padding: 0;
        }

        #megamenu-slim-eventi .content-bottom-megamenu-slim #linkCredits .nav-link {
            padding: 0;
        }

        .navbar-new-eventi {
            position: fixed;
            right: 0;
            top: 0;
            z-index: 99;
            padding: 10px 30px;
        }

        .navbar-new-eventi .uk-navbar-right .link-login>a,
        .navbar-new-eventi .uk-navbar-right .link-login>span {
            border-width: 2px;
            border-style: solid;
            border-color: #29b973;
            border-radius: 100em;
            color: #FFF;
            height: auto;
            text-transform: initial;
            font-weight: bold;
            font-size: 1em;
            max-width: 320px;
            padding: 0 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            background-color: #000;
        }

        @media (max-width: 568px) {
            .navbar-new-eventi .uk-navbar-right .link-login .username {
                display: none;
            }
        }

        .navbar-new-eventi .uk-navbar-right .link-login>a:hover,
        .navbar-new-eventi .uk-navbar-right .link-login>span:hover {
            background-color: #29b973;
            color: #000;
        }

        .navbar-new-eventi .uk-navbar-right .link-login>a .material-icons,
        .navbar-new-eventi .uk-navbar-right .link-login>span .material-icons {
            font-size: 1.5em;
            line-height: 1.5;
        }

        .navbar-new-eventi .uk-navbar-right .link-login>a span+span,
        .navbar-new-eventi .uk-navbar-right .link-login>span span+span {
            margin-left: 6px;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .navbar-new-eventi amp-mega-menu nav {
            background: none;
        }

        nav>ul {
            padding-left: 0;
        }

        .dropdownmenu {
            background-color: #000;
            padding: 15px;
            min-width: 230px;
            list-style: none;
            position: absolute;
        }

        .dropdownmenu a {
            color: #fff;
            font-weight: bold;
            font-size: 1em;
            text-decoration: none;
            border-radius: 100em;
            padding: 12px;
            display: block;
        }

        .dropdownmenu a:hover {
            background-color: #29b973;
            color: #000;
        }

        .slider-piani {
            height: 100vh;
            position: relative;
            margin-right: -30px;
        }

        .slider-piani .content-piano {
            color: #fff;
            z-index: 1;
            position: relative;
            width: 100%;
            text-align: center;
            max-width: 100%;
            height: 100%;
            display: flex;
            align-items: flex-start;
        }

        .img-piano img {
            object-fit: cover;
        }

        .piano-palazzo {
            background-color: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            display: flex;
            position: relative;
            align-items: center;
            justify-content: center;
        }

        .display-piano {
            text-align: center;
            width: 100%;
            margin-bottom: 70px;
            padding: 0 40px;
        }

        .display-piano h2 {
            color: #FFF;
            display: flex;
            flex-direction: column;
        }

        .display-piano .label-piano {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            margin-bottom: 0;
            font-size: 18px;
        }

        .display-piano .nome-piano {
            font-weight: bold;
            line-height: 0.8;
            margin-top: 0;
            overflow: hidden;
            font-size: 60px;
        }

        @media (min-width: 567px) {
            .display-piano .nome-piano {
                font-size: 100px;
            }
        }

        .display-piano .numero-piano {
            font-weight: bold;
            line-height: 0.8;
            margin-top: 0;
            overflow: hidden;
            font-size: 90px;
        }

        @media (max-height: 800px) {
            .display-piano .numero-piano {
                font-size: 80px;
            }
        }

        @media (min-width: 768px) {
            .display-piano .numero-piano {
                font-size: 160px;
            }
        }

        .display-piano .numero-piano::before,
        .display-piano .numero-piano::after {
            background-color: #FFF;
            content: "";
            display: inline-block;
            height: 2px;
            position: relative;
            vertical-align: middle;
            width: 15%;
        }

        .display-piano .numero-piano::before {
            right: 0.1em;
            margin-left: -50%;
        }

        .display-piano .numero-piano::after {
            left: 0.1em;
            margin-right: -50%;
        }

        .link-sale {
            margin-top: 5px;
        }

        .link-sale .content-link {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .link-sale .el-item {
            margin-top: 12px;
            padding: 0 20px;
        }

        .link-sale a {
            color: #fad87a;
            text-transform: uppercase;
            font-size: 16px;
            text-shadow: 1px 1px 1px black;
            text-decoration: none;
        }

        .link-sale a:hover {
            text-decoration: underline;
        }

        .link-sale a .icon {
            font-size: 16px;
            color: #fff;
            transform: rotate(-90deg);
            line-height: 0.8;
            padding-right: 4px;
        }

        .my-arrow {
            position: fixed;
            left: 0;
            width: 100%;
            background-color: transparent;
            border: none;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-prev.my-arrow {
            top: 70px;
        }

        .carousel-next.my-arrow {
            bottom: 100px;
        }

        .text-arrow {
            font-size: 12px;
            font-family: 'Titillium Web', sans-serif;
            text-transform: uppercase;
            margin-left: 6px;
        }

        .amp-esplora-eventi {
            font-family: 'Red Hat Display', sans-serif;
        }

        .hero-banner {
            min-height: 80vh;
            margin-top: 0px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            background-color: hsl(212, 42%, 15%);
            padding: 0;
        }

        .hero-banner .video-bg {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .hero-banner .video-bg .img-sfondo {
            right: 0;
            bottom: 0;
            left: 0;
            min-width: 100%;
            min-height: 100%;
            max-width: inherit;
        }

        .img-sfondo img {
            object-fit: cover;
        }

        .hero-banner>.uk-container {
            display: flex;
            align-items: center;
            height: 100%;
            padding-left: 40px;
            padding-right: 40px;
        }

        .hero-banner>.uk-container img.img-event {
            position: absolute;
            left: 0;
            min-height: 100%;
            min-width: 100%;
            object-fit: cover;
            top: 0;
        }

        .hero-banner>.uk-container img.img-event.img-desktop {
            display: none;
        }

        @media (min-width: 768px) {
            .hero-banner>.uk-container img.img-event.img-desktop {
                display: block;
            }
        }

        @media (min-width: 768px) {
            .hero-banner>.uk-container img.img-event.img-mobile {
                display: none;
            }
        }

        .hero-banner .uk-container {
            width: 100%;
        }

        .hero-banner .uk-container .uk-container {
            width: inherit;
            margin: 0;
        }

        @media (min-width: 960px) {
            .hero-banner .uk-container {
                max-width: 100%;
            }
        }

        .hero-banner .uk-container .content-hero-title {
            position: relative;
            z-index: 1;
            padding-bottom: 90px;
            padding-top: 100px;
            display: flex;
            align-items: flex-end;
        }

        .hero-banner .uk-container .content-hero-title h1 {
            font-size: 2.8em;
            font-weight: bold;
            color: #FFF;
            letter-spacing: -0.05em;
            overflow-wrap: break-word;
            hyphens: auto;
            line-height: 1;
            margin: 0 0 20px 0;
        }

        @media (min-width: 992px) {
            .hero-banner .uk-container .content-hero-title h1 {
                flex-basis: 60%;
            }
        }

        @media (min-width: 576px) {
            .hero-banner .uk-container .content-hero-title h1 {
                font-size: 4.7vw;
                hyphens: inherit;
            }
        }

        @media (min-width: 1400px) {
            .hero-banner .uk-container .content-hero-title h1 {
                font-size: 4.5em;
            }
        }

        .hero-banner:after {
            content: '';
            position: absolute;
            width: 100%;
            min-height: 100vh;
            height: 100%;
            background-color: hsla(212, 42%, 15%, 0.7);
            top: 0;
        }

        .hero-banner.gradient-verde-blu:after {
            background: linear-gradient(-180deg, rgba(16, 51, 102, 0.5) 0%, rgba(41, 122, 56, 0.9) 100%);
        }

        .hero-banner.gradient-viola-blu::after {
            background: linear-gradient(-180deg, rgba(45, 111, 170, 0.5) 0%, rgba(70, 53, 111, 0.9) 100%);

        }

        .hero-banner.gradient-arancio-rosa::after {
            background: linear-gradient(-180deg, rgba(102, 16, 83, 0.5) 0%, rgba(195, 75, 54, 0.9) 100%);

        }

        .section-today-streaming .title-intro-streaming {
            background-color: #961111;
        }

        .section-today-streaming .uk-container {
            max-width: 100%;
            padding: 0 40px;
        }

        .section-today-streaming .title-intro-streaming h2 {
            color: #FFF;
            text-transform: uppercase;
            font-size: 30px;
            margin: 0;
            font-weight: bold;
            padding: 18px 0;
            display: flex;
        }

        .section-today-streaming .title-intro-streaming h2 .material-icons {
            font-size: 30px;
            margin-right: 10px;
        }

        .section-today-streaming .wrap-playlist {
            background-color: #1A1A1A;
            color: #FFF;
        }

        .section-today-streaming .wrap-playlist h1,
        .section-today-streaming .wrap-playlist h2,
        .section-today-streaming .wrap-playlist h3,
        .section-today-streaming .wrap-playlist h4,
        .section-today-streaming .wrap-playlist h5,
        .section-today-streaming .wrap-playlist h6,
        .section-today-streaming .wrap-playlist p {
            color: #FFF;
        }

        .section-today-streaming .wrap-playlist .info-streaming {
            padding: 50px 0;
        }

        .section-today-streaming .wrap-playlist .info-streaming h3 {
            font-size: 40px;
        }

        .section-today-streaming .wrap-playlist .info-streaming h4 {
            margin-top: 0;
            font-size: 25px;
            margin-bottom: 40px;
            font-weight: normal;
        }

        .section-today-streaming .wrap-playlist .info-streaming p {
            font-size: 20px;
        }

        .section-today-streaming .wrap-playlist .streaming-video-container p {
            padding: 70px 0;
        }

        .section-today-streaming .wrap-playlist .streaming-bg {
            position: relative;
            padding: 0;
        }

        .section-today-streaming .wrap-playlist .streaming-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, #1a1a1a 40px, rgba(26, 26, 26, 0) 100%);
        }

        .section-today-streaming .wrap-playlist .streaming-bg .streaming-video-container p {
            padding: 30% 0;
        }

        .section-today-streaming .wrap-playlist .streaming-bg .streaming-video-container .mdi {
            font-size: 180px;
            position: absolute;
            z-index: 9;
            top: 50%;
            line-height: 1;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }

        .section-today-streaming .wrap-playlist .wrap-video-thumbs {
            padding-bottom: 10px;
            margin: 0;
            margin-top: 10px;
        }

        .section-today-streaming .wrap-playlist .wrap-video-thumbs.row>* {
            padding-left: 15px;
            padding-right: 15px;
        }

        .section-today-streaming .wrap-playlist .wrap-video-thumbs a.wrap-video {
            display: block;
            background-color: #000;
            color: #FFF;
        }

        .section-today-streaming .wrap-playlist .wrap-video-thumbs a.wrap-video .uk-overlay-primary {
            background: rgba(34, 34, 34, 0.5);
        }

        .section-today-streaming .wrap-playlist .wrap-video-thumbs a.wrap-video:hover {
            opacity: 0.8;
        }

        .section-today-streaming .wrap-playlist .wrap-video-thumbs a.wrap-video h4 {
            font-size: 30px;
            line-height: 1.3;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .section-today-streaming .wrap-playlist .wrap-video-thumbs a.wrap-video p {
            margin-top: 0;
            font-size: 20px;
            margin-bottom: 0;
        }

        .btn-live-streaming {
            background-color: #961111;
            color: #ffffff;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 0;
            font-weight: normal;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            background-image: none;
            border: 1px solid transparent;
            padding: 6px 12px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            text-transform: uppercase;
            text-decoration: none;
        }

        .btn-live-streaming:hover {
            background-color: #710d0d;
            color: #ffffff;
        }

        #coming-next {
            margin-top: 60px;
        }

        .container-elenco-eventi {
            padding-bottom: 40px;
        }

        .container-elenco-eventi .lista-eventi {
            display: flex;
            flex-wrap: wrap;
            margin-left: -15px;
            margin-right: -15px;
        }

        .container-elenco-eventi .lista-eventi .empty {
            text-align: center;
            width: 100%;
            padding: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container-elenco-eventi .lista-eventi div[data-key],
        .container-elenco-eventi .lista-eventi .data-key {
            position: relative;
            padding: 15px;
            -webkit-box-flex: 0;
            -ms-flex: 0 0 33.333333%;
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }

        @media (max-width: 991px) {

            .container-elenco-eventi .lista-eventi div[data-key],
            .container-elenco-eventi .lista-eventi .data-key {
                -ms-flex: 0 0 50%;
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 767px) {

            .container-elenco-eventi .lista-eventi div[data-key],
            .container-elenco-eventi .lista-eventi .data-key {
                -ms-flex: 0 0 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento {
            display: block;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento:focus,
        .container-elenco-eventi .lista-eventi .data-key .link-evento:focus {
            outline-offset: 2px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento:hover .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento:hover .titolo-evento,
        .container-elenco-eventi .lista-eventi div[data-key] .link-evento:focus .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento:focus .titolo-evento {
            text-decoration: underline;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento {
            height: auto;
            position: relative;
            min-height: 265px;
            overflow: hidden;
        }

        @media (max-width: 767px) {

            .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento,
            .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento {
                min-height: 350px;
            }
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .actions-event,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .actions-event {
            position: absolute;
            right: 10px;
            top: 10px;
            z-index: 9;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .actions-event .btn+.btn,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .actions-event .btn+.btn {
            margin-left: 8px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .actions-event svg,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .actions-event svg {
            width: 24px;
            height: 24px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .actions-event svg.icon-white,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .actions-event svg.icon-white {
            fill: #FFF;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento {
            display: block;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .img-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .img-evento {
            position: absolute;
            top: 0;
            height: 100%;
            width: 100%;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .img-evento img,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .img-evento img {
            object-fit: cover;
            min-width: 100%;
            min-height: 100%;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .badge-webex,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .badge-webex {
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50px;
            position: absolute;
            right: 4%;
            bottom: 4%;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .badge-webex span,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .badge-webex span {
            background: #1fc2fb;
            background: linear-gradient(180deg, #82CF5F 0%, #00BCF5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 4%;
            background: linear-gradient(180deg, rgba(23, 37, 54, 0.5) 0%, rgba(23, 37, 54, 0.9) 64.05%);
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-tag,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-tag {
            position: absolute;
            top: 5%;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-tag .tipologia-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-tag .tipologia-evento {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50px;
            margin-right: 10px;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            padding: 6px 12px;
            line-height: 1;
            display: inline-block;
            margin-bottom: 6px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .triangle,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .triangle {
            position: absolute;
            top: -30px;
            right: -30px;
            width: 60px;
            height: 60px;
            -webkit-transform: rotate(45deg);
            background: #FFE600;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento {
            position: relative;
            display: flex;
            flex-direction: column;
            max-width: 90%;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .tipologia-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .tipologia-evento {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 3px 8px;
            font-size: 14px;
            color: #fff;
            width: fit-content;
            margin-bottom: 6px;
            margin-right: 6px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .badge,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .badge {
            margin-bottom: 10px;
            border-radius: 4px;
            font-size: 14px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .titolo-evento {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 24px;
            line-height: 1.3;
            font-weight: bold;
            letter-spacing: -0.5px;
            color: #FFF;
            margin-bottom: 10px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento:hover span.tipologia-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento:hover span.tipologia-evento,
        .container-elenco-eventi .lista-eventi div[data-key] .link-evento:focus span.tipologia-evento,
        .container-elenco-eventi .lista-eventi .data-key .link-evento:focus span.tipologia-evento {
            opacity: 1;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location {
            display: flex;
            flex-wrap: wrap;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi {
            font-size: 14px;
            color: #fff;
            display: flex;
            align-items: flex-start;
            margin-right: 20px;
            flex-basis: 100%;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi .data-icon,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi .data-icon {
            margin-right: 5px;
            line-height: 1;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi .data-text,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi .data-text {
            display: flex;
            flex-wrap: wrap;
            margin-left: 5px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi .data-text span:first-child,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .data-eventi .data-text span:first-child {
            margin-right: 3px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .location-eventi,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .location-eventi {
            font-size: 14px;
            color: #fff;
            display: flex;
            align-items: flex-start;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .location-eventi .location-icon,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-data-location .location-eventi .location-icon {
            margin-right: 5px;
            line-height: 1;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-badge,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .content-badge {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .btn-download-ticket,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .btn-download-ticket {
            background-color: rgba(0, 0, 0, 0.7);
            width: fit-content;
            line-height: 1;
            padding: 5px;
            color: #fff;
            border-radius: 6px;
            font-size: 24px;
            display: inline-block;
            margin-right: 6px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .badge-accompagnatori,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .badge-accompagnatori {
            background-color: rgba(0, 0, 0, 0.7);
            font-size: 15px;
            color: #FFF;
            padding: 5px;
            border-radius: 6px;
            line-height: 1;
            display: flex;
            align-items: center;
            font-weight: bold;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover .content-info-evento .badge-accompagnatori .mdi,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover .content-info-evento .badge-accompagnatori .mdi {
            font-size: 24px;
            line-height: 1;
            margin-right: 3px;
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover.gradient-verde-blu,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover.gradient-verde-blu {
            background: linear-gradient(-180deg, rgba(16, 51, 102, 0.5) 0%, rgba(41, 122, 56, 0.9) 100%);
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover.gradient-viola-blu,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover.gradient-viola-blu {
            background: linear-gradient(-180deg, rgba(45, 111, 170, 0.5) 0%, rgba(70, 53, 111, 0.9) 100%);
        }

        .container-elenco-eventi .lista-eventi div[data-key] .link-evento .box-evento .content-evento .position-cover.gradient-arancio-rosa,
        .container-elenco-eventi .lista-eventi .data-key .link-evento .box-evento .content-evento .position-cover.gradient-arancio-rosa {
            background: linear-gradient(-180deg, rgba(102, 16, 83, 0.5) 0%, rgba(195, 75, 54, 0.9) 100%);
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti {
            -ms-flex: 0 0 33%;
            flex: 0 0 33%;
            max-width: 33%;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti .badge,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti .badge {
            display: none;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti .box-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti .box-evento {
            min-height: 200px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti .box-evento .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti .box-evento .titolo-evento {
            font-size: 20px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti .box-evento .data-eventi,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti .box-evento .data-eventi {
            font-size: 15px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti:nth-child(-n+4),
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti:nth-child(-n+4) {
            -ms-flex: 0 0 33%;
            flex: 0 0 33%;
            max-width: 33%;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti:nth-child(-n+4) .box-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti:nth-child(-n+4) .box-evento {
            min-height: 200px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti:nth-child(-n+4) .box-evento .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti:nth-child(-n+4) .box-evento .titolo-evento {
            font-size: 20px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-interessanti:nth-child(-n+4) .box-evento .data-eventi,
        .container-elenco-eventi .lista-eventi .data-key.item-interessanti:nth-child(-n+4) .box-evento .data-eventi {
            font-size: 15px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa {
            -ms-flex: 0 0 33%;
            flex: 0 0 33%;
            max-width: 33%;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa .badge,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa .badge {
            display: none;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa .box-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa .box-evento {
            min-height: 265px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa .box-evento .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa .box-evento .titolo-evento {
            font-size: 24px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa .box-evento .data-eventi,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa .box-evento .data-eventi {
            font-size: 16px;
            color: #fff;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa:nth-child(-n+4),
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa:nth-child(-n+4) {
            -ms-flex: 0 0 33%;
            flex: 0 0 33%;
            max-width: 33%;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa:nth-child(-n+4) .box-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa:nth-child(-n+4) .box-evento {
            min-height: 265px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa:nth-child(-n+4) .box-evento .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa:nth-child(-n+4) .box-evento .titolo-evento {
            font-size: 24px;
        }

        .container-elenco-eventi .lista-eventi div[data-key].item-in-attesa:nth-child(-n+4) .box-evento .data-eventi,
        .container-elenco-eventi .lista-eventi .data-key.item-in-attesa:nth-child(-n+4) .box-evento .data-eventi {
            font-size: 16px;
        }

        @media (min-width: 768px) {

            .container-elenco-eventi .lista-eventi div[data-key]:nth-child(-n+4),
            .container-elenco-eventi .lista-eventi .data-key:nth-child(-n+4) {
                -webkit-box-flex: 0;
                -ms-flex: 0 0 50%;
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        .container-elenco-eventi .lista-eventi div[data-key]:nth-child(-n+4) .content-data-location .data-eventi,
        .container-elenco-eventi .lista-eventi .data-key:nth-child(-n+4) .content-data-location .data-eventi {
            font-size: 16px;
            flex-basis: 50%;
        }

        @media (max-width: 1023px) {

            .container-elenco-eventi .lista-eventi div[data-key]:nth-child(-n+4) .content-data-location .data-eventi,
            .container-elenco-eventi .lista-eventi .data-key:nth-child(-n+4) .content-data-location .data-eventi {
                flex-basis: 100%;
            }
        }

        .container-elenco-eventi .lista-eventi div[data-key]:nth-child(-n+4) .content-data-location .location-eventi,
        .container-elenco-eventi .lista-eventi .data-key:nth-child(-n+4) .content-data-location .location-eventi {
            font-size: 16px;
            margin-top: 5px;
        }

        @media (max-width: 1023px) {

            .container-elenco-eventi .lista-eventi div[data-key]:nth-child(-n+4) .content-data-location .location-eventi,
            .container-elenco-eventi .lista-eventi .data-key:nth-child(-n+4) .content-data-location .location-eventi {
                flex-basis: 100%;
            }
        }

        .container-elenco-eventi .lista-eventi div[data-key]:nth-child(-n+4) .box-evento,
        .container-elenco-eventi .lista-eventi .data-key:nth-child(-n+4) .box-evento {
            min-height: 400px;
        }

        .container-elenco-eventi .lista-eventi div[data-key]:nth-child(-n+4) .box-evento .titolo-evento,
        .container-elenco-eventi .lista-eventi .data-key:nth-child(-n+4) .box-evento .titolo-evento {
            font-size: 28px;
        }

        .detail-event {
            font-family: 'Red Hat Display', sans-serif;
        }

        .detail-event .hero-banner .uk-container .content-hero-title {
            flex-basis: 100%;
            flex-wrap: wrap;
        }

        .detail-event .hero-banner .uk-container .content-hero-title>* {
            width: 1px;
            flex: 1;
            min-width: 0;
            flex-basis: 1px;

        }

        @media (min-width:992px) {
            .detail-event .hero-banner .uk-container .content-hero-title>div:first-child {
                padding-right: 30px;
            }
        }


        @media (max-width:991px) {
            .detail-event .hero-banner .uk-container .content-hero-title>* {
                width: 100%;
                flex-basis: 100%;

            }
        }

        .detail-event .hero-banner .uk-container .content-hero-title .luogo-evento,
        .detail-event .hero-banner .uk-container .content-hero-title .data-evento,
        .detail-event .hero-banner .uk-container .content-hero-title h2 {
            font-size: 1.3em;
            text-transform: uppercase;
            font-weight: bold;
            color: #E8C619;
            letter-spacing: -0.03em;
            line-height: 1.1em;
            margin: 0;
        }

        @media (min-width: 576px) {

            .detail-event .hero-banner .uk-container .content-hero-title .luogo-evento,
            .detail-event .hero-banner .uk-container .content-hero-title .data-evento,
            .detail-event .hero-banner .uk-container .content-hero-title h2 {
                font-size: 2.2vw;
            }
        }

        .detail-event .hero-banner .uk-container .content-hero-title .luogo-evento p,
        .detail-event .hero-banner .uk-container .content-hero-title .data-evento p,
        .detail-event .hero-banner .uk-container .content-hero-title h2 p {
            margin: 0;
        }

        .detail-event .hero-banner .uk-container .content-hero-title .data-evento {
            color: #FFF;
        }

        .detail-event .hero-banner .uk-container .content-hero-title .data-evento h2 {
            color: #FFF;
        }

        .text-intro p,
        .text-intro ul {
            line-height: 1.2;
            letter-spacing: -0.03em;
            color: #000;
            margin-bottom: 60px;
            font-size: 2.5em;
            word-break: break-word;
        }

        @media (max-width: 567px) {

            .text-intro p,
            .text-intro ul {
                font-size: 1.5em;
            }
        }

        .text-intro .cta-programma {
            display: none;
        }

        .section-programma {
            background-color: #000;
            color: #FFF;
            padding: 90px 0;
        }

        .section-programma a {
            color: #FFF;
        }

        .section-programma a:hover {
            opacity: 0.8;
        }

        .section-programma h3,
        .section-programma h2 {
            font-size: 3em;
            color: #FFF;
            margin-bottom: 60px;
            font-weight: bold;
        }

        @media (min-width: 576px) {

            .section-programma h3,
            .section-programma h2 {
                font-size: 4em;
            }
        }

        .section-programma .testo-programma {
            margin-bottom: 60px;
            font-size: 1.34em;
        }

        @media (min-width: 767px) {
            .section-programma .testo-programma {
                -webkit-columns: 250px 2;
                columns: 177px 2;
                -webkit-column-gap: 25px;
                column-gap: 25px;
                -webkit-column-rule: 0px none transparent;
                column-rule: 0px none transparent;
            }
        }

        .section-programma .testo-programma p {
            font-size: 1.34em;
            margin: 0 0 20px 0;
            margin-bottom: 1.34em;
            font-weight: 300;
        }

        @media (max-width: 567px) {
            .section-programma .testo-programma p {
                font-size: 1.2em;
            }
        }

        .section-protagonisti {
            position: relative;
            background-color: rgba(22, 37, 54, 1);
            padding: 90px 0;
        }

        .section-protagonisti .uk-background-norepeat {
            background-size: cover;
        }

        .section-protagonisti .list-view {
            height: auto;
            display: flex;
            flex-wrap: wrap;
        }

        .section-protagonisti .list-view>* {
            width: 100%;
            flex-basis: 100%;
            padding: 30px;
        }

        @media (min-width:576px) {
            .section-protagonisti .list-view>* {
                width: 50%;
                flex-basis: 50%;
                padding: 30px;
            }
        }

        @media (min-width:768px) {
            .section-protagonisti .list-view>* {
                width: 25%;
                flex-basis: 25%;
                padding: 30px;
            }
        }

        .section-protagonisti h3,
        .section-protagonisti h2 {
            font-size: 3em;
            color: #FFF;
            margin-bottom: 60px;
            font-weight: bold;
        }

        @media (min-width: 576px) {

            .section-protagonisti h3,
            .section-protagonisti h2 {
                font-size: 4em;
            }
        }

        .section-protagonisti .speaker .uk-transition-toggle {
            background-color: #162536;
            border-radius: 100em;
            position: relative;
        }

        .section-protagonisti .speaker .uk-transition-toggle .img {
            border-radius: 100em;
            overflow: hidden;
            filter: grayscale(100%);
            mix-blend-mode: luminosity;
            transition: all 0.5s ease;
        }

        .object-fit-cover img {
            object-fit: cover;
        }

        .section-protagonisti .speaker .uk-transition-toggle .img img {
            object-fit: cover;
        }

        .section-protagonisti .speaker .uk-transition-toggle:hover .img,
        .section-protagonisti .speaker .uk-transition-toggle:focus .img {
            filter: grayscale(100%) brightness(20%);
            transition: all 0.5s ease;
        }

        .section-protagonisti .speaker .uk-transition-toggle .uk-position-center {
            width: 90%;
        }

        .section-protagonisti .text-protagonist {
            position: absolute;
            width: 90%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: table;
            text-align: center;
            opacity: 0;
        }

        .section-protagonisti .text-protagonist h4,
        .section-protagonisti .text-protagonist p {
            margin: 0;
            color: #fff;
        }

        .section-protagonisti .text-protagonist h4 {
            font-size: 22px;
            font-weight: bold;
        }

        .section-protagonisti .speaker .uk-transition-toggle:hover .text-protagonist {
            opacity: 1;
        }

        .section-contact-info {
            padding-top: 90px;
            padding-bottom: 90px;
        }

        .section-contact-info h3,
        .section-contact-info h2 {
            font-size: 3em;
            margin-bottom: 60px;
            font-weight: bold;
        }

        .section-contact-info p {
            font-size: 1.3em;
        }

        @media (min-width: 576px) {

            .section-contact-info h3,
            .section-contact-info h2 {
                font-size: 4em;
            }
        }

        .section-news {
            background-color: #006666;
            overflow: hidden;
            padding-top: 90px;
            padding-bottom: 90px;
        }

        .section-news h3,
        .section-news h2 {
            font-size: 3em;
            color: #FFF;
            margin-bottom: 60px;
            font-weight: bold;
        }

        @media (min-width: 576px) {

            .section-news h3,
            .section-news h2 {
                font-size: 4em;
            }
        }

        .section-news a {
            color: #fff;
        }

        .section-news a:hover {
            text-decoration: none;
        }

        .section-news .item-news-home h4 {
            font-size: 2em;
            line-height: 1.2em;
            letter-spacing: -0.02em;
        }

        .section-gallery {
            padding-top: 90px;
            padding-bottom: 90px;
        }

        .section-gallery h3,
        .section-gallery h2 {
            font-size: 3em;
            color: #000;
            margin-bottom: 60px;
            font-weight: bold;
        }

        @media (min-width: 576px) {

            .section-gallery h3,
            .section-gallery h2 {
                font-size: 4em;
            }
        }

        .section-gallery .list-view div[data-key] .item-image {
            aspect-ratio: 1.70212766;
            overflow: hidden;
        }

        @media (min-width: 960px) {
            .section-gallery .list-view div[data-key] {
                padding-left: 50px;
            }
        }

        @media (min-width: 960px) {
            .section-gallery .list-view div[data-key]:nth-child(5):not(:last-of-type) {
                width: calc(100% * 2 / 3);
            }
        }

        .section-gallery .list-view div[data-key] img {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        .section-gallery .list-view .pagination>li>a,
        .section-gallery .list-view .pagination>li>span {
            color: #000;
        }

        .section-gallery .item-image-gallery {
            margin-bottom: 30px;
        }

        .section-streaming {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            margin-top: 30px;
        }

        .section-streaming .box-streaming {
            color: #FFF;
        }

        .section-streaming h3 {
            margin-bottom: 0;
            color: #FFF;
        }

        .section-streaming .btn-primary {
            background-color: transparent;
        }

        .section-streaming .btn-primary:hover {
            background-color: #297A38;
        }

        .section-streaming #countdown {
            text-align: center;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            padding: 10px 0;
            justify-content: space-between;
            color: #fff;
            font-size: 22px;
        }

        @media (max-width: 367px) {
            .section-streaming #countdown {
                flex-wrap: wrap;
                margin-bottom: 10px;
            }
        }

        .section-streaming #countdown div {
            width: 22%;
            text-align: center;
            padding: 0px;
        }

        @media (max-width: 420px) {
            .section-streaming #countdown div {
                font-size: 14px;
            }
        }

        .section-streaming #countdown div p {
            background-color: rgba(0, 0, 0, 0.85);
            margin: 0 0 5px;
            padding: 20px 5px;
            border-radius: 20px;
            color: #fff;
            font-size: 30px;
        }

        @media (min-width:768px) {
            .section-streaming #countdown div p {
                font-size: 50px;
            }
        }

        .section-streaming #diretta-streaming {
            padding: 0;
        }

        .section-streaming #diretta-streaming iframe {
            margin-top: 10px;
        }

        @media (max-width: 991px) {
            .section-streaming #diretta-streaming iframe {
                height: 520px;
            }
        }

        @media (max-width: 620px) {
            .section-streaming #diretta-streaming iframe {
                height: 220px;
            }
        }

        .section-streaming .streaming-video-container {
            margin-top: 10px;
        }

        .section-streaming .btn-primary {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #297A38;
            color: #fff;
        }

        .section-video {
            position: relative;
            background-color: rgba(0, 0, 0, 1);
            padding: 90px 0;
        }



        .section-video .uk-container {
            position: relative;
            z-index: 1;
        }

        .section-video h3,
        .section-video h2 {
            font-size: 3em;
            color: #FFF;
            margin-bottom: 60px;
            font-weight: bold;
            margin-top: 0;
        }

        @media (min-width: 576px) {

            .section-video h3,
            .section-video h2 {
                font-size: 4em;
            }
        }

        .section-video .wrap-playlist .wrap-active-video iframe {
            width: 100%;
            height: 675px;
        }

        .section-video .wrap-playlist .wrap-video-thumbs {
            display: block;
            justify-content: space-between;
            margin-top: 50px;
        }

        @media (min-width: 640px) {
            .section-video .wrap-playlist .wrap-video-thumbs {
                display: flex;
                flex-wrap: wrap;
            }
        }

        .section-video .wrap-playlist .wrap-video-thumbs a.wrap-video {
            width: 100%;
            margin-bottom: 20px;
        }

        @media (min-width: 640px) {
            .section-video .wrap-playlist .wrap-video-thumbs a.wrap-video {
                width: 48%;
            }
        }

        .section-video .wrap-playlist .wrap-video-thumbs a.wrap-video img,
        .section-video .wrap-playlist .wrap-video-thumbs a.wrap-video .uk-inline {
            width: 100%;
        }

        .section-video .wrap-playlist .wrap-video-thumbs a.wrap-video p {
            color: #000;
            font-size: 1.2em;
        }

        .section-video .wrap-playlist .wrap-video-thumbs a.wrap-video:hover {
            opacity: 0.7;
        }

        .form-section {
            padding: 90px 0;
        }

        .form-section a:not(.btn) {
            text-decoration: underline;
        }

        .form-section .uk-container> :last-child:not(.btn) {
            margin-bottom: 90px;
        }

        .form-section .required label:after {
            content: '*';
            padding-left: 2px;
            font-size: 0.8em;
        }

        .form-section .required.field-recorddynamicmodel-privacy label:after {
            content: inherit;
        }

        .green-blu-form {
            background-color: #162536;
        }

        .green-blu-form form .social-container span,
        .green-blu-form form .social-container span strong,
        .green-blu-form form label,
        .green-blu-form form label>a,
        .green-blu-form form span.help-block,
        .green-blu-form form span.am-help,
        .green-blu-form form .control-label {
            color: #ffffff;
        }

        .green-blu-form form label>a {
            color: #ffffff;
        }

        .green-blu-form form input.form-control {
            border-bottom: 1px solid #ffffff;
            color: #ffffff;
        }

        .green-blu-form form .btn.btn-primary {
            background-color: #ffffff;
            border-color: #ffffff;
            color: #162536;
        }

        .green-blu-form form .btn.btn-primary:hover {
            background-color: #162536;
            border-color: #ffffff;
            color: #ffffff;
        }

        .green-blu-form form .social-container .btn-login-social>span {
            color: #162536;
        }

        .section-landing-cms-form form {
            margin-top: 20px;
        }

        .section-landing-cms-form p {
            font-size: 1.2em;
        }

        .section-landing-cms-form p a {
            color: orange;
        }

        .section-landing-cms-form .social-container .btn-login-social {
            color: transparent;
            width: 30px;
            height: 30px;
        }

        .section-landing-cms-form .social-container .btn-login-social .social-buttons a {
            margin-right: 0;
            margin-left: 0;
        }

        .section-landing-cms-form .social-container .btn-login-social.btn-facebook:hover,
        .section-landing-cms-form .social-container .btn-login-social.btn-twitter:hover,
        .section-landing-cms-form .social-container .btn-login-social.btn-linkedin:hover {
            background-color: transparent;
            opacity: 1;
            border: 1px solid white;
        }

        .section-landing-cms-form h3,
        .section-landing-cms-form h2 {
            color: white;
            font-size: 4em;
            line-height: 1;
            margin-top: 0;
        }

        .section-landing-cms-form .Nome_item,
        .section-landing-cms-form .Cognome_item,
        .section-landing-cms-form .Email_item {
            width: 49%;
            display: inline-block;
        }

        .section-landing-cms-form .Sesso_item,
        .section-landing-cms-form .Eta_item,
        .section-landing-cms-form .Comune_item {
            width: 30%;
            display: inline-block;
            margin: 0;
        }

        .section-landing-cms-form .Email_item {
            width: 100%;
        }

        .section-landing-cms-form .Eta_item {
            margin: 0 4%;
        }

        .section-landing-cms-form .select2-search--dropdown {
            display: none;
        }

        .section-landing-cms-form .Sesso_item .radio {
            margin: 10px 0;
        }

        .section-landing-cms-form .Sesso_item .radio:nth-child(2) {
            margin-left: 10px;
        }

        .section-landing-cms-form .Nome_item {
            margin: 0;
        }

        .section-landing-cms-form .Cognome_item {
            margin-top: 0;
            margin-bottom: 0;
            margin-left: 1%;
            margin-right: 0;
        }

        .section-landing-cms-form #recorddynamicmodel-sesso {
            display: flex;
        }

        .section-landing-cms-form .Email_item {
            margin: 0;
        }

        .section-landing-cms-form .Privacy_item {
            margin: 5% 0;
        }

        .section-landing-cms-form #social-container {
            display: flex;
            flex-direction: row;
            margin-bottom: 20px;
            margin-top: 50px;
            text-align: left;
            flex-wrap: wrap;
        }

        .section-landing-cms-form #social-container span {
            width: 60%;
        }

        .section-landing-cms-form #social-container .social-buttons {
            text-align: left;
            margin-top: 14px;
        }

        .section-landing-cms-form form .help-block.help-block-error {
            color: orange;
        }

        .section-landing-cms-form form label,
        .section-landing-cms-form form label>a {
            font-weight: 700;
        }

        .section-landing-cms-form form .social-container {
            margin-bottom: 30px;
        }

        .section-landing-cms-form form .social-container span.am {
            padding: 0;
            bottom: 1px;
            position: relative;
        }

        .section-landing-cms-form form .userSocial_item,
        .section-landing-cms-form form .datiRecuperatiDaSocial_item,
        .section-landing-cms-form form .socialScelto_item,
        .section-landing-cms-form form .associaNuovoAccountSocial_item {
            display: none;
        }

        .section-landing-cms-form form .help-block {
            margin-top: 0;
        }

        .section-landing-cms-form form input.form-control {
            background: transparent;
            box-shadow: none;
            border: none;
            border-radius: 0;
        }

        .section-landing-cms-form form .form-group>div.row {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .section-landing-cms-form form .form-group>div.row>div:nth-child(1) {
            width: auto;
            order: 1;
        }

        .section-landing-cms-form form .form-group>div.row>div:nth-child(2) {
            order: 3;
            flex-grow: 1;
            width: 100%;
        }

        .section-landing-cms-form form .form-group>div.row>div:nth-child(3) {
            width: auto;
            order: 2;
            display: flex;
            flex-direction: row-reverse;
        }

        .section-landing-cms-form form label em {
            font-size: 16px;
            line-height: 1;
            display: block;
            font-weight: normal;
        }

        .section-landing-cms-form form .uk-form-controls {
            text-align: center;
            margin-top: 30px;
        }

        .section-landing-cms-form form .uk-form-controls .btn.btn-primary {
            font-size: 1.5em;
        }

        .btn-idpc {
            background-color: #0066CC;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
        }

        .btn-idpc:hover {
            color: #ffffff;
            background-color: #0059b3;
        }

        .prossimi-eventi {
            background-color: #585858;
            padding: 90px 0;
            color: #fff;
        }

        .prossimi-eventi .tit-prox-eventi {
            font-size: 1.8em;
            color: #FFF;
            font-weight: bold;
            letter-spacing: -0.03em;
            margin-bottom: 20px;
        }

        .prossimi-eventi .item-child {
            margin-bottom: 40px;
        }

        .prossimi-eventi a {
            color: #FFF;
            text-decoration: none;
        }

        .prossimi-eventi a:hover,
        .prossimi-eventi a:focus {
            text-decoration: underline;
        }

        .prossimi-eventi .title-event-child {
            font-weight: bold;
            margin-top: 10px;
            font-size: 22px;
        }
    </style>

</head>

<body>
    <?php
    echo $this->render('parts/_amp-menu');
    ?>
    <?php
    echo $this->render('parts/_amp-navbar');
    ?>
    <div id="main-content" class="content">
        <?= $content; ?>
    </div>


</body>

</html>

<?php
$this->endPage();
?>