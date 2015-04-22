<?php

namespace mata\form\controllers;

use mata\form\models\Form;
use mata\form\models\FormSearch;
use matacms\controllers\module\Controller;
use yii\helpers\Json;
use mata\db\DynamicActiveDataProvider;
use matacms\base\MessageEvent;

/**
 * FormController implements the CRUD actions for Form model.
 */
class SubmissionController extends Controller {

	public function getModel() {
		return new Form;
	}
	public function getSearchModel() {
		return new FormSearch;
	}

	public function actionList($id) {

		$formModel = \mata\form\models\Form::findOne($id);

		$dynamicModel = new \mata\db\DynamicActiveRecord($formModel->ReferencedTable);

		$dataProvider = $this->getSearchModelForDynamicActiveRecord($dynamicModel, $formModel, \Yii::$app->request->queryParams);


		return $this->render("index", [
			'dataProvider' => $dataProvider,
			'formModel' => $formModel
			]);
	}

	public function actionDetails($formId, $submissionId) {

		$formModel = \mata\form\models\Form::findOne($formId);

		$dynamicModel = new \mata\db\DynamicActiveRecord($formModel->ReferencedTable);

		$submission = $dynamicModel->findOne($submissionId);

		return $this->render("view", [
			'model' => $submission,
			'formModel' => $formModel
			]);
	}

	public function actionDeleteSubmission($formId, $submissionId) {

		$formModel = \mata\form\models\Form::findOne($formId);

		$dynamicModel = new \mata\db\DynamicActiveRecord($formModel->ReferencedTable);

		$submission = $dynamicModel->findOne($submissionId);

		$this->trigger(parent::EVENT_MODEL_DELETED, new MessageEvent($formModel->Name ." <strong>".$submission->getLabel()."</strong> has been <strong>deleted</strong>."));
		$submission->delete();

		return $this->redirect(['list?id=' . $formId]);
	}

	protected function getSearchModelForDynamicActiveRecord($model, $formModel, $params) {

        $query = $model->find();

        $dataProvider = new DynamicActiveDataProvider([
            'query' => $query,
            'tableName' => $formModel->tableName,
            'sort' => false
        ]);

        $model->load($params);

        if (!$model->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

  //       if(!empty($model->Extra)) {
		// 	$settings = Json::decode($model->Extra);
		// 	if(isset($settings['filterBy']) && !empty($settings['filterBy'])) {
		// 		foreach($settings['filterBy'] as $filterBy) {
		// 			// $query->andFilterWhere(['like', 'Name', $this->Name])
		// 			// var_dump($filterBy);
		// 		}
		// 	}

		// }

        return $dataProvider;
	}

}
