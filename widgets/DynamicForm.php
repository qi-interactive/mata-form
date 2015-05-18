<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\form\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use ReflectionClass;
use yii\helpers\ArrayHelper;
use yii\base\Event;
use mata\base\MessageEvent;
use yii\web\View;

class DynamicForm extends \mata\widgets\DynamicForm {

    public $model;
    public $action = '';
    public $fieldAttributes = [];
    public $omitId = true;
    public $autoRenderFields = true;
    public $hasSuccessMessage = false;
    public $ajaxSubmit = false;
    public $onAjaxSubmitResponse;
    private $modelAttributes;

	public function init()
	{
		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->getId();
		}

        $this->options = ArrayHelper::merge([
            "submitButtonText" => "Submit"
            ], $this->options);

        $this->checkValidationResult();


        if(!$this->hasSuccessMessage)
            echo Html::beginForm($this->action, $this->method, $this->options);
    }
    
    protected function checkValidationResult()
    {
        $session = Yii::$app->getSession();

        $cacheKey = $session->get('form_' . $this->model->tableName());
        $cacheValue = \Yii::$app->cache->get($cacheKey);
        if(!empty($cacheValue)) {

            if(!$cacheValue['hasErrors'] && $cacheValue['message']) {
                $this->hasSuccessMessage = true;
                echo $cacheValue['message'];
            } else {
                $this->model = $cacheValue['model'];
            }

            // Remove cache and session
            \Yii::$app->cache->delete($cacheKey);
            $session->remove($cacheKey);
        }
        
    }

    /**
     * Runs the widget.
     * This registers the necessary javascript code and renders the form close tag.
     * @throws InvalidCallException if `beginField()` and `endField()` calls are not matching
     */
    public function run()
    {
    	if (!empty($this->_fields)) {
    		throw new InvalidCallException('Each beginField() should have a matching endField() call.');
    	}

        // Set custom attribute labels
        if(!empty($this->fieldAttributes)) {
            $attributeLabels = $this->model->attributeLabels();
            foreach($this->fieldAttributes as $fieldName => $fieldAttribute) {
                if(array_key_exists($fieldName, $attributeLabels) && isset($fieldAttribute['label'])) {
                    $this->model->setAttributeLabel($fieldName, $fieldAttribute['label']);
                }
            }
        }

        $modelClass = (new ReflectionClass($this->model))->getName();

        $this->modelAttributes = $this->model->attributes;
        // Remove Id from model attributes
        if($this->omitId) {
            if(array_key_exists('Id', $this->modelAttributes)) {
                unset($this->modelAttributes['Id']);
            }
        }

        // Generate fields
        if($this->autoRenderFields) {
            foreach($this->modelAttributes as $fieldName => $fieldValue)
                echo $this->generateActiveField($fieldName);
        }        
        

        if ($this->enableClientScript) {
            $id = $this->options['id'];
            $options = Json::encode($this->getClientOptions());
            $attributes = Json::encode($this->attributes);
            $view = $this->getView();
            \yii\widgets\ActiveFormAsset::register($view);
            $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
        }

        if($this->autoRenderFields)
            echo $this->submitBtns();

        if(!$this->hasSuccessMessage)
            echo Html::endForm();

        if($this->ajaxSubmit) {
            \Yii::$app->view->registerJs("
                $('#" . $this->id . "').on('beforeSubmit', function(event, jqXHR, settings) {
                    var form = $(this);
                    if(form.find('.has-error').length) {
                        return false;
                    }
                    
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        dataType: 'json',
                        success: function(data) {
                            $this->onAjaxSubmitResponse
                        }
                    });
                
                    return false;
                });", View::POS_READY);
        }
    }
}
