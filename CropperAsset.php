<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-cropper/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-cropper#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\cropper;

use yii\web\AssetBundle;

/**
 * CropperAsset
 *
 * @url https://github.com/fengyuanchen/cropper
 */
class CropperAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $css = [
        'cropper/dist/cropper.min.css',
    ];
    public $js = [
        'cropper/dist/cropper.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
