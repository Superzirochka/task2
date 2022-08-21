<?php

use yii\db\Migration;

/**
 * Class m220820_151910_countries
 */
class m220820_151910_countries extends Migration
{
    // /**
    //  * {@inheritdoc}
    //  */
    // public function safeUp()
    // {

    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function safeDown()
    // {
    //     echo "m220820_151910_countries cannot be reverted.\n";

    //     return false;
    // }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('countries', array(
            'id' => $this->primaryKey(),
            'country' => $this->string(200)->notNull(),

        ));

        $this->insert(
            'countries',
            [
                'country' => 'Ukraine',
            ]
        );
        $this->insert(
            'countries',
            [
                'country' => 'USA',
            ]
        );
        $this->insert(
            'countries',
            [
                'country' => 'Czech',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('countries');
    }
}
