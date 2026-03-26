<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TranslatorSearch extends Translator
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'is_available'], 'integer'],
            [['full_name', 'language_pair', 'work_schedule'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
                'defaultOrder' => ['full_name' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['is_available'  => $this->is_available]);
        $query->andFilterWhere(['work_schedule' => $this->work_schedule]);
        $query->andFilterWhere(['like', 'full_name',     $this->full_name]);
        $query->andFilterWhere(['like', 'language_pair', $this->language_pair]);

        return $dataProvider;
    }
}
