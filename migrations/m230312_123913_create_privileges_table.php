<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%privileges}}`.
 */
class m230312_123913_create_privileges_table extends Migration
{
    /**
     * ### Ruoli utente dal README
     * 
     * 1 - Webmaster           50  => Full control su applicazione
     * 3 - Administrator       40  => Full control sui dati di tutti gli esercenti
     * 4 - Senior              30  => Visualizza tutti i propri negozi/pos/invoices
     * 2 - Junior               0  => Visualizza solo il negozio/pos/invoice assegnati
     */

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%privileges}}', [
            'id' => $this->primaryKey(),
            'description' => $this->string(50)->notNull(),
            'level' => $this->integer()->notNull(),
            'codice_ruolo' => $this->string(50)->notNull(),
        ]);

        $this->insert('privileges', [
            'description' => 'Webmaster',
            'level' => 50,
            'codice_ruolo' => 'WEBMASTER'
        ]);

        $this->insert('privileges', [
            'description' => 'Junior',
            'level' => 0,
            'codice_ruolo' => 'JUNIOR'
        ]);

        $this->insert('privileges', [
            'description' => 'Administrator',
            'level' => 40,
            'codice_ruolo' => 'ADMINISTRATOR'
        ]);

        $this->insert('privileges', [
            'description' => 'Senior',
            'level' => 30,
            'codice_ruolo' => 'SENIOR'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%privileges}}');
    }
}
