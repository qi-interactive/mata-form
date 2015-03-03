<?php

namespace mata\form\clients;

use mata\widgets\DynamicForm;


class FormClient {

	public function renderForm($model, $action = 'processForm', $fieldAttributes = [], $options = ['submitButtonText'=>'Submit']) {

		echo DynamicForm::widget([
			'model' => $model,
			// 'enableClientScript' => false,
			'action' => $action,
			'fieldAttributes' => $fieldAttributes,
			'options' => $options
			]);

	}

}