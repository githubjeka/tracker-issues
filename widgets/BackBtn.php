<?php

namespace tracker\widgets;


use yii\base\Widget;
use yii\helpers\Html;

/**
 * Widget to provide back button by URL referrer or by alternative Url if referrer is incorrect.
 * Referrer URL is incorrect in if it's Null or it like as current URL by controller/action
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class BackBtn extends Widget
{
    public $alternativeUrl = ['/'];
    public $icon = '<i class="fa fa-2x fa-arrow-left fa-pull-left"></i>';

    public function run()
    {
        // by default URL is alternative
        $url = $this->alternativeUrl;

        $request = \Yii::$app->request;
        $referrer = $request->getReferrer();

        if ($referrer !== null) {
            // need compare referrer URL with current URL
            if (preg_match('/^http/', $referrer)) {
                $current = $request->getAbsoluteUrl();
            } else {
                $current = $request->getUrl();
            }

            // find GET params and remove it for compare
            $endReferrer = !($endReferrer = stripos($referrer, '?')) ? null : $endReferrer;
            $endCurrent = !($endCurrent = stripos($current, '?')) ? null : $endCurrent;

            // compare, if diff then URL is referrer
            if (substr($referrer, 0, $endReferrer) !== substr($current, 0, $endCurrent)) {
                $url = $referrer;
            }
        }

        return Html::a($this->icon, $url);
    }
}
