<?php

use yii\db\Migration;

/**
 * Class m220820_152430_cities
 */
class m220820_152430_cities extends Migration
{
    /**
     * {@inheritdoc}
     */
    // public function safeUp()
    // {

    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function safeDown()
    // {
    //     echo "m220820_152430_cities cannot be reverted.\n";

    //     return false;
    // }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('cities', array(
            'id' => $this->primaryKey(),
            'id_region' => $this->integer()->notNull(),
            'city' => $this->string(200)->notNull(),
        ));


        $this->insert('cities', [
            'id_region' => 1,
            'city' => 'Kyiv',
        ]);

        $this->insert('cities', [
            'id_region' => 1,
            'city' => 'Irpen',
        ]);

        $this->insert('cities', [
            'id_region' => 2,
            'city' => 'Kharkov',
        ]);

        $this->insert('cities', [
            'id_region' => 2,
            'city' => 'Volchansk',
        ]);

        $this->insert('cities', [
            'id_region' => 3,
            'city' => 'New York',
        ]);

        $this->insert('cities', [
            'id_region' => 4,
            'city' => 'New Mexico',
        ]);

        $this->insert('cities', [
            'id_region' => 5,
            'city' => 'Kolín',
        ]);
        $this->insert('cities', [
            'id_region' => 5,
            'city' => 'Nymburk',
        ]);

        $this->insert('cities', [
            'id_region' => 6,
            'city' => 'Písek',
        ]);
        $this->insert('cities', [
            'id_region' => 6,
            'city' => 'Tábor',
        ]);
    }

    public function down()
    {
        $this->dropTable('cities');
    }
}
