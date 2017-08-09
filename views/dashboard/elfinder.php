<?php
/** @var \humhub\components\View $this */
//\yii\web\JqueryAsset::register($this);
?>
<?= alexantr\elfinder\ElFinder::widget([
    'id'=>'elfinder-vts',
    'connectorRoute' => ['dashboard/connector'],
    'settings' => [
        'height' => 640,
    ],
    'buttonNoConflict' => true,
]) ?>
<?php
$this->registerJs('
var elfinder = $("#elfinder-vts").elfinder("instance");
elfinder.bind("upload", function(event) { console.log(event); alert("123"); });
')
?>
