Dropzone Extension for Yii 2
==============================

This extension provides the [Dropzone](http://www.dropzonejs.com/) integration for the Yii2 framework.


Installation
------------

This extension requires [Dropzone](https://github.com/enyo/dropzone)

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist devgroup/yii2-dropzone "*"
```

or add

```
"devgroup/yii2-dropzone": "*"
```

to the require section of your composer.json.


General Usage
-------------

```php
use devgroup\dropzone\DropZone;

DropZone::widget(
    [
        'name' => 'file', // input name or 'model' and 'attribute'
        'url' => '', // upload url
        'storedFiles' => [], // stores files
        'eventHandlers' => [], // dropzone event handlers
        'sortable' => true, // sortable flag
        'sortableOptions' => [], // sortable options
        'htmlOptions' => [], // container html options
        'options' => [], // dropzone js options
    ]
)
```

you can also register `devgroup\dropzone\UploadAction` and `devgroup\dropzone\RemoveAction` actions in your controller
