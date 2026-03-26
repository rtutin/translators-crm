<?php

namespace app\controllers;

use app\models\Translator;
use app\models\TranslatorSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TranslatorsController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel  = new TranslatorSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $models = $dataProvider->getModels();
        $pagination = $dataProvider->getPagination();

        $translators = array_map(static fn(Translator $t) => [
            'id'             => $t->id,
            'full_name'      => $t->full_name,
            'language_pair'  => $t->language_pair,
            'work_schedule'  => $t->work_schedule,
            'schedule_label' => $t->getScheduleLabel(),
            'is_available'   => (bool)$t->is_available,
        ], $models);

        $sort = $dataProvider->getSort();

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'translators'  => $translators,
            'pagination'   => [
                'page'      => $pagination->getPage() + 1,
                'pageCount' => $pagination->getPageCount(),
                'pageSize'  => $pagination->pageSize,
                'totalCount'=> $pagination->totalCount,
            ],
            'sort'  => \Yii::$app->request->get('sort', ''),
        ]);
    }

    public function actionSave(): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $body = \Yii::$app->request->getBodyParams();

        $id = $body['id'] ?? null;
        $translator = $id ? $this->findModel((int)$id) : new Translator();

        // Vue передаёт is_available как boolean — приводим к int (0/1) для валидатора
        if (isset($body['is_available'])) {
            $body['is_available'] = (int)(bool)$body['is_available'];
        }

        $translator->load($body, '');

        if ($translator->save()) {
            return [
                'success' => true,
                'model'   => [
                    'id'             => $translator->id,
                    'full_name'      => $translator->full_name,
                    'language_pair'  => $translator->language_pair,
                    'work_schedule'  => $translator->work_schedule,
                    'schedule_label' => $translator->getScheduleLabel(),
                    'is_available'   => (bool)$translator->is_available,
                ],
            ];
        }

        return ['success' => false, 'errors' => $translator->errors];
    }

    public function actionDelete(int $id): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $this->findModel($id)->delete();
        return ['success' => true];
    }

    private function findModel(int $id): Translator
    {
        $model = Translator::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException("Переводчик #$id не найден.");
        }
        return $model;
    }
}
