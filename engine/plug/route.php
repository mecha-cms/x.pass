<?php namespace x\pass;

function route($content, $path, $query) {
    if (null !== $content) {
        return $content;
    }
    \extract($GLOBALS, \EXTR_SKIP);
    $GLOBALS['t'][] = $page->title;
    $path = \trim($path ?? "", '/');
    if ('POST' === $_SERVER['REQUEST_METHOD'] && 0 === \strpos($path, '.pass/')) {
        $error = 0;
        if (empty($_POST['token']) || !\check($_POST['token'], 'pass')) {
            \Alert::error('Invalid token.');
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
                    \Alert::success('Correct answer! This page will remain open to you for the next 1 day.');
                } else {
                    \Alert::error('Wrong answer!');
                }
            } else {
                \Alert::error('Wrong answer!');
            }
        } else {
            \Alert::error('Please fill out the %s field.', 'Pass');
        }
        \kick('/' . \explode('/', $path, 2)[1]);
    }
    \State::set([
        'has' => ['pass' => true],
        'is' => ['secret' => true]
    ]);
    $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
    \Asset::set(__DIR__ . \D . '..' . \D . '..' . \D . 'index' . $z . 'css', 20.1);
    $this->status(403);
    return [__DIR__ . \D . '..' . \D . 'y' . \D . 'page.php', [], 403];
}

\Hook::set('route', __NAMESPACE__ . "\\route", 0);