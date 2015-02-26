<?php

namespace mata\form\controllers;

use mata\form\models\Form;
use mata\form\models\FormSearch;
use matacms\controllers\module\Controller;

/**
 * FormController implements the CRUD actions for Form model.
 */
class FormController extends Controller {

	public function getModel() {
		return new Form();
	}

	public function getSearchModel() {
		return new FormSearch();
	}

}