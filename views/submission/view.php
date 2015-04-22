<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mata\contentblock\models\ContentBlock */

$this->title = $formModel->Name . ' - ' . $model->getLabel();
$this->params['breadcrumbs'][] = ['label' => 'Content Blocks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div><?= Html::a("Back to list view", sprintf("/mata-cms/%s/%s/list?id=%d", $this->context->module->id, $this->context->id, $formModel->Id), ['id' => 'back-to-list-view']);?></div>

<div class="content-block-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        // 'attributes' => [
        //     'Id',
        //     'Title:ntext',
        //     'Text:ntext',
        //     'Region',
        // ],
    ]) ?>

</div>
