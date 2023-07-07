<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%merchants}}`.
 */
class m230421_063924_create_merchants_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%merchants}}', [
            'id' => $this->primaryKey(),
            'piva' => $this->string(16)->notNull(),
            'description' => $this->string(256)->notNull(),
            'address' => $this->string(256)->notNull(),
            'email' => $this->string(256)->notNull(),
            'create_date' => $this->date()->notNull(),
            'close_date' => $this->date()->defaultValue(NULL),
            'historical' => $this->integer()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%merchants}}');
    }
}
