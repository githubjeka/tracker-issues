<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

namespace tracker\widgets;

use tracker\models\DocumentCategory;
use yii\base\Widget;
use yii\helpers\Html;

class DocumentCategoryLabel extends Widget
{
    /**
     * @var DocumentCategory|null
     */
    public $category;

    public function run()
    {
        if (!($this->category instanceof DocumentCategory)) {
            return '';
        }

        return Html::tag(
            'strong',
            Html::encode($this->category->name),
            ['class' => 'text-uppercase', 'style' => 'color:' . Html::encode($this->category->color)]
        );
    }
}
