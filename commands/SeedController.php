<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

class SeedController extends Controller
{
    public function actionIndex($count = 10)
    {
        $faker = \Faker\Factory::create('ru_RU');

        $translators = [];
        for ($i = 0; $i < $count; $i++) {
            $translators[] = [
                'full_name' => $faker->name(),
                'language_pair' => $faker->randomElement(['EN-RU', 'HE-RU', 'ZH-RU', 'CZ-RU', 'FR-RU', 'ES-RU', 'DE-RU']),
                'work_schedule' => $faker->randomElement(['weekday', 'weekend', 'both']),
                'is_available' => $faker->boolean(80), // 80% chance to be available
            ];
        }

        \Yii::$app->db->createCommand()->batchInsert(
            'translators',
            ['full_name', 'language_pair', 'work_schedule', 'is_available'],
            $translators
        )->execute();

        echo "Seeded {$count} translators.\n";

        return ExitCode::OK;
    }
}
