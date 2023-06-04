<?php

namespace x\pass\page {
    function content($content) {
        $pass_current = $this->pass ?? "";
        if ($pass_current && 0 !== \strpos($this->path ?? "", \LOT . \D . 'user' . \D)) {
            $pass = (string) \cookie('page.pass');
            $a = (string) \trim(\is_array($pass_current) ? ($pass_current['a'] ?? "") : $pass_current);
            if ($pass && $pass === $a) {
                return $content;
            }
            return '<p role="status">' . \i('This %s is protected by a pass code.', ['page']) . '</p>';
        }
        return $content;
    }
    \Hook::set('page.content', __NAMESPACE__ . "\\content", 100);
}

namespace x\pass {
    function route($content, $path, $query, $hash) {
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
    \Hook::set('route.page', __NAMESPACE__ . "\\route", 100.1);
}

namespace {
    if (\class_exists("\\Layout")) {
        \Layout::set('form/pass', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'form' . \D . 'pass.php');
        \Layout::set('pass', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'pass.php');
    }
}