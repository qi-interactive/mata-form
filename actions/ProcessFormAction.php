<?php

namespace mata\form\actions;

use Yii;
use mata\form\base\ValidationException;
use \Mailchimp as MailchimpApi;

class ProcessFormAction extends \yii\base\Action {

	public $model;
	public $notify = [];
	public $mailChimpOptions = [];
	public $redirect = true;
	public $onValidationErrorHandler;

	public function init() {
		if(empty($this->onValidationErrorHandler)) {
			$this->onValidationErrorHandler = function($model, $exception) {
				throw $exception;
			};
		}
		
	}

	public function run() {
		// Load data and validate
		try {
			if($this->model->load(Yii::$app->request->post()) && $this->isDataValid()) {
				// Save to database
				if(!$this->model->save()) {
					throw new NotFoundHttpException('The requested page does not exist.');
				}
				$this->subscribeToMailChimpList()->sendNotifications();
				// Add Thank you message (?)
			}

		} catch (ValidationException $e) {
			call_user_func_array($this->onValidationErrorHandler, [$this->model, $e]);
		}

		if ($this->redirect != false)
			return $this->controller->redirect(!empty($this->redirect) ? $this->redirect : Yii::$app->request->referrer);
		
	}

	public function isDataValid() {
		if(!$this->model->validate())
			throw new ValidationException();
		return true;
	}

	protected function sendNotifications() {
		$recipients = (is_array($this->notify)) ? $this->notify : [$this->notify];
		foreach ($recipients as $recipient) {
			\Yii::$app->mailer->compose()
			->setFrom([\Yii::$app->params['adminEmail'] => \Yii::$app->name . ' notification'])
			->setTo($recipient)
			->setSubject('New Form Submission ' . \Yii::$app->name)
			->setTextBody('body')
			->send();
		}
		return $this;
	}

	protected function subscribeToMailChimpList() {
		if(!empty($this->mailChimpOptions)) {
			$emailAttribute = $this->mailChimpOptions['modelEmailAttributeName'];
			$this->subscribeToMailChimpListInternal($this->mailChimpOptions['apiKey'], $this->mailChimpOptions['listId'], $this->model->$emailAttribute);
		}
		return $this;
	}

	protected function subscribeToMailChimpListInternal($apiKey, $listId, $email) {
		$mailChimpAPI = new MailchimpApi($apiKey);
		$mailChimpAPI->lists->subscribe($listId, 
			['email' => $email]
			);
		return $this;
	}


}  