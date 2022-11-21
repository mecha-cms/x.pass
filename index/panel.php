<?php

Hook::set('_', function ($_) {
    if (isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']) && 0 === strpos($_['type'] . '/', 'page/page/')) {
        $num_1 = rand(0, 5);
        $num_2 = rand(0, 5);
        $num_3 = $num_1 + $num_2;
        if ('get' === $_['task'] && !$_['file']) {
            return $_;
        }
        $page = new Page($_['file']);
        $pass = (array) $page['pass'];
        if (isset($pass[0]) && !isset($pass['a'])) {
            $pass = ['a' => $pass[0]];
        }
        $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pass'] = [
            'lot' => [
                'fields' => [
                    'description' => 'Make this page private by adding a secret pass code. Share the secret pass code with your trusted frields. Question and hint fields are optional, but if you have friends you can trust, you can write down a question and its answer which only they know.',
                    'type' => 'fields',
                    'lot' => [
                        'q' => [
                            'title' => 'Question',
                            'hint' => ['What is %d + %d?', [$num_1, $num_2]],
                            'type' => 'text',
                            'name' => 'data[pass][q]',
                            'value' => $pass['q'] ?? null,
                            'width' => true,
                            'stack' => 10
                        ],
                        'a' => [
                            'title' => 'Answer',
                            'description' => 'This answer is case sensitive. I suggest you to use lower-case letters only.',
                            'hint' => (string) $num_3,
                            'type' => 'text',
                            'name' => 'data[pass][a]',
                            'value' => $pass['a'] ?? null,
                            'width' => true,
                            'stack' => 20
                        ],
                        'h' => [
                            'title' => 'Hint',
                            'hint' => ['The answer is a number between %d to %d.', [0, 10]],
                            'type' => 'text',
                            'name' => 'data[pass][h]',
                            'value' => $pass['h'] ?? null,
                            'width' => true,
                            'stack' => 30
                        ]
                    ],
                    'stack' => 10
                ]
            ],
            'stack' => 11
        ];
    }
    return $_;
});