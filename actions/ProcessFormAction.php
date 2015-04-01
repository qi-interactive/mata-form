<?php

namespace mata\form\actions;

use Yii;
use mata\form\base\ValidationException;
use \Mailchimp as MailchimpApi;
use yii\base\Event;
use mata\base\MessageEvent;

class ProcessFormAction extends \yii\base\Action {

	public $model;
	public $formClass = \mata\form\widgets\DynamicForm::class;
	public $notify = [];
	public $mailChimpOptions = [];
	public $onValidationErrorHandler;
	public $onValidationSuccessHandler;
	public $onSuccess;
	public $successMessage;

	public function init() {

		if(empty($this->onValidationErrorHandler)) {
			$this->onValidationErrorHandler = function($model, $exception) {
				$session = Yii::$app->getSession();
				$cacheKey = uniqid($model->tableName());
				$cacheValue = ['form' => $model->tableName(), 'model' => $model, 'hasErrors' => true, 'message' => null];
				\Yii::$app->cache->set($cacheKey, $cacheValue, 1800); 
				$session->set('form_' . $model->tableName(), $cacheKey);
			};
		}

		if(empty($this->onValidationSuccessHandler)) {
			$this->onValidationSuccessHandler = function($model) {
				$session = Yii::$app->getSession();
				$cacheKey = uniqid($model->tableName());
				$cacheValue = ['form' => $model->tableName(), 'model' => $model, 'hasErrors' => false, 'message' => $this->successMessage];
				\Yii::$app->cache->set($cacheKey, $cacheValue, 1800); 
				$session->set('form_' . $model->tableName(), $cacheKey);
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
				call_user_func_array($this->onValidationSuccessHandler, [$this->model]);
			}

		} catch (ValidationException $e) {
			call_user_func_array($this->onValidationErrorHandler, [$this->model, $e]);

		}
		return $this->controller->redirect(Yii::$app->request->referrer);
		
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
		try {
			$mailChimpAPI = new MailchimpApi($apiKey);
			$mailChimpAPI->lists->subscribe($listId, 
				['email' => $email]
			);
			
		} catch (\Mailchimp_List_AlreadySubscribed $e) {
			$this->model->addError($this->mailChimpOptions['modelEmailAttributeName'], $e->getMessage());
			throw new ValidationException();
		}

		return $this;
	}


}  