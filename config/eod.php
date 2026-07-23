<?php

declare(strict_types=1);

return [

  'planning' => [

    'day_colors' => [
      1 => '#ffd6e0',
      2 => '#e8e8e8',
      3 => '#d6eaf8',
      4 => '#c5d5e4',
      5 => '#ffe4b5',
    ],

    'day_labels' => [
      1 => 'LUNDI',
      2 => 'MARDI',
      3 => 'MERCREDI',
      4 => 'JEUDI',
      5 => 'VENDREDI',
    ],

    'defaults' => [
      'notify_on_publish' => true,
      'notify_supervisor_on_publish' => false,
      'reminder_enabled' => true,
      'reminder_same_day' => true,
      'reminder_same_day_time' => '08:00',
      'reminder_day_before' => true,
      'reminder_day_before_time' => '17:00',
      'default_supervisor_name' => 'NDICK/MAR',
      'max_reminders_per_day' => 2,
    ],

  ],

];
