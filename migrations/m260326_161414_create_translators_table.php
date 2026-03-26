<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%translators}}`.
 */
class m260326_161414_create_translators_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%translators}}', [
            'id'            => $this->primaryKey()->unsigned(),
            'full_name'     => $this->string(150)->notNull()->comment('Имя переводчика'),
            'language_pair' => $this->string(20)->notNull()->comment('Языковая пара, напр. EN-RU'),
            'work_schedule' => "ENUM('weekday','weekend','both') NOT NULL DEFAULT 'weekday' COMMENT 'График работы'",
            'is_available'  => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('1 = свободен, 0 = занят'),
            'created_at'    => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_translators_schedule',  '{{%translators}}', 'work_schedule');
        $this->createIndex('idx_translators_available', '{{%translators}}', 'is_available');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%translators}}');
    }
}
