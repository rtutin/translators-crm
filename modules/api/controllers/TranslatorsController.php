<?php

namespace app\modules\api\controllers;

use app\models\Translator;
use yii\rest\Controller;
use yii\web\Response;

/**
 *   GET /api/translators/status
 *   GET /api/translators/list?day=weekday|weekend
 */
class TranslatorsController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actionStatus(): string
    {
        $available = Translator::findAvailable();

        return count($available) > 0
                ? 'Список переводчиков готов'
                : 'Нет свободных переводчиков';
    }

    public function actionList(): array
    {
        $day = \Yii::$app->request->get('day', 'weekday');

        $translators = ($day === 'weekend')
            ? Translator::findForWeekend()
            : Translator::findForWeekday();

        if (empty($translators)) {
            return [];
        }

        $data = array_map(static function (Translator $t) {
            return [
                'id'            => $t->id,
                'full_name'     => $t->full_name,
                'language_pair' => $t->language_pair,
                'work_schedule' => $t->work_schedule,
                'schedule_label'=> $t->getScheduleLabel(),
                'is_available'  => (bool)$t->is_available,
            ];
        }, $translators);

        return $data;
    }
}
