<?php namespace _\lot\x\pass;

function check() {
    global $url;
    $chops = explode('/', \trim($url->path, '/'));
    $p = PAGE;
    $page = false;
    while ($chop = \array_shift($chops)) {
        $p .= DS . $chop;
        if ($file = \File::exist([
            $p . '.page',
            $p . '.archive'
        ])) {
            if (\is_file($p . DS . 'pass.data')) {
                $page = new \Page($file);
            } else {
                foreach (\stream($file) as $k => $v) {
                    if ($k === 0 && $v !== '---') {
                        // No header marker means no `pass` property found
                        break;
                    }
                    if ($v === '...') {
                        // End header marker means no `pass` property found
                        break;
                    }
                    if (
                        \strpos($v, 'pass:') === 0 ||
                        \strpos($v, '"pass":') === 0 ||
                        \strpos($v, "'pass':") === 0
                    ) {
                        $page = new \Page($file); // Found one!
                        break;
                    }
                }
            }
        }
    }
    if ($page) {
        $GLOBALS['page'] = $page;
        $pass = \Cookie::get('page.pass');
        $a = isset($page['pass']['a']) ? $page['pass']['a'] : $page['pass'];
        if ($pass && $pass === (string) $a) {
            // Do nothing!
        } else {
            // Redirect to parent page that has `pass` property
            if (\HTTP::is('get') && \strpos($url->clean, $page->url . '/') === 0) {
                \Guard::kick($page->url . $url->query);
            }
            require __DIR__ . DS . 'route.php';
        }
    }
}

\Hook::set('start', __NAMESPACE__ . "\check", 0);