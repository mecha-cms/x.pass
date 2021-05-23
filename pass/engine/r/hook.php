<?php namespace x\pass;

function alter($content) {
    if (!empty($this['pass'])) {
        $pass = \Cookie::get('page.pass');
        $a = \is_array($this['pass']) ? ($this['pass']['a'] ?? "") : $this['pass'];
        if ($pass && $pass === (string) $a) {
            return $content;
        }
        return '<p><em>' . \i('This %s is protected by a password.', ['page']) . '</em></p>';
    }
    return $content;
}

function check() {
    extract($GLOBALS);
    $chops = \explode('/', $url['path']);
    $folder = \LOT . \DS . 'page';
    $page = false;
    if (\Request::is('Post')) {
        // Remove the `.pass` prefix in URL path
        \array_shift($chops);
    }
    while ($chop = \array_shift($chops)) {
        $folder .= \DS . $chop;
        if ($file = \File::exist([
            $folder . '.archive',
            $folder . '.page'
        ])) {
            if (\is_file($folder . \DS . 'pass.data')) {
                $page = new \Page($file);
                break;
            }
            $start = \defined("\\YAML\\SOH") ? \YAML\SOH : '---';
            $end = \defined("\\YAML\\EOT") ? \YAML\EOT : '...';
            foreach (\stream($file) as $k => $v) {
                if (0 === $k && $start . "\n" !== $v) {
                    // No header marker means no property at all
                    break;
                }
                if ($end . "\n" === $v) {
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
    if ($page) {
        $GLOBALS['page'] = $page;
        $pass = \Cookie::get('page.pass');
        $a = \is_array($page['pass']) ? ($page['pass']['a'] ?? "") : $page['pass'];
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

\Hook::set([
    'page.content',
    'page.excerpt' // `.\lot\x\excerpt`
], __NAMESPACE__ . "\\alter", 100);

\Hook::set('get', __NAMESPACE__ . "\\check", 0);
