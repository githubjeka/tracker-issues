<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
/**
 * @var $this \humhub\components\View
 */

\tracker\assets\FullCalendarAsset::register($this);

$events = [];
$jsonUrl = \yii\helpers\Url::to(['/' . tracker\Module::getIdentifier() . '/calendar/issues']);
$lang = \Yii::$app->user->identity->language;
$this->registerJs("
$('#calendar').fullCalendar({
	eventLimit: true,
	locale: '$lang',
	timeFormat: 'HH:mm',
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'listDay,agendaWeek,month'
    },   
    eventRender: function( event, element, view ) {
        var title = element.find('.fc-title, .fc-list-item-title');          
        title.html(title.text());
    },  
    events: {
        url: \"$jsonUrl\",
    },
})
;")
?>

<div class="panel panel-default">
    <div class="panel-body">
        <div id='calendar'></div>
    </div>
</div>
