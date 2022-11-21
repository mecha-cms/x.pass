<?php

if (!is_array($page->pass)) {
    $a = $page->pass ?? null;
    $h = $q = null;
} else {
    $a = $page->pass['a'] ?? null;
    $h = $page->pass['h'] ?? null;
    $q = $page->pass['q'] ?? null;
}

echo new HTML(Hook::fire('y.form.pass', [[
    0 => 'form',
    1 => [
        'alert' => self::alert(),
        'description' => ($q = To::description($q)) ? [
            0 => 'p',
            // This `<span>` wrapper is required because parent element currently has `flex` display.
            // It prevents the child element(s) like `<em>` and `<strong>` from getting messy.
            1 => '<span>' . $q . '</span>'
        ] : null,
        'pass' => [
            0 => 'p',
            1 => [
                0 => [
                    0 => 'label',
                    1 => i('Pass'),
                    2 => [
                        'for' =>  $id = 'f:' . substr(uniqid(), 6)
                    ]
                ],
                1 => [
                    0 => 'br',
                    1 => false
                ],
                2 => [
                    0 => 'span',
                    1 => [
                        0 => [
                            0 => 'input',
                            1 => false,
                            2 => [
                                'autofocus' => true,
                                'id' => $id,
                                'name' => 'pass[a]',
                                'placeholder' => $h,
                                'type' => 'password'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'tasks' => [
            0 => 'p',
            1 => [
                0 => [
                    0 => 'label',
                    1 => i('Tasks')
                ],
                1 => [
                    0 => 'br',
                    1 => false
                ],
                2 => [
                    0 => 'span',
                    1 => [
                        'enter' => [
                            0 => 'button',
                            1 => i('Enter'),
                            2 => [
                                'name' => 'pass[task]',
                                'type' => 'submit',
                                'value' => 'enter'
                            ]
                        ]
                    ],
                    2 => [
                        'role' => 'group'
                    ]
                ]
            ]
        ],
        'token' => [
            0 => 'input',
            1 => false,
            2 => [
                'name' => 'pass[token]',
                'type' => 'hidden',
                'value' => token('pass')
            ]
        ],
        'kick' => [
            0 => 'input',
            1 => false,
            2 => [
                'name' => 'pass[kick]',
                'type' => 'hidden',
                'value' => $kick ?? null
            ]
        ]
    ],
    2 => [
        'action' => strtr($page->url, [$url . '/' => $url . '/' . trim($state->x->pass->route ?? 'pass', '/') . '/']) . $url->query,
        'method' => 'post',
        'name' => 'pass'
    ]
]], $page), true);