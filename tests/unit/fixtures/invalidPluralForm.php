<?php
return [
    'pluralForm' => function (&$nplurals, &$plural, $n) {
        $nplurals = 1;
        if ($n == 1) {
            $plural = -1;
        } else {
            $plural = 2;
        }
    },
    'texts' => [
        'someKey' => 'some translation',
        'someKeyWithPlural' => [
            'singular translation',
            'plural translation',
        ]
    ]
];