<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\form\clients;

use mata\widgets\DynamicForm;

class FormClient {

	public function renderForm($model, $action = 'processForm', $fieldAttributes = [], $options = ['submitButtonText'=>'Submit']) {

		echo DynamicForm::widget([
			'model' => $model,
			'action' => $action,
			'fieldAttributes' => $fieldAttributes,
			'options' => $options,
			]);

	}
}