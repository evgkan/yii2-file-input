<?php

namespace sgdot\widgets;

use \yii\helpers\Html;
use yii\helpers\Json;

class FormInputImage extends \mihaildev\elfinder\InputFile {

    public $language = 'ru';
    public $controller = 'elfinder';
    public $filter = 'image';
    public $template = '{preview}{input}{button}';

    public function run() {
        if ($this->hasModel()) {
            $src = \yii\helpers\Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $src = $this->value;
        }
        $previewId = $this->id . '_preview_img';
        if (empty($src)) {
            $preview = '<div id="' . $previewId . '"></div>';
        } else {
            $preview = Html::tag('div', Html::img($src, ['class' => 'form-img-preview']), [
                    'id' => $previewId,
            ]);
        }
        $replace['{preview}'] = $preview;
        $this->template = strtr($this->template, $replace);

        parent::run();
        $this->getView()->registerJs("ElFinderFileCallback.register(" . Json::encode($this->options['id']) . ", function(file, id){ \$('#' + id).val(file.url); \$('#$previewId').html('<img class=\"form-img-preview\" src=\"'+file.url+'\">'); return true;}); ");
        $this->view->registerCss('.form-img-preview {max-width:100px;}');
    }

}
