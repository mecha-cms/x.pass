<?php namespace x\pass;

function route($any) {
    extract($GLOBALS, \EXTR_SKIP);
    $GLOBALS['t'][] = $page->title;
    if (\Request::is('Post') && 0 === \strpos($any, '.pass/')) {
        $error = 0;
        $lot = \Post::get();
        if (empty($lot['token']) || !\Guard::check($lot['token'], 'pass')) {
            \Alert::error('Invalid token.');
            ++$error;
        }
        if (!empty($lot['pass']['a'])) {
            if (isset($page['pass'])) {
                $a = \trim((string) ($lot['pass']['a'] ?? ""));
                $b = \trim((string) (\is_array($page['pass']) ? ($page['pass']['a'] ?? "") : $page['pass']));
                $enter = false;
                if (0 === \strpos($b, \P)) {
                    $enter = \password_verify($a, \substr($b, 1));
                } else {
                    $enter = $a === $b;
                }
                if ($enter && 0 === $error) {
                    \Cookie::set('page.pass', $a, '1 day');
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
        \Guard::kick(\explode('/', $any, 2)[1]);
    }
    \State::set([
        'has' => ['pass' => true],
        'is' => ['secret' => true]
    ]);
    $z = \defined("\\DEBUG") && \DEBUG ? '.' : '.min.';
    \Asset::set(__DIR__ . \DS . '..' . \DS . '..' . \DS . 'lot' . \DS . 'asset' . \DS . 'css' . \DS . 'index' . $z . 'css', 10);
    $this->status(403);
    $this->layout(__DIR__ . \DS . 'layout' . \DS . 'page.php');
}

// Override the `*` route address
\Route::set('*', __NAMESPACE__ . "\\route", 20);
