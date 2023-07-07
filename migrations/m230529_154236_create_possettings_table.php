<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%possettings}}`.
 */
class m230529_154236_create_possettings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%possettings}}', [
            'id' => $this->primaryKey(),
            'pos_id' => $this->integer()->notNull(),
            'sin' => $this->string(255)->defaultValue(null),

            'appName' => $this->string(30)->defaultValue(null),
            'title' => $this->string(30)->defaultValue(null),
            'description' => $this->string(512)->defaultValue(null),
            'template' => $this->json()->defaultValue(null),
            'defaultView' => $this->string(512)->defaultValue(null),
            'currency' => $this->string(512)->defaultValue(null),
            'showCustomAmount' => $this->boolean()->defaultValue(null),
            'showDiscount' => $this->boolean()->defaultValue(null),
            'enableTips' => $this->boolean()->defaultValue(null),
            'customAmountPayButtonText' => $this->string(512)->defaultValue(null),
            'fixedAmountPayButtonText' => $this->string(512)->defaultValue(null),
            'tipText' => $this->string(512)->defaultValue(null),
            'customCSSLink' => $this->string(512)->defaultValue(null),
            'embeddedCSS' => $this->string(512)->defaultValue(null),
            'notificationUrl' => $this->string(512)->defaultValue(null),
            'redirectUrl' => $this->string(512)->defaultValue(null),
            'redirectAutomatically' => $this->boolean()->defaultValue(null),
            'requiresRefundEmail' => $this->boolean()->defaultValue(null),
            'checkoutType' => $this->string(512)->defaultValue(null),
            'formId' => $this->string(512)->defaultValue(null),
        ]);

        $this->createIndex('{{%idx-possettings-pos_id}}', '{{%possettings}}', 'pos_id');
        $this->addForeignKey(
            '{{%fk-possettings-pos_id}}',
            '{{%possettings}}',
            'pos_id',
            '{{%pos}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-possettings-pos_id}}', '{{%possettings}}');
        $this->dropIndex('{{%idx-possettings-pos_id}}', '{{%possettings}}');
        $this->dropTable('{{%possettings}}');
    }
}
