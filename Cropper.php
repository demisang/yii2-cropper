<?php

namespace demi\cropper;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

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
    /** @var array Default HTML-options for image tag */
    public $defaultImageOptions = [
        'class' => 'cropper-image img-responsive',
        'alt' => 'crop-image',
    ];
    /** @var array HTML-options for image tag */
    public $imageOptions = [];
    /** @var array Default cropper options https://github.com/fengyuanchen/cropper/blob/master/README.md#options */
    public $defaultPluginOptions = [
        'strict' => true,
        'autoCropArea' => 1,
        'checkImageOrigin' => false,
        'checkCrossOrigin' => false,
        'checkOrientation' => false,
        'zoomable' => false,
    ];
    /** @var array Additional cropper options https://github.com/fengyuanchen/cropper/blob/master/README.md#options */
    public $pluginOptions = [];
    /** @var array Ajax options for send crop-reques */
    public $ajaxOptions = [
        'success' => 'js:function(data) { console.log(data); }',
    ];
    /**
     * Translated messages:
     *
     * [
     *     'cropBtn' => Yii::t('app', 'Crop'),
     *     'cropModalTitle' => Yii::t('app', 'Select crop area and click "Crop" button'),
     *     'closeModalBtn' => Yii::t('app', 'Close'),
     *     'cropModalBtn' => Yii::t('app', 'Crop selected'),
     * ]
     *
     * @var array
     */
    public $messages = [];

    public function init()
    {
        parent::init();

        if (empty($this->messages['cropBtn'])) {
            $this->messages['cropBtn'] = 'Crop';
        }
        if (empty($this->messages['cropModalTitle'])) {
            $this->messages['cropModalTitle'] = 'Select crop area and click "Crop" button';
        }
        if (empty($this->messages['closeModalBtn'])) {
            $this->messages['closeModalBtn'] = 'Close';
        }
        if (empty($this->messages['cropModalBtn'])) {
            $this->messages['cropModalBtn'] = 'Crop selected';
        }
    }

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

        $this->pluginOptions = ArrayHelper::merge($this->defaultPluginOptions, $this->pluginOptions);
        $this->imageOptions = ArrayHelper::merge($this->defaultImageOptions, $this->imageOptions);

        // Set additional cropper js-options
        if (!empty($this->aspectRatio)) {
            $this->pluginOptions['aspectRatio'] = $this->aspectRatio;
        }

        $content = '';

        if ($this->modal) {
            // Modal button
            $buttonOptions = $this->options;
            unset($buttonOptions['id']);
            $content .= Html::a($this->messages['cropBtn'] . ' <i class="glyphicon glyphicon-scissors"></i>',
                '#' . $this->id,
                ArrayHelper::merge([
                    'data' => [
                        'toggle' => 'modal',
                        'target' => '#' . $this->id,
                        'crop-url' => Url::to($this->cropUrl),
                    ],
                    'class' => 'btn btn-primary',
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

        $selector = "#$this->id .crop-image-container > img";

        if ($this->modal) {
            $ajaxOptions = Json::encode($this->ajaxOptions);
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

        image.cropper($.extend({
            built: function () {
                // Strict mode: set crop box data first
                image.cropper('setCropBoxData', cropBoxData);
                image.cropper('setCanvasData', canvasData);
            },
            dragend: function() {
                cropBoxData = image.cropper('getCropBoxData');
                canvasData = image.cropper('getCanvasData');
            }
        }, $options));
    }).on('hidden.bs.modal', function () {
        cropBoxData = image.cropper('getCropBoxData');
        canvasData = image.cropper('getCanvasData');
        image.cropper('destroy');
    });

    $(document).on("click", "#$this->id .crop-submit", function(e) {
        e.preventDefault();

        $.ajax($.extend({
            method: "POST",
            url: cropUrl,
            data: image.cropper("getData"),
            dataType: "JSON",
            error: function() {
                alert("Error while cropping");
            }
        }, $ajaxOptions));

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
