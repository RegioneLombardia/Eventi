<?php

/** do not delete asset */

use open20\design\assets\BootstrapItaliaDesignAsset;

$currentAsset = BootstrapItaliaDesignAsset::register($this);

?>

<!-- usata per CMS Luya -->
<?php $customBehavior ?>


<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">



<head>
    <?php if ((isset(\Yii::$app->params['layoutConfigurations']['customPlatformHead']))) : ?>
        <?php
        $customPlatformHead = \Yii::$app->params['layoutConfigurations']['customPlatformHead'];
        echo $this->render($customPlatformHead, [
            'currentAsset' => $currentAsset,
        ]);
        ?>
    <?php else : ?>

        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-head", [
            'currentAsset' => $currentAsset
        ]); ?>
    <?php endif ?>
</head>

<body class="bg-body position-relative d-flex flex-column">
    <?php $this->beginBody() ?>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-skiplink", [
        'currentAsset' => $currentAsset,
    ]); ?>

    <?php
    if (!(\Yii::$app->params['layoutConfigurations']['hideCmsMenuPluginHeader'])) :
        $cmsDefaultMenu .= $cmsPluginMenu;
    endif;
    ?>

    <?php
    if (isset(\Yii::$app->view->params['hideHamburgerMenuHeader'])) {
        $hideHamburgerMenuHeaderCheck = (\Yii::$app->view->params['hideHamburgerMenuHeader']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['hideHamburgerMenuHeader'])) {
            $hideHamburgerMenuHeaderCheck = (\Yii::$app->params['layoutConfigurations']['hideHamburgerMenuHeader']);
        } else {
            $hideHamburgerMenuHeaderCheck = false;
        }
    }

    if (isset(\Yii::$app->view->params['hideGlobalSearchHeader'])) {
        $hideGlobalSearchHeaderCheck = (\Yii::$app->view->params['hideGlobalSearchHeader']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['hideGlobalSearchHeader'])) {
            $hideGlobalSearchHeaderCheck = (\Yii::$app->params['layoutConfigurations']['hideGlobalSearchHeader']);
        } else {
            $hideGlobalSearchHeaderCheck = false;
        }
    }

    if (isset(\Yii::$app->view->params['hideUserMenuHeader'])) {
        $hideUserMenuHeaderCheck = (\Yii::$app->view->params['hideUserMenuHeader']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['hideUserMenuHeader'])) {
            $hideUserMenuHeaderCheck = (\Yii::$app->params['layoutConfigurations']['hideUserMenuHeader']);
        } else {
            $hideUserMenuHeaderCheck = false;
        }
    }

    if (isset(\Yii::$app->view->params['fluidContainerHeader'])) {
        $fluidContainerHeaderCheck = (\Yii::$app->view->params['fluidContainerHeader']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['fluidContainerHeader'])) {
            $fluidContainerHeaderCheck = (\Yii::$app->params['layoutConfigurations']['fluidContainerHeader']);
        } else {
            $fluidContainerHeaderCheck = false;
        }
    }

    if (isset(\Yii::$app->view->params['hideCookieBar'])) {
        $hideCookieBarCheck = (\Yii::$app->view->params['hideCookieBar']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['hideCookieBar'])) {
            $hideCookieBarCheck = (\Yii::$app->params['layoutConfigurations']['hideCookieBar']);
        } else {
            $hideCookieBarCheck = false;
        }
    }

    if (isset(\Yii::$app->view->params['hideAssistance'])) {
        $hideAssistanceCheck = (\Yii::$app->view->params['hideAssistance']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['hideAssistance'])) {
            $hideAssistanceCheck = (\Yii::$app->params['layoutConfigurations']['hideAssistance']);
        } else {
            $hideAssistanceCheck = false;
        }
    }

    if (isset(\Yii::$app->view->params['hideTopHeaderForGuestUser'])) {
        $hideTopHeaderForGuestUserCheck = (\Yii::$app->view->params['hideTopHeaderForGuestUser']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['hideTopHeaderForGuestUser'])) {
            $hideTopHeaderForGuestUserCheck = (\Yii::$app->params['layoutConfigurations']['hideTopHeaderForGuestUser']);
        } else {
            $hideTopHeaderForGuestUserCheck = false;
        }
    }
    if ($hideTopHeaderForGuestUserCheck == true && \Yii::$app->user->isGuest) {
        $hideTopHeaderForGuestUserCheck = true;
    } else {
        $hideTopHeaderForGuestUserCheck = false;
    }

    if (isset(\Yii::$app->view->params['alwaysHamburgerMenuRight'])) {
        $alwaysHamburgerMenuRightCheck = (\Yii::$app->view->params['alwaysHamburgerMenuRight']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['alwaysHamburgerMenuRight'])) {
            $alwaysHamburgerMenuRightCheck = (\Yii::$app->params['layoutConfigurations']['alwaysHamburgerMenuRight']);
        } else {
            $alwaysHamburgerMenuRightCheck = false;
        }
    }


    if (isset(\Yii::$app->view->params['showSocialHeader'])) {
        $showSocialHeaderCheck = (\Yii::$app->view->params['showSocialHeader']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['showSocialHeader'])) {
            $showSocialHeaderCheck = (\Yii::$app->params['layoutConfigurations']['showSocialHeader']);
        } else {
            $showSocialHeaderCheck = false;
        }
    }

    if (isset(\Yii::$app->view->params['showSocialFooter'])) {
        $showSocialFooterCheck = (\Yii::$app->view->params['showSocialFooter']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['showSocialFooter'])) {
            $showSocialFooterCheck = (\Yii::$app->params['layoutConfigurations']['showSocialFooter']);
        } else {
            $showSocialFooterCheck = false;
        }
    }

    if (isset(\Yii::$app->params['layoutConfigurations']['customCopyleftFooter'])) {
        $customCopyleftFooter = (\Yii::$app->params['layoutConfigurations']['customCopyleftFooter']);
    } else {
        $customCopyleftFooter = 'Powered by Open 2.0';
    }

    ?>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-header", [
        'currentAsset' => $currentAsset,
        'cmsDefaultMenu' => $cmsDefaultMenu,
        'cmsSecondaryMenu' => $cmsSecondaryMenu,
        'privacyPolicyLink' => \Yii::$app->params['linkConfigurations']['privacyPolicyLinkCommon'],
        'cookiePolicyLink' => \Yii::$app->params['linkConfigurations']['cookiePolicyLinkCommon'],
        'hideHamburgerMenu' => $hideHamburgerMenuHeaderCheck,
        'alwaysHamburgerMenu' => \Yii::$app->params['layoutConfigurations']['showAlwaysHamburgerMenuHeader'],
        'hideLangSwitchMenu' => \Yii::$app->params['layoutConfigurations']['hideLangSwitchMenuHeader'],
        'hideGlobalSearch' => $hideGlobalSearchHeaderCheck,
        'hideUserMenu' => $hideUserMenuHeaderCheck,
        'fluidContainerHeader' => $fluidContainerHeaderCheck,
        'customUserMenu' => \Yii::$app->params['layoutConfigurations']['customUserMenuHeader'],
        'customUserNotLogged' => \Yii::$app->params['layoutConfigurations']['customUserNotLoggedHeader'],
        'customUserMenuLoginLink' => \Yii::$app->params['linkConfigurations']['loginLinkCommon'],
        'customUserMenuLogoutLink' => \Yii::$app->params['linkConfigurations']['logoutLinkCommon'],
        'customUserProfileLink' => \Yii::$app->params['linkConfigurations']['userProfileLinkCommon'],
        'customPlatformMenu' => \Yii::$app->params['menuCmsConfigurations']['customPlatformMenu'],
        'showSocial' => $showSocialHeaderCheck,
        'showSecondaryMenu' => \Yii::$app->params['layoutConfigurations']['showSecondaryMenuHeader'],
        'disableThemeLight' => \Yii::$app->params['layoutConfigurations']['disableThemeLightHeader'],
        'disableSmallHeader' => \Yii::$app->params['layoutConfigurations']['disableSmallHeader'],
        'enableHeaderSticky' => \Yii::$app->params['layoutConfigurations']['enableHeaderStickyHeader'],
        'pageSearchLink' => \Yii::$app->params['linkConfigurations']['pageSearchLinkCommon'],
        'hideTopHeaderForGuestUser' => $hideTopHeaderForGuestUserCheck,
        'alwaysHamburgerMenuRight' => $alwaysHamburgerMenuRightCheck
    ]); ?>

    <div id="mainContent" class="d-flex <?= \Yii::$app->view->params['customClassMainContent'] ?>">

        <main role="main" class="w-100 bg-white <?= (\Yii::$app->params['layoutConfigurations']['enableHeaderStickyHeader']) ? 'mt-0' : '' ?>">
            <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-messages", [
                'currentAsset' => $currentAsset
            ]); ?>

            <?php if ((!(\Yii::$app->params['layoutConfigurations']['hideBreadcrumbs']) || (!\Yii::$app->view->params['hideBreadcrumbs']))) : ?>
                <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-breadcrumbs", [
                    'currentAsset' => $currentAsset
                ]); ?>
            <?php endif ?>

            <?php if (\Yii::$app->view->params['enablePluginHeader']) : ?>
                <?= $this->render(
                    "parts" . DIRECTORY_SEPARATOR . "bi-plugin-header",
                    [
                        'title' => $this->titlePlugin,
                        'helpText' => $this->textPluginHeader,
                        'hideViewAll' => true
                    ]
                ); ?>
            <?php endif ?>

            <?php if ((\Yii::$app->view->params['enablePluginToolbar'])) : ?>
                <?= $this->render(
                    "parts" . DIRECTORY_SEPARATOR . "bi-plugin-toolbar"
                ); ?>
            <?php endif ?>

            <?php if ($this instanceof AmosView) : ?>
                <?php $this->beginViewContent() ?>
            <?php endif; ?>
            <?= $content ?>
            <?php if ($this instanceof AmosView) : ?>
                <?php $this->endViewContent() ?>
            <?php endif; ?>
        </main>

    </div>

    <?php if ((isset(\Yii::$app->params['layoutConfigurations']['customPlatformFooter']))) : ?>
        <?php
        $customPlatformFooter = \Yii::$app->params['layoutConfigurations']['customPlatformFooter'];
        echo $this->render($customPlatformFooter, [
            'currentAsset' => $currentAsset,
            'cmsFooterMenu' => $cmsFooterMenu,
            'showSocial' => $showSocialFooterCheck,
            'customCopyleftFooter' => $customCopyleftFooter,
        ]);
        ?>
    <?php else : ?>
        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-footer", [
            'customCopyleftFooter' => $customCopyleftFooter,
            'currentAsset' => $currentAsset,
            'cmsFooterMenu' => $cmsFooterMenu,
            'showSocial' => $showSocialFooterCheck,
        ]); ?>
    <?php endif; ?>
    <?php if (!$hideCookieBarCheck) : ?>
        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-cookiebar", [
            'currentAsset' => $currentAsset,
            'cookiePolicyLink' => \Yii::$app->params['linkConfigurations']['cookiePolicyLinkCommon']
        ]); ?>
    <?php endif ?>
    <!-- ASSISTANCE -->
    <?php if (!$hideAssistanceCheck) : ?>
        <?php
        $isMail = ((isset(Yii::$app->params['assistance']['type']) && Yii::$app->params['assistance']['type'] == 'email') || (!isset(Yii::$app->params['assistance']['type']) && isset(\Yii::$app->params['email-assistenza']))) ? true : false;
        $mailAddress = isset(Yii::$app->params['assistance']['email']) ? Yii::$app->params['assistance']['email'] : (isset(\Yii::$app->params['email-assistenza']) ? \Yii::$app->params['email-assistenza'] : '');
        $urlAssistance = !empty(Yii::$app->params['assistance']['url']) ? \Yii::$app->params['platform']['frontendUrl'] . '/' . Yii::$app->params['assistance']['url'] : '#';
        ?>
        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-assistance", [
            'currentAsset' => $currentAsset,
            'isMail' => $isMail,
            'mailAddress' => $mailAddress,
            'urlAssistance' => $urlAssistance
        ]); ?>
    <?php endif ?>
    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-backtotop", [
        'currentAsset' => $currentAsset,
    ]); ?>


    <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>