<?php

use yii\helpers\Html;
use matacms\theme\simple\assets\ListAsset;

ListAsset::register($this);

$moduleBaseUrl = sprintf("/mata-cms/%s/%s", $this->context->module->id, $this->context->id);

?> 

<div class="list-container row">
	<a href='<?= sprintf("%s/details?formId=%d&submissionId=%d", $moduleBaseUrl, $formModel->Id, $model->primaryKey);?>' class="list-link">
			<div class="list-contents-container">
				<div class="list-label"> 
					<span class='item-label'><?= $model->getLabel();?></span>
				</div>
			</div>
		</a>
		<a class='delete-btn' href="<?= sprintf("%s/delete-submission?formId=%d&submissionId=%d", $moduleBaseUrl, $formModel->Id, $model->primaryKey );?>" <?php if(method_exists($model, 'canBeDeleted')) {
			echo "data-delete-allowed=\"" . var_export($model->canBeDeleted(), true) . "\"";
			if(!$model->canBeDeleted()) {
				echo " data-delete-alert=\"" . $model->deleteAlertMessage() . "\"";
			}
		}
		?>></a>
	</div>




