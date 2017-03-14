<?php
namespace rocketfirm\fileupload\assets;

use yii\web\AssetBundle;

/**
 * Class ImagePreviewAsset
 * @package rocketfirm\fileupload\assets\imagepreview
 */
class ImagePreviewAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/rocketfirm/yii2-file-upload/js';

    /**
     * @var array script file
     */
    public $js = ['rf-img-preview.js'];

    /**
     * @var array depends on jquery asset
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}