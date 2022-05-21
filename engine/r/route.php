<?php namespace x\pass;

function route($content, $path, $query) {
    if (null !== $content) {
        return $content;
    }
    \extract($GLOBALS, \EXTR_SKIP);
    $GLOBALS['t'][] = $page->title;
    $p = \trim($state->x->pass->route ?? "", '/');
    $path = \trim($path ?? "", '/');
    if ('POST' === $_SERVER['REQUEST_METHOD'] && 0 === \strpos($path . '/', $p . '/')) {
        $error = 0;
        if (empty($_POST['pass']['token']) || !\check($_POST['pass']['token'], 'pass')) {
            \class_exists("\\Alert") && \Alert::error('Invalid token.');
            ++$error;
        }
        if (!empty($_POST['pass']['a'])) {
            if (isset($page['pass'])) {
                $a = \trim((string) ($_POST['pass']['a'] ?? ""));
                $b = \trim((string) (\is_array($page['pass']) ? ($page['pass']['a'] ?? "") : $page['pass']));
                $enter = false;
                if (0 === \strpos($b, \P)) {
                    $enter = \password_verify($a, \substr($b, 1));
                } else {
                    $enter = $a === $b;
                }
                if ($enter && 0 === $error) {
                    \cookie('page.pass', $a, '1 day');
                    \class_exists("\\Alert") && \Alert::success('Correct answer! This page will remain open to you for the next 1 day.');
                } else {
                    \class_exists("\\Alert") && \Alert::error('Wrong answer!');
                }
            } else {
                \class_exists("\\Alert") && \Alert::error('Wrong answer!');
            }
        } else {
            \class_exists("\\Alert") && \Alert::error('Please fill out the %s field.', 'Pass');
        }
        \kick($_POST['pass']['kick'] ?? '/' . \explode('/', $path, 2)[1]);
    }
    \State::set([
        'has' => ['pass' => true],
        'is' => ['secret' => true]
    ]);
    $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
    \class_exists("\\Asset") && \Asset::set(__DIR__ . \D . '..' . \D . '..' . \D . 'index' . $z . 'css', 20.1);
    return ['pass', [], 403];
}

\Hook::set('route', __NAMESPACE__ . "\\route", 0);