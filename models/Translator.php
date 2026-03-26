<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property string $full_name
 * @property string $language_pair
 * @property string $work_schedule
 * @property int    $is_available
 * @property string $created_at
 */
class Translator extends ActiveRecord
{
    const SCHEDULE_WEEKDAY = 'weekday';
    const SCHEDULE_WEEKEND = 'weekend';
    const SCHEDULE_BOTH    = 'both';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'translators';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['full_name', 'language_pair', 'work_schedule'], 'required'],
            [['full_name'], 'string', 'max' => 150],
            [['language_pair'], 'string', 'max' => 20],
            [['work_schedule'], 'in', 'range' => [self::SCHEDULE_WEEKDAY, self::SCHEDULE_WEEKEND, self::SCHEDULE_BOTH]],
            [['is_available'], 'integer', 'min' => 0, 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'            => 'ID',
            'full_name'     => 'Имя',
            'language_pair' => 'Языковая пара',
            'work_schedule' => 'График работы',
            'is_available'  => 'Доступен',
            'created_at'    => 'Дата добавления',
        ];
    }

    public static function findForWeekday(): array
    {
        return static::find()
            ->where(['in', 'work_schedule', [self::SCHEDULE_WEEKDAY, self::SCHEDULE_BOTH]])
            ->andWhere(['is_available' => 1])
            ->all();
    }

    public static function findForWeekend(): array
    {
        return static::find()
            ->where(['in', 'work_schedule', [self::SCHEDULE_WEEKEND, self::SCHEDULE_BOTH]])
            ->andWhere(['is_available' => 1])
            ->all();
    }

    public static function findAvailable(): array
    {
        return static::find()
            ->where(['is_available' => 1])
            ->all();
    }

    public static function scheduleLabels(): array
    {
        return [
            self::SCHEDULE_WEEKDAY => 'Будни (Пн–Пт)',
            self::SCHEDULE_WEEKEND => 'Выходные (Сб–Вс)',
            self::SCHEDULE_BOTH    => 'Будни и выходные',
        ];
    }

    public function getScheduleLabel(): string
    {
        return self::scheduleLabels()[$this->work_schedule] ?? $this->work_schedule;
    }
}
