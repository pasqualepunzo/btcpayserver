<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%webhooks}}`.
 */
class m230619_133128_create_webhooks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%webhooks}}', [
            'id' => $this->primaryKey(),
            'store_id' => $this->integer()->notNull(),
            'bps_storeid' => $this->string(512)->notNull(),
            'webhookId' => $this->string(512)->notNull(),
            'url' => $this->string(512)->notNull()
        ]);

        $this->createIndex('{{%idx-webhooks-store_id}}', '{{%webhooks}}', 'store_id');
        $this->addForeignKey(
            '{{%fk-webhooks-store_id}}',
            '{{%webhooks}}',
            'store_id',
            '{{%stores}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Aggiorna settings con un secret
        $this->insert('settings', [
            'description' => 'WebHook secret for callback',
            'code' => 'webhookSecret',
            'value' => \Yii::$app->security->generateRandomString(),
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(
            'settings',
            ['code' => 'webhookSecret'] // Condition to match the rows to be updated
        );

        $this->dropForeignKey('{{%fk-webhooks-store_id}}', '{{%webhooks}}');
        $this->dropIndex('{{%idx-webhooks-store_id}}', '{{%webhooks}}');

        $this->dropTable('{{%webhooks}}');
    }
}
