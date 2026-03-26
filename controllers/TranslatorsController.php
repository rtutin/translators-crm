<?php

namespace app\controllers;

use app\models\Translator;
use yii\web\Controller;

class TranslatorsController extends Controller
{
    public function actionIndex(): string
    {
        $day = \Yii::$app->request->get('day', 'all');

        switch ($day) {
            case 'available':
                $translators = Translator::findAvailable();
                break;
            case 'weekday':
                $translators = Translator::findForWeekday();
                break;
            case 'weekend':
                $translators = Translator::findForWeekend();
                break;
            default:
                $translators = Translator::find()->all();
                $day = 'all';
        }

        return $this->render('index', [
            'translators' => $translators,
            'day'         => $day,
        ]);
    }
}
