<?php

namespace sgdot\widgets;

use \yii\helpers\Html;
use yii\helpers\Json;
use \mihaildev\elfinder\AssetsCallBack;
use yii\base\Exception;

class FormInputImages extends \mihaildev\elfinder\InputFile {

    public $language = 'ru';
    public $controller = 'elfinder';
    public $filter = 'image';
    public $template = '{preview}{button}';
    public $previewId;

    public function run() {
        if ($this->hasModel()) {
            $src = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $src = $this->value;
        }
        $this->previewId = $this->id . '_preview_img';
        if (empty($src)) {
            $preview = '<div id="' . $this->previewId . '"></div>';
        } elseif (is_array($src)) {
            $images = $this->renderImages($src);
            $preview = Html::tag('div', $images, [
                    'id' => $this->previewId,
            ]);
        } else {
            throw new Exception('Ошибка');
        }
        $replace['{preview}'] = $preview;
        $this->template = strtr($this->template, $replace);


        $replace['{button}'] = Html::button($this->buttonName, $this->buttonOptions);
        echo strtr($this->template, $replace);

        $this->registerJs();
    }

    /**
     * @param array $src
     */
    public function renderImages($items) {
        $itemswrap = [];
        foreach ($items as $src) {
            $img = Html::img($src);
            $imgwrap = Html::tag('div', $img, ['class' => 'image-preview']);
            $dellink = Html::a('<span class="glyphicon glyphicon-remove"></span>', null, ['class' => 'delete-image']);
            $delbutton = Html::tag('div', $dellink, ['class' => 'delete-button']);
            $name = Html::getInputName($this->model, $this->attribute);
            $input = Html::hiddenInput($name . '[]', $src);
            $itemswrap[] = Html::tag('div', $imgwrap . $delbutton . $input, ['class' => 'image-preview-wrap']);
        }
        return implode("\n", $itemswrap);
    }

    public function registerJs() {
        AssetsCallBack::register($this->getView());
        $js = "mihaildev.elFinder.register("
            . Json::encode($this->options['id'])
            . ", function(file, id){
                \$('#' + id).val(file.url); return true;
            });
            $('#" . $this->buttonOptions['id'] . "').click(function(){
                ElFinderFileCallback.openManager("
            . Json::encode($this->_managerOptions)
            . ");});";
        $this->getView()->registerJs($js);

        $this->getView()->registerJs(
            "ElFinderFileCallback.register("
            . Json::encode($this->options['id'])
            . ", function(file, id){
                var img = templateItem.replace('{src}', file.url).replace('{src}', file.url);
                \$('#$this->previewId').append(img);
                return true;}); ");
        $hiddenInput = Html::activeHiddenInput($this->model, $this->attribute . '[]', ['value' => '{src}']);
        $templateJS = 'var templateItem = \'<div class="image-preview-wrap">
  <div class="image-preview">
    <img src="{src}" alt="">
  </div>
  <div class="delete-button">
    <a class="delete-image">
      <span class="glyphicon glyphicon-remove"></span>
    </a>
  </div>
  ' . $hiddenInput . '
</div>\';';
        $this->getView()->registerJs(strtr($templateJS, [PHP_EOL => '']), \yii\web\View::POS_END);
        $deleteJs = '$(document).on("click", ".delete-button", function(){$(this).parent().remove()});';
        $this->getView()->registerJs($deleteJs);

        $this->view->registerCss('.form-img-preview {max-width:100px;}');
        $this->view->registerCss('.image-preview img {max-width:200px;}');
        $this->view->registerCss('.image-preview-wrap {position: relative;display: inline-block;max-width: 200px;margin: 10px;}');
        $this->view->registerCss('.delete-button {top: 0;position: absolute;right: 0;}');
    }

}
