<?php

namespace mata\form\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mata\form\models\Form;

/**
 * FormSearch represents the model behind the search form about `mata\form\models\Form`.
 */
class FormSearch extends Form {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['Id'], 'integer'],
            [['Name', 'ReferencedTable'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Form::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id' => $this->Id,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
        ->andFilterWhere(['like', 'ReferencedTable', $this->ReferencedTable]);

        return $dataProvider;
    }
}
