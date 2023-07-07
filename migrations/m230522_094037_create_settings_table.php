<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m230522_094037_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'description' => $this->string(256)->notNull(),
            'code' => $this->string(256)->notNull(),
            'value' => $this->string(2048)
        ]);

        $this->insert('settings', [
            'description' => 'Public Key Derivation Scheme',
            'code' => 'derivationScheme',
            'value' => '',
        ]);
        $this->insert('settings', [
            'description' => 'Public Key Derivation Label',
            'code' => 'derivationLabel',
            'value' => '',
        ]);
        $this->insert('settings', [
            'description' => 'Public Key Account Key Path',
            'code' => 'derivationAccountKeyPath',
            'value' => '',
        ]);
        $this->insert('settings', [
            'description' => 'Btcpayserver API Key',
            'code' => 'btcpayApiKey',
            'value' => '',
        ]);
        $this->insert('settings', [
            'description' => 'Btcpayserver host url',
            'code' => 'btcpayHost',
            'value' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
