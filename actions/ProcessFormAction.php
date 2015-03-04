<?php

namespace mata\form\actions;

use Yii;
use sammaye\mailchimp\Mailchimp;

class ProcessFormAction extends \yii\base\Action {

	public $model;
	public $notify = [];
	public $mailChimpOptions = [];
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
				$this->sendNotifications()->subscribeToMailChimpList();

				// Add Thank you message (?)
			} catch (\Exception $e) {
				throw $e;
			}			
		}
		return $this->controller->redirect(!empty($this->redirect) ? $this->redirect : Yii::$app->request->referrer);
		
	}

	protected function sendNotifications() {
		if(!empty($this->notify)) {
			$recipients = (is_array($this->notify)) ? $this->notify : [$this->notify];
			foreach ($recipients as $recipient) {
				\Yii::$app->mailer->compose()
				->setFrom([\Yii::$app->params['adminEmail'] => \Yii::$app->name . ' notification'])
				->setTo($recipient)
				->setSubject('New Form Submission ' . \Yii::$app->name)
				->setTextBody('body')
				->send();
			}
		}
		return $this;
	}

	protected function subscribeToMailChimpList() {
		if(!empty($this->mailChimpOptions)) {
			$emailAttribute = $this->mailChimpOptions['modelEmailAttributeName'];
			$mailChimpAPI = new \sammaye\mailchimp\Mailchimp(['apikey' => $this->mailChimpOptions['apiKey']]);
			$mailChimpAPI->lists->subscribe(
				$this->mailChimpOptions['listId'], 
				['email' => $this->model->$emailAttribute]
				);
		}
		return $this;
	}


}  