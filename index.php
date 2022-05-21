<?php namespace x\pass;

function check() {
    \extract($GLOBALS);
    $chops = \explode('/', \trim($url->path ?? "", '/'));
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
            if (\is_file($folder . \D . 'pass.data')) {
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
        $pass = \cookie('page.pass');
        $pass_current = $page['pass'] ?? "";
        $a = \trim(\is_array($pass_current) ? ($pass_current['a'] ?? "") : $pass_current);
        if ($pass && $pass === (string) $a) {
            // Do nothing!
        } else {
            // Redirect to parent page that has `pass` property
            if ('GET' === $_SERVER['REQUEST_METHOD'] && 0 === \strpos($url->current(false, false), $page->url . '/')) {
                \kick($page->url . $url->query);
            }
            require __DIR__ . \D . 'engine' . \D . 'r' . \D . 'route.php';
        }
    }
}

function hide($content) {
    $pass_current = $this['pass'] ?? "";
    if ($pass_current && 0 !== \strpos($this->path ?? "", \LOT . \D . 'user' . \D)) {
        $pass = \cookie('page.pass');
        $a = \trim(\is_array($pass_current) ? ($pass_current['a'] ?? "") : $pass_current);
        if ($pass && $pass === (string) $a) {
            return $content;
        }
        return '<p role="status">' . \i('This %s is protected by a pass code.', ['page']) . '</p>';
    }
    return $content;
}

function hook($id, array $lot = [], $join = "") {
    $tasks = \Hook::fire($id, $lot);
    \array_shift($lot); // Remove the task(s) input. Function `x\pass\tasks()` don’t need that!
    return \implode($join, \x\pass\tasks($tasks, $lot));
}

function tasks(array $tasks, array $lot = []) {
    $out = [];
    foreach ($tasks as $k => $v) {
        if (false === $v || null === $v) {
            continue;
        }
        if (\is_array($v)) {
            $out[$k] = new \HTML(\array_replace([false, "", []], $v));
        } else if (\is_callable($v)) {
            $out[$k] = \fire($v, $lot);
        } else {
            $out[$k] = $v;
        }
    }
    return $out;
}

if (\class_exists("\\Layout")) {
    \Layout::set('form/pass', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'form' . \D . 'pass.php');
    \Layout::set('pass', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'pass.php');
}

\Hook::set('get', __NAMESPACE__ . "\\check", 0);
\Hook::set('page.content', __NAMESPACE__ . "\\hide", 100);