<?php

namespace devgroup\dropzone;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

class DropZone extends Widget
{
    public $model;
    public $attribute;
    public $htmlOptions = [];
    public $name;
    public $options = [];
    public $eventHandlers = [];
    public $url;
    public $storedFiles = [];
    public $sortable = false;
    public $sortableOptions = [];
    public $message;
    public $messageOptions = [];

    protected $dropzoneName = 'dropzone';

    public function init()
    {
        parent::init();

        Html::addCssClass($this->htmlOptions, 'dropzone');
        Html::addCssClass($this->messageOptions, 'dz-message');
        $this->dropzoneName = 'dropzone_' . $this->id;
    }

    private function registerAssets()
    {
        DropZoneAsset::register($this->getView());
        $this->getView()->registerJs('Dropzone.autoDiscover = false;');
    }

    protected function addFiles($files = [])
    {
        foreach ($files as $file) {
            $fileId = 'mockFile_' . uniqid();
            // Create the mock file:
            $this->getView()->registerJs(
                'var ' . $fileId . ' = { name: "' . $file['name'] . '", size: ' . $file['size'] . ' };'
            );
            // Call the default addedfile event handler
            $this->getView()->registerJs(
                $this->dropzoneName . '.emit("addedfile", ' . $fileId . ');'
            );
            // And optionally show the thumbnail of the file:
            $this->getView()->registerJs(
                $this->dropzoneName . '.emit("thumbnail", ' . $fileId . ', "' . $file['thumbnail'] . '");'
            );
        }
    }

    protected function decrementMaxFiles($num)
    {
        $this->getView()->registerJs(
            'if (' . $this->dropzoneName . '.options.maxFiles > 0) { '
            . $this->dropzoneName . '.options.maxFiles = '
            . $this->dropzoneName . '.options.maxFiles - ' . $num . ';'
            . ' }'
        );
    }

    protected function createDropzone()
    {
        $options = Json::encode($this->options);
        $this->getView()->registerJs($this->dropzoneName . ' = new Dropzone("#' . $this->id . '", ' . $options . ');');
    }

    public function run()
    {
        if (empty($this->name) && (!empty($this->model) && !empty($this->attribute))) {
            $this->name = Html::getInputName($this->model, $this->attribute);
        }

        if (empty($this->url)) {
            $this->url = Url::toRoute(['site/upload']);
        }

        $options = [
            'url' => $this->url,
            'paramName' => $this->name,
            'params' => [],
        ];

        if (Yii::$app->request->enableCsrfValidation) {
            $options['params'][Yii::$app->request->csrfParam] = Yii::$app->request->getCsrfToken();
        }

        if (!empty($this->message)) {
            $message = Html::tag('div', $this->message, $this->messageOptions);
        } else {
            $message = '';
        }

        $this->htmlOptions['id'] = $this->id;
        $this->options = ArrayHelper::merge($this->options, $options);
        echo Html::tag('div', $message, $this->htmlOptions);

        $this->registerAssets();

        $this->createDropzone();

        foreach ($this->eventHandlers as $event => $handler) {
            $handler = new \yii\web\JsExpression($handler);
            $this->getView()->registerJs(
                $this->dropzoneName . ".on('{$event}', {$handler})"
            );
        }

        $this->addFiles($this->storedFiles);
        $this->decrementMaxFiles(count($this->storedFiles));

        if ($this->sortable) {
            $options = Json::encode($this->sortableOptions);
            $this->getView()->registerJs("jQuery('#{$this->id}').sortable(" . $options . ");");
        }
    }
}
