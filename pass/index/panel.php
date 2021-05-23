<?php

Hook::set('_', function($_) {
    if (isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']) && 0 === strpos($_['type'] . '/', 'page/page/')) {
        $num_1 = rand(0, 5);
        $num_2 = rand(0, 5);
        $num_3 = $num_1 + $num_2;
        $page = new Page($_['f'] ?: null);
        $pass = (array) $page['pass'];
        $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pass'] = [
            'lot' => [
                'fields' => [
                    'description' => 'Protect this page by adding a secret question that can only be answered by the visitors to which you have been told the answer. The question and hint fields are optional. You can also just provide a text input to write the secret answer without asking any questions.',
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
                            'hint' => ['The answer is numbers between %d to %d.', [0, 10]],
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
