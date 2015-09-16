<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\form\actions;

use Yii;
use mata\form\base\ValidationException;
use matacms\settings\models\Setting;
use matacms\form\models\Form;
use \Mailchimp as MailchimpApi;
use yii\base\Event;
use mata\base\MessageEvent;
use yii\web\Response;
use yii\helpers\Json;


class ProcessFormAction extends \yii\base\Action {

	public $model;
	public $formClass = \mata\form\widgets\DynamicForm::class;
	public $notify = [];
	public $notifySubject;
	public $mailChimpOptions = [];
	public $onValidationErrorHandler;
	public $onValidationSuccessHandler;
	public $onAjaxResponse;
	public $successMessage;

	public function init() {

		if(empty($this->onValidationErrorHandler)) {
			$this->onValidationErrorHandler = function($model, $exception) {
				if(\Yii::$app->request->isAjax && !empty($this->onAjaxResponse)) {
		            Yii::$app->response->format = Response::FORMAT_JSON;
		            call_user_func_array($this->onAjaxResponse, ['ERROR', $this->model->getTopError()]);
		            Yii::$app->end();
		        } else {
		        	$session = Yii::$app->getSession();
					$cacheKey = uniqid($model->tableName());
					$cacheValue = ['form' => $model->tableName(), 'model' => $model, 'hasErrors' => true, 'message' => null];
					\Yii::$app->cache->set($cacheKey, $cacheValue, 1800);
					$session->set('form_' . $model->tableName(), $cacheKey);
		        }
			};
		}

		if(empty($this->onValidationSuccessHandler)) {
			$this->onValidationSuccessHandler = function($model) {
				if(\Yii::$app->request->isAjax && !empty($this->onAjaxResponse)) {
		            Yii::$app->response->format = Response::FORMAT_JSON;
		            call_user_func_array($this->onAjaxResponse, ['OK', $this->successMessage]);
		            Yii::$app->end();
		        } else {
		        	$session = Yii::$app->getSession();
					$cacheKey = uniqid($model->tableName());
					$cacheValue = ['form' => $model->tableName(), 'model' => $model, 'hasErrors' => false, 'message' => $this->successMessage];
					\Yii::$app->cache->set($cacheKey, $cacheValue, 1800);
					$session->set('form_' . $model->tableName(), $cacheKey);
		        }
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
		$this->model->refresh();
		$body = '';

		$formModel = Form::find()->where(["ReferencedTable" => $this->model->tableName()])->one();

		foreach($this->model->attributes as $attribute => $value) {
			if($attribute == 'Id')
				continue;
			$body .= '<p>' . $this->model->getAttributeLabel($attribute) . ': <strong>' . $value . '</strong></p>';
		}

		$fromEmail = !empty(Setting::findValue('NOTIFICATION_FROM_EMAIL')) ? Setting::findValue('NOTIFICATION_FROM_EMAIL') : \Yii::$app->params['notificationEmail'];
		$fromName = !empty($formModel) ? 'New ' . $formModel->Name . ' form submission' : \Yii::$app->name . ' notification';

		$recipients = (is_array($this->notify)) ? $this->notify : [$this->notify];
		$subject = (!empty($this->notifySubject)) ? $this->notifySubject : 'New Form Submission ' . \Yii::$app->name;
		foreach ($recipients as $recipient) {
			\Yii::$app->mailer->compose()
			->setFrom([$fromEmail => $fromName])
			->setTo($recipient)
			->setSubject($subject)
			->setHtmlBody($body)
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

		} catch (\Mailchimp_Error $e) {
			$this->model->addError($this->mailChimpOptions['modelEmailAttributeName'], $e->getMessage());
			throw new ValidationException();
		}

		return $this;
	}
}
