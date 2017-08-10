<?php

namespace tracker\widgets;


use humhub\libs\Html;
use tracker\Module;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class LinkToDocFileWidget extends Widget
{
    public $document;
    public $icon = '<i class="fa fa-download"></i>';
    public $text;
    public $onlyIcon = false;
    public $options = [];

    public function run()
    {
        if (!isset($this->document->id)) {
            return '';
        }

        if (empty($this->text)) {
            $this->text = \Yii::t('TrackerIssuesModule.views', 'Show the file');
        }

        $url = Url::to(['/' . Module::getIdentifier() . '/document/download', 'id' => $this->document->id,]);

        $defaultHtmlOptions = ['target' => '_blank'];
        if ($this->onlyIcon) {
            $text = $this->icon;
            $defaultHtmlOptions['title'] = $this->text;
        } else {
            $text = $this->icon . ' ' . $this->text;
        }

        $this->options = ArrayHelper::merge($defaultHtmlOptions, $this->options);

        return Html::a($text, $url, $this->options);
    }
}
