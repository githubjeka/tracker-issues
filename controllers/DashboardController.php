<?php

namespace tracker\controllers;

use humhub\components\Controller;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DashboardController extends Controller
{
    public function actions()
    {
        return [
            'stream' => [
                'class' => DashboardStreamAction::className(),
            ],
        ];
    }
}
