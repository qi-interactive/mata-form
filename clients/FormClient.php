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
			'options' => $options,
	      	// 'enableClientValidation' => false

			]);

		// TO BE REMOVED LATER
		
		// $form = DynamicForm::begin([
		// 	'model' => $model,
		// 	'renderFields' => false,
		// 	// 'enableClientScript' => false,
		// 	'action' => $action,
		// 	'fieldAttributes' => $fieldAttributes,
		// 	'options' => $options,
		// 	'fieldConfig' => [
		// 	'template' => "{label}\n<div class=\"eight columns\">{input}</div>\n<div class=\"col-sm-offset-4 col-lg-8\">{error}\n{hint}</div>",
		// 	'labelOptions' => ['class' => 'four columns control-label'],
		// 	],
		// 	]);

		// echo $form->generateActiveField('Name');

		// DynamicForm::end();

	}

}