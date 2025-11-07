<?php

return [
  'categories' => [
    'Attendance' => [
      'value_type'     => 'status',
      'allowed_status' => ['PRESENT','ABSENT','EXCUSED','CANCELED'],
      'allow_percent'  => false,
      'allow_number'   => false,
    ],
    'Productivity' => [
      'value_type'     => 'status_or_percent',
      'allowed_status' => ['COMPLETED','NOT COMPLETED'],
      'allow_percent'  => true,   // "70%" or numeric 0..1
      'allow_number'   => true,   // numeric_value 0..1 as alternative to %
    ],
    'Devotion' => [
      'value_type'     => 'number',
      'allowed_status' => [],
      'allow_percent'  => false,
      'allow_number'   => true,   // numeric count/duration
    ],
  ],
];