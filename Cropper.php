<?php

namespace demi\cropper;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class Cropper extends Widget
{
    /** @var string URL for send crop data */
    public $cropUrl;
    /** @var string Original image URL */
    public $image;
    /** @var float Aspect ratio for crop box. If not set(null) - it means free aspect ratio */
    public $aspectRatio;
    /** @var bool Show crop box in modal window */
    public $modal = true;
    /** @var string Name of the view file for modal cropping mode */
    public $modalView = 'modal';
    /** @var array HTML widget options */
    public $options = [];
    /** @var array HTML-options for image tag */
    public $imageOptions = [
        'class' => 'cropper-image',
        'alt' => 'crop-image',
    ];
    /** @var array Additional cropper options https://github.com/fengyuanchen/cropper/blob/master/README.md#options */
    public $pluginOptions = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        } else {
            $this->setId($this->options['id']);
        }

        // Set additional cropper js-options
        if (!empty($this->aspectRatio)) {
            $this->pluginOptions['aspectRatio'] = $this->aspectRatio;
        }

        $content = '';

        if ($this->modal) {
            // Modal button
            $buttonOptions = $this->options;
            unset($buttonOptions['id']);
            $content .= Html::a('Crop image', '#' . $this->id, ArrayHelper::merge([
                'data' => [
                    'toggle' => 'modal',
                    'target' => '#' . $this->id,
                    'crop-url' => Url::to($this->cropUrl),
                ],
            ], $buttonOptions));

            // Modal dialog
            $content .= $this->render($this->modalView, ['widget' => $this]);
        } else {
            $content .= Html::beginTag('div', $this->options);
            $content .= Html::img($this->image, $this->imageOptions);
            $content .= Html::endTag('div');
        }

        $this->registerClientScript();

        return $content;
    }

    /**
     * Registers required script for the plugin to work as jQuery image cropping
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        // Register jQuery image cropping js and css files
        CropperAsset::register($view);
        // Additional plugin options
        $options = Json::encode($this->pluginOptions);

        $imageCssClass = $this->imageOptions['class'];
        $selector = "#$this->id ." . $imageCssClass;

        if ($this->modal) {

            $view->registerJs(<<<JS
(function() {

    var modalBox = $("#$this->id"),
        image = $("$selector"),
        cropBoxData,
        canvasData,
        cropUrl;

    modalBox.on("shown.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        cropUrl = button.data('crop-url'); // Extract info from data-* attributes

        image.cropper({
            autoCropArea: 0.5,
            built: function () {
                // Strict mode: set crop box data first
                image.cropper('setCropBoxData', cropBoxData);
                image.cropper('setCanvasData', canvasData);
            }
        });
    }).on('hidden.bs.modal', function () {
        cropBoxData = image.cropper('getCropBoxData');
        canvasData = image.cropper('getCanvasData');
        image.cropper('destroy');
    });

    $(document).on("click", "#$this->id .crop-submit", function(e) {
        e.preventDefault();

        $.post(cropUrl, image.cropper('getCropBoxData'), function(data) {

        }).fail(function() {
            alert("Error while cropping");
        });

        modalBox.modal("hide");
    });

})();
JS
            );
        } else {
            $view->registerJs(";$(\"$selector\").cropper($options);");
        }
    }
}