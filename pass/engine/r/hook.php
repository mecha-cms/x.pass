<?php namespace _\lot\x\pass;

function check() {
    $url = $GLOBALS['url'];
    $chops = \explode('/', \trim($url->path, '/'));
    $p = \LOT . \DS . 'page';
    $page = false;
    if (\Request::is('Post')) {
        // Remove the `.pass` prefix in URL path
        \array_shift($chops);
    }
    while ($chop = \array_shift($chops)) {
        $p .= \DS . $chop;
        if ($file = \File::exist([
            $p . '.page',
            $p . '.archive'
        ])) {
            if (\is_file($p . \DS . 'pass.data')) {
                $page = new \Page($file);
            } else {
                foreach (\stream($file) as $k => $v) {
                    if (0 === $k && "---\n" !== $v) {
                        // No header marker means no property at all
                        break;
                    }
                    if ("...\n" === $v) {
                        // End header marker means no `pass` property found
                        break;
                    }
                    if (
                        0 === \strpos($v, 'pass:') ||
                        0 === \strpos($v, '"pass":') ||
                        0 === \strpos($v, "'pass':")
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
            if (\Request::is('Get') && 0 === \strpos($url->clean, $page->url . '/')) {
                \Guard::kick($page->url . $url->query);
            }
            require __DIR__ . \DS . 'route.php';
        }
    }
}

\Hook::set('get', __NAMESPACE__ . "\\check", 0);
