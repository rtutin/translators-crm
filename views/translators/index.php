<?php

/** @var yii\web\View $this */
/** @var app\models\Translator[] $translators */
/** @var string $day */

use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Переводчики';

$translatorsJson = Json::encode(array_map(static function ($t) {
    return [
        'id'             => $t->id,
        'full_name'      => $t->full_name,
        'language_pair'  => $t->language_pair,
        'work_schedule'  => $t->work_schedule,
        'schedule_label' => $t->getScheduleLabel(),
        'is_available'   => (bool)$t->is_available,
    ];
}, $translators));
?>

<div class="translators-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="mb-3">
        <?= Html::a('Все',              ['translators/index'],                       ['class' => 'btn btn-sm ' .      ($day === 'all'       ? 'btn-primary' : 'btn-outline-secondary')]) ?>
        <?= Html::a('Свободные',        ['translators/index', 'day' => 'available'], ['class' => 'btn btn-sm ms-1 ' . ($day === 'available' ? 'btn-primary' : 'btn-outline-secondary')]) ?>
        <?= Html::a('Будни (Пн–Пт)',    ['translators/index', 'day' => 'weekday'],   ['class' => 'btn btn-sm ms-1 ' . ($day === 'weekday'   ? 'btn-primary' : 'btn-outline-secondary')]) ?>
        <?= Html::a('Выходные (Сб–Вс)', ['translators/index', 'day' => 'weekend'],   ['class' => 'btn btn-sm ms-1 ' . ($day === 'weekend'   ? 'btn-primary' : 'btn-outline-secondary')]) ?>
    </div>

    <div id="translators-app">
        <translators-list :initial-translators="initialTranslators" />
    </div>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    window.__TRANSLATORS__ = <?= $translatorsJson ?>;
</script>
<?php $this->registerJsFile('@web/js/translators-app.js', ['depends' => []]); ?>
