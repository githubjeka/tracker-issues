<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

namespace tracker\widgets;

use tracker\models\DocumentType;
use yii\base\Widget;
use yii\helpers\Html;

class DocumentTypeLabel extends Widget
{
    /**
     * @var DocumentType|null
     */
    public $type;

    public function run()
    {
        if (!($this->type instanceof DocumentType)) {
            return '';
        }

        return Html::tag(
            'span',
            Html::encode($this->type->name),
            ['class' => 'label label-default', 'style' => 'background-color:' . Html::encode($this->type->color)]
        );
    }
}
