<?php

$a = explode('/', $url->path);
$s = "";

while ($aa = array_shift($a)) {
    $s .= '/' . $aa;
    if ($f = File::exist([
        PAGE . $s . '.page',
        PAGE . $s . '.archive'
    ])) {
        if (strpos("\n" . explode("\n...\n", n(file_get_contents($f)), 2)[0], "\npass:") !== false) {
            $page = new Page($f);
            $pass = $page->pass;
            if (isset($pass['a'])) {
                $pass = $pass['a'];
            }
            $session = Session::get('page.pass', false);
            if (Route::is('%*%/' . Plugin::state('pass', 'path')) && Request::is('post')) {
                if (!Guardian::check(Request::post('token'))) {
                    Message::error($language->plugin_pass->message_error_token);
                } else if ($answer = Request::post('a')) {
                    if ($answer === $pass) {
                        Session::set('page.pass', [$answer, date('Y.m.d') /* valid for a day */]);
                        Guardian::kick(dirname($url->current));
                    } else {
                        Message::error($language->plugin_pass->message_error_pass);
                    }
                } else {
                    Message::error($language->plugin_pass->message_error_void);
                }
            }
            if ($pass && isset($session[0]) && $pass === $session[0] && isset($session[1]) && $session[1] === date('Y.m.d')) {
                // Do nothing.
            } else {
                if ($a) {
                    Guardian::kick($url . $s . $url->query);
                }
                HTTP::status(403);
                require File::exist([
                    SHIELD . DS . $config->shield . DS . '-page.pass.php',
                    __DIR__ . DS . 'lot' . DS . 'worker' . DS . 'document.php'
                ]);
                exit;
            }
        }
    }
}