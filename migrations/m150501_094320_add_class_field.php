<?php

/*
 * This file is part of the mata project.
 *
 * (c) mata project <http://github.com/mata/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\db\Schema;
use yii\db\Migration;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class m150501_094320_add_class_field extends Migration
{
    
    public function up()
    {
        $this->addColumn('{{%mata_form}}', 'Class', 'VARCHAR(255) NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%mata_form}}', 'Class');
    }

}
