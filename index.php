<?php namespace x\pass;

function page__content($content) {
    $pass_current = $this->pass ?? "";
    if ($pass_current && 0 !== \strpos($this->path ?? "", \LOT . \D . 'user' . \D)) {
        $pass = (string) \cookie('page.pass');
        $a = (string) \trim(\is_array($pass_current) ? ($pass_current['a'] ?? "") : $pass_current);
        if ($pass && (0 === \strpos($a, \P) && \password_verify($pass, \substr($a, 1)) || $pass === $a)) {
            return $content;
        }
        return '<p role="status">' . \i('This %s is protected by a pass code.', ['page']) . '</p>';
    }
    return $content;
}

function route__page($content, $path, $query, $hash) {
    \extract($GLOBALS, \EXTR_SKIP);
    $chops = \explode('/', $path = \trim($path ?? "", '/'));
    $folder = \LOT . \D . 'page';
    $page = false;
    if ('POST' === $_SERVER['REQUEST_METHOD']) {
        // Remove `pass` prefix from URL path
        \array_shift($chops);
    }
    while ($chop = \array_shift($chops)) {
        $folder .= \D . $chop;
        if ($file = \exist([
            $folder . '.archive',
            $folder . '.page'
        ], 1)) {
            // Get `pass` data from external data
            if (\is_file($folder . \D . 'pass.data')) {
                $page = new \Page($file);
                break;
            }
            // Get `pass` data from internal data
            foreach (\stream($file) as $k => $v) {
                // No `---\n` part at the start of the stream means no page header at all
                if (0 === $k && "---\n" !== $v && 3 !== \strspn($v, '-')) {
                    break;
                }
                // Has reached the `...\n` part in the stream means the end of the page header
                if ("...\n" === $v) {
                    break;
                }
                // Test for `{ asdf: asdf }` part in the stream
                if ($v && '{' === $v[0]) {
                    $v = \trim(\substr(\trim(\strstr($v, '#', true) ?: $v), 1, -1));
                }
                if ($v && ('pass' === \strtok($v, " :\n\t") || '"' === $v[0] && \preg_match('/^"pass"\s*:/', $v) || "'" === $v[0] && \preg_match("/^'pass'\\s*:/", $v))) {
                    $page = new \Page($file); // Found one!
                    break;
                }
            }
        }
    }
    if ($page) {
        $GLOBALS['page'] = $page;
        $pass = (string) \cookie('page.pass');
        $pass_current = $page->pass ?? "";
        $a = (string) \trim(\is_array($pass_current) ? ($pass_current['a'] ?? "") : $pass_current);
        \State::set('has.pass', true);
        if ($pass && $pass === $a) {
            \State::set('has.user', true);
        } else {
            // Redirect to parent page that has `pass` property
            if ('GET' === $_SERVER['REQUEST_METHOD'] && 0 === \strpos($url->current(false, false), $page->url . '/')) {
                \kick($page->url . $query . $hash);
            }
            // User try to enter the pass
            if ('POST' === $_SERVER['REQUEST_METHOD']) {
                $route = \trim($state->x->pass->route ?? 'pass', '/');
                if (0 !== \strpos($path . '/', $route . '/')) {
                    return;
                }
                $error = 0;
                if (empty($_POST['pass']['token']) || !\check($_POST['pass']['token'], 'pass')) {
                    \class_exists("\\Alert") && \Alert::error('Invalid token.');
                    ++$error;
                } else if (!empty($_POST['pass']['a'])) {
                    $enter = false;
                    $try = \trim((string) ($_POST['pass']['a'] ?? ""));
                    if (0 === \strpos($a, \P)) {
                        $enter = \password_verify($try, \substr($a, 1));
                    } else {
                        $enter = $try === $a;
                    }
                    if ($enter) {
                        \cookie('page.pass', $try, '+1 day');
                        \class_exists("\\Alert") && \Alert::success('Correct answer! This page will remain open to you for the next 1 day.');
                    } else {
                        \class_exists("\\Alert") && \Alert::error('Incorrect answer!');
                    }
                } else {
                    \class_exists("\\Alert") && \Alert::error('Please fill out the %s field.', 'Pass');
                }
                \kick($_POST['pass']['kick'] ?? '/' . \substr($path, \strlen($route) + 1));
            }
            $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
            \class_exists("\\Asset") && \Asset::set(__DIR__ . \D . 'index' . $z . 'css', 20.1);
            return ['pass', [], 403];
        }
    }
}

if (\class_exists("\\Layout")) {
    \Layout::set('form/pass', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'form' . \D . 'pass.php');
    \Layout::set('pass', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'pass.php');
}

\Hook::set('page.content', __NAMESPACE__ . "\\page__content", 100);
\Hook::set('route.page', __NAMESPACE__ . "\\route__page", 100.1);