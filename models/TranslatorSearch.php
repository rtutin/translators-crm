<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TranslatorSearch extends Translator
{
    public ?string $q = null;

    public function rules(): array
    {
        return [
            [['id', 'is_available'], 'integer'],
            [['work_schedule', 'q'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * @param array $params
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Translator::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
            'sort'       => [
                'attributes'   => [
                    'id',
                    'full_name',
                    'language_pair',
                    'work_schedule',
                    'is_available',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['is_available'  => $this->is_available]);
        $query->andFilterWhere(['work_schedule' => $this->work_schedule]);

        if ($this->q !== null && $this->q !== '') {
            $query->andWhere([
                'or',
                ['like', 'full_name',     $this->q],
                ['like', 'language_pair', $this->q],
            ]);
        }

        return $dataProvider;
    }
}
