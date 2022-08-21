<?php

use yii\db\Migration;

/**
 * Class m220820_152404_regions
 */
class m220820_152404_regions extends Migration
{
    /*
     * {@inheritdoc}
     
    public function safeUp()
    {

    }

    
     * {@inheritdoc}
     
    public function safeDown()
    {
        echo "m220820_152404_regions cannot be reverted.\n";

        return false;
    }

    */
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('regions', array(
            'id' => $this->primaryKey(),
            'id_country' => $this->integer()->notNull(),
            'region' => $this->string(200)->notNull(),

        ));

        $this->insert('regions', [
            'id_country' => 1,
            'region' => 'Kyiv region',
        ]);

        $this->insert('regions', [
            'id_country' => 1,
            'region' => 'Kharkov region',
        ]);

        $this->insert('regions', [
            'id_country' => 2,
            'region' => 'New York',
        ]);
        $this->insert('regions', [
            'id_country' => 2,
            'region' => 'New Mexico',
        ]);

        $this->insert('regions', [
            'id_country' => 3,
            'region' => 'Central Bohemian',
        ]);

        $this->insert('regions', [
            'id_country' => 3,
            'region' => 'South Bohemian',
        ]);
    }



    public function down()
    {
        $this->dropTable('regions');
    }
}
