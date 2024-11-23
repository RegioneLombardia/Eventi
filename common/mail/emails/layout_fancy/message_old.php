<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\email
 * @category   CategoryName
 */


use open20\amos\admin\models\UserProfile;

$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appName = Yii::$app->name;
?>
<table width="600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td style="margin-bottom:10px;background-color:#297A38;height:15px"></td>
        <td style="margin-bottom:10px;background-color:#297A38;height:15px"></td>
        <td style="margin-bottom:10px;background-color:#297A38;height:15px"></td>
    </tr>
    <tr>
        <td style="height:10px"></td>
    </tr>
    <tr style="background-color:#ffffff;">
        <td>
            <?php if (isset(Yii::$app->params['logoMail'])) {
                $logoMail = $appLink . Yii::$app->params['logoMail'];
            } else {
                $logoMail = '';
            } ?>
            <img style="max-width:500px; max-height:60px;" src="<?php echo $logoMail ?>" alt="logo">
        </td>
    </tr>
</table>

<?php if ($heading) { ?>
    <table width=" 600" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td align="center" valign="top">
                <h1 style="padding-top: 25px; color:green;margin:0;display:block;font-family:Arial;font-size:25px;font-weight:bold;text-align:center;line-height:150%"><?php echo $heading; ?></h1>
            </td>
        </tr>
    </table>
<?php } ?>


<table width=" 600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td>
            <div class="corpo"
                 style="padding:10px;margin-bottom:10px;background-color:#fff;">
                <?php echo $contents; ?>
            </div>
        </td>
    </tr>
</table>

