Yii2-cropper
===================
Wrapper for [Image Cropper](http://fengyuanchen.github.io/cropper/) javascript library 

Installation
---
Run
```code
php composer.phar require "demi/cropper" "~1.0"
```
or


Add to composer.json in your project
```json
{
	"require": {
  		"demi/cropper": "~1.0"
	}
}
```
then run command
```code
php composer.phar update
```

# Usage
---
_hint: functionality of this extension is already implemented into my extension to [image uploading](https://github.com/demisang/yii2-image-uploader)_

```php
echo Cropper::widget([
    // If true - it's output button for toggle modal crop window
    'modal' => true,
    // You can customize modal window. Copy /vendor/demi/cropper/views/modal.php
    'modalView' => '@backend/views/image/custom_modal',
    // URL-address for the crop-handling request
    // By default, sent the following post-params: x, y, width, height, rotate
    'cropUrl' => ['cropImage', 'id' => $image->id],
    // Url-path to original image for cropping
    'image' => Yii::$app->request->baseUrl . '/images/' . $image->src,
    // The aspect ratio for the area of cropping
    'aspectRatio' => 4 / 3, // or 16/9(wide) or 1/1(square) or any other ratio. Null - free ratio
    // Additional params for JS cropper plugin
    'pluginOptions' => [
        // All possible options: https://github.com/fengyuanchen/cropper/blob/master/README.md#options
        'minCropBoxWidth' => 400, // minimal crop area width
        'minCropBoxHeight' => 300, // minimal crop area height
    ],
    // HTML-options for widget container
    'options' => [],
    // HTML-options for cropper image tag
    'imageOptions' => [],
    // Translated messages
    'messages' => [
        'cropBtn' => Yii::t('app', 'Crop'),
        'cropModalTitle' => Yii::t('app', 'Select crop area and click "Crop" button'),
        'closeModalBtn' => Yii::t('app', 'Close'),
        'cropModalBtn' => Yii::t('app', 'Crop selected'),
    ],
    // Additional ajax-options for send crop-request. See jQuery $.ajax() options
    'ajaxOptions' => [
        'success' => new JsExpression(<<<JS
            function(data) {
                // data - your JSON response from [[cropUrl]]
                $("#myImage").attr("src", data.croppedImageSrc);
            }
JS
        ),
    ],
]);
```
