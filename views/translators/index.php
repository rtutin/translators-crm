<?php

/** @var yii\web\View $this */
/** @var app\models\TranslatorSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $translators */
/** @var array $pagination */
/** @var string $sort */

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->title = 'Переводчики';
?>

<div class="translators-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="mb-3">
        <?= Html::a('Все',              ['translators/index'],                                         ['class' => 'btn btn-sm ' .      (empty($searchModel->work_schedule) && $searchModel->is_available === null ? 'btn-primary' : 'btn-outline-secondary')]) ?>
        <?= Html::a('Свободные',        ['translators/index', 'TranslatorSearch[is_available]' => 1], ['class' => 'btn btn-sm ms-1 ' . ($searchModel->is_available == 1 && empty($searchModel->work_schedule) ? 'btn-primary' : 'btn-outline-secondary')]) ?>
        <?= Html::a('Будни (Пн–Пт)',    ['translators/index', 'TranslatorSearch[work_schedule]' => 'weekday', 'TranslatorSearch[is_available]' => 1], ['class' => 'btn btn-sm ms-1 ' . ($searchModel->work_schedule === 'weekday' ? 'btn-primary' : 'btn-outline-secondary')]) ?>
        <?= Html::a('Выходные (Сб–Вс)', ['translators/index', 'TranslatorSearch[work_schedule]' => 'weekend', 'TranslatorSearch[is_available]' => 1], ['class' => 'btn btn-sm ms-1 ' . ($searchModel->work_schedule === 'weekend' ? 'btn-primary' : 'btn-outline-secondary')]) ?>
    </div>

    <div id="translators-app">
        <div class="text-muted py-3">Загрузка...</div>
    </div>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    window.__TRANSLATORS__  = <?= Json::encode($translators, JSON_UNESCAPED_UNICODE) ?>;
    window.__PAGINATION__   = <?= Json::encode($pagination, JSON_UNESCAPED_UNICODE) ?>;
    window.__SORT__         = '<?= $sort ?>';
    window.__SAVE_URL__     = '<?= Url::to(['translators/save']); ?>';
    window.__DELETE_URL__   = '<?= Url::to(['translators/delete']); ?>';
</script>
<?php $this->registerJsFile('@web/js/translators-app.js', ['depends' => []]); ?>
