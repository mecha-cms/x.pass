<?php

Shield::set('page-pass', File::exist([
    SHIELD . DS . $config->shield . DS . 'page-pass.php',
    __DIR__ . DS . 'lot' . DS . 'worker' . DS . 'document.php'
]));

Shield::set('page-pass.form', File::exist([
    SHIELD . DS . $config->shield . DS . 'page-pass.form.php',
    __DIR__ . DS . 'lot' . DS . 'worker' . DS . 'form.php'
]));

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
            $date = date('Y.m.d'); // valid for a day
            if (Route::is('%*%/' . Plugin::state('pass', 'path')) && HTTP::is('post')) {
                if (!Guardian::check(HTTP::post('token'))) {
                    Message::error($language->plugin_pass->message_error_token);
                } else if ($answer = HTTP::post('a')) {
                    if ($answer === $pass) {
                        Session::set('page.pass', [$answer, $date]);
                        Guardian::kick(Path::D($url->current));
                    } else {
                        Message::error($language->plugin_pass->message_error_pass);
                    }
                } else {
                    Message::error($language->plugin_pass->message_error_void);
                }
            }
            if ($pass && isset($session[0]) && $pass === $session[0] && isset($session[1]) && $session[1] === $date) {
                // Do nothing.
            } else {
                if ($a) {
                    Guardian::kick($url . $s . $url->query);
                }
                HTTP::status(403);
                Config::set('trace', new Anemon([$language->enter . ': ' . $page->title, $site->title], ' &#x00B7; '));
                Lot::set('page', $page);
                Hook::set('route.enter', function() {
                    Asset::reset();
                    Hook::set('asset:head', 'fn_pass_css');
                    Shield::attach('page-pass');
                }, 20);
            }
            break;
        }
    }
}

function fn_pass_css($content) {
    $color = substr(md5($_SERVER['REQUEST_TIME']), 0, 6);
    $alt = call_user_func(function($c) {
        $c = str_split($c, 2);
        return $c[0] + $c[1] + $c[2] > 382 ? ['#000', '#fff'] : ['#fff', '#000'];
    }, $color);
    $color = '#' . $color;
    return $content . '<style media="screen">body,form,html,p{overflow:hidden}html::after,html::before,input+div::before{content:""}*{margin:0;padding:0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}body,form,html{max-width:100%;max-height:100%;width:100%;height:100%}html{background:' . $color . ';color:' . $alt[0] . ';padding:5em 4em;font-family:Helmet,FreeSans,sans-serif}p+p{margin-top:1em}button,input{display:block;float:left;padding:.5em .75em;width:auto;height:auto;min-width:0;min-height:0;font:inherit;color:' . $color . ';text-align:inherit;background:' . $alt[0] . ';border:0}button,input+div{color:' . $alt[0] . '}input{min-width:12em}button{background:rgba(0,0,0,.2);padding-right:1em;padding-left:1em;margin-left:.25em;cursor:pointer;text-align:center}button:focus,button:hover{background:rgba(0,0,0,.1)}button:active{background:rgba(0,0,0,.3)}button::-moz-focus-inner{margin:0;padding:0;border:0;outline:0}html::after,html::before{position:absolute;top:0;right:0;left:0;height:1em;background-image:-webkit-linear-gradient(-45deg,rgba(0,0,0,.05)25%,transparent 25%,transparent 50%,rgba(0,0,0,.05)50%,rgba(0,0,0,.05)75%,transparent 75%,transparent);background-image:-moz-linear-gradient(-45deg,rgba(0,0,0,.05)25%,transparent 25%,transparent 50%,rgba(0,0,0,.05)50%,rgba(0,0,0,.05)75%,transparent 75%,transparent);background-image:linear-gradient(-45deg,rgba(0,0,0,.05)25%,transparent 25%,transparent 50%,rgba(0,0,0,.05)50%,rgba(0,0,0,.05)75%,transparent 75%,transparent);background-size:2em 2em}html:after{top:auto;bottom:0}input+div{font-size:75%;margin:.5em 0 0}@media (max-width:450px){html{padding:3em 2em}button,input{display:block;width:100%}button{margin-left:0;margin-top:.25em}}@media (min-width:449px){input+div{position:relative;background:' . $alt[1] . ';border-color:' . $alt[1] . ';float:left;padding:.5em .75em}input+div::before{width:0;height:0;position:absolute;bottom:100%;left:1em;border:.4em solid transparent;border-bottom-color:inherit}}</style>';
}