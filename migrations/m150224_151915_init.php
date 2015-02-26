<?php

/*
 * This file is part of the mata project.
 *
 * (c) mata project <http://github.com/qi-interactive/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\db\Schema;
use yii\db\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class m150224_151915_init extends Migration {

	public function up() {
		$this->createTable('{{%mata_form}}', [
			'Id' => Schema::TYPE_PK,
			'Name' => Schema::TYPE_STRING . '(128) NOT NULL',
			'ReferencedTable'  => Schema::TYPE_STRING . '(64) NOT NULL'
			]);
	}

	public function down() {
		$this->dropTable('{{%mata_form}}');
	}
}