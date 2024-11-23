<?php
/**
 * @var $model \amos\sitemanagement\models\SiteManagementSliderElem
 */

$fileImage = $model->fileImage;
if($fileImage){
$urlImageThumb = $fileImage->getWebUrl('thumb');

?>
<div class="col-md-4 item-image-gallery">
    <amp-img src="<?= $urlImageThumb ?>" width="500" height="300" class="object-fit-cover" layout="responsive"></amp-img>
</div>
<?php } ?>

