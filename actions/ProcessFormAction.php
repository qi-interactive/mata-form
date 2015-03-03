<?php

namespace mata\form\actions;

use Yii;

class ProcessFormAction extends \yii\base\Action {

	public $model;
	public $notify = [];
	public $redirect;

	public function run() {

		$isDynamicModel = is_a($this->model, 'mata\base\DynamicModel');
		// Load data and validate
		if($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
			// Save to database
			try {
				if(!$this->model->save()) {
					throw new NotFoundHttpException('The requested page does not exist.');
				}
				$this->sendNotifications($this->notify);

				// Add Thank you message (?)
			} catch (\Exception $e) {
				throw $e;
			}			
		}
		return $this->controller->redirect(!empty($this->redirect) ? $this->redirect : Yii::$app->request->referrer);
		
		// $revisions = Revision::find()->where([
		// 	"DocumentId" => $documentId
		// 	])->orderBy("Revision DESC")->all();


		// return $this->controller->render($this->view ?: $this->id, [
		// 	"revisions" => $revisions
		// 	]);
	}

	protected function sendNotifications($recipients) {
		if(empty($recipients))
			return;

		$recipients = (is_array($recipients)) ? $recipients : [$recipients];
		foreach ($recipients as $recipient) {
			\Yii::$app->mailer->compose()
                    ->setFrom([\Yii::$app->params['adminEmail'] => \Yii::$app->name . ' notification'])
                    ->setTo($recipient)
                    ->setSubject('New Form Submission ' . \Yii::$app->name)
                    ->setTextBody('body')
                    ->send();
		}
	}


}  