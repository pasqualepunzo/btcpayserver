<?php

use yii\db\Migration;

/**
 * Class m230616_072540_update_pos_table
 */
class m230616_072540_update_pos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('pos', 'sin', $this->string(256)->defaultValue(null)->after('description'));

        $this->update(
            'pos', 
            ['sin' => new \yii\db\Expression('(SELECT sin FROM possettings WHERE possettings.pos_id = pos.id)')], // New values for the column
            [] // Condition to match the rows to be updated
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('pos', 'sin');
    }

   
}
