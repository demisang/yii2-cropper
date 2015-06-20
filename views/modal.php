<?php

use demi\cropper\Cropper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $widget Cropper */

?>
<div class="modal fade" id="<?= $widget->id ?>" role="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select crop area and click "Crop" button</h4>
            </div>
            <div class="modal-body">
                <div class="crop-image-container">
                    <?= Html::img($widget->image, $widget->imageOptions) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary crop-submit">Crop</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->