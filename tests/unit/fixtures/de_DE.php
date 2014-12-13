<?php
return [
    'pluralForm' => function (&$nplurals, &$plural, $n) {
        $nplurals = 2; $plural = ($n != 1);
    },
    'texts' => [
        'someKey' => 'some translation',
        'someKeyWithPlural' => [
            'singular translation',
            'plural translation',
        ]
    ]
];