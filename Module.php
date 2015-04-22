<?php

/*
 * This file is part of the mata project.
 *
 * (c) mata project <http://github.com/qi-interactive/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mata\form;

use mata\base\Module as BaseModule;

/**
 * This is the main module class for the Yii2-user.
 *
 * @property array $modelMap
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Module extends BaseModule {

	public $runBootstrap = true;

	public function getNavigation() {
		$forms = \mata\form\models\Form::find()->all();
		$navigation = [];
		foreach ($forms as $form) {
			$navigation[] = [
				'label' => $form->getLabel(),
				'url' => "/mata-cms/form/form/submissions?id=$form->Id",
				'icon' => "/images/module-icon.svg"
			];
		}
		
		return $navigation;


		// return "/mata-cms/form/form";
	}
}