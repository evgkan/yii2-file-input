<?php

namespace sgdot\widgets;

use yii\helpers\Html;
use yii\helpers\Json;
use mihaildev\elfinder\AssetsCallBack;

class InputFile extends \mihaildev\elfinder\InputFile {

    public $callbackFunction;

    /**
     * Runs the widget.
     */
    public function run() {
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::textInput($this->name, $this->value, $this->options);
        }

        $replace['{button}'] = Html::button($this->buttonName, $this->buttonOptions);

        echo strtr($this->template, $replace);

        AssetsCallBack::register($this->getView());

        if (!empty($this->callbackFunction)) {
            $this->getView()->registerJs("mihaildev.elFinder.register(" . Json::encode($this->options['id']) . "," . Json::encode($this->callbackFunction) . ");$('#" . $this->buttonOptions['id'] . "').click(function(){ElFinderFileCallback.openManager(" . Json::encode($this->_managerOptions) . ");});");
        } else {
            $this->getView()->registerJs("mihaildev.elFinder.register(" . Json::encode($this->options['id']) . ", function(file, id){ \$('#' + id).val(file.url); return true;});$('#" . $this->buttonOptions['id'] . "').click(function(){ElFinderFileCallback.openManager(" . Json::encode($this->_managerOptions) . ");});");
        }
    }

}
