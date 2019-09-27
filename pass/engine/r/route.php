<?php namespace _\lot\x\pass;

function route($lot, $type) {
    global $language, $page, $state;
    $GLOBALS['t'][] = $page->title;
    if ($type === 'Post' && \strpos($this[0], '.pass/') === 0) {
        $error = $lot['_error'] ?? 0;
        if (empty($lot['token']) || !\Guard::check($lot['token'], 'pass')) {
            \Alert::error('pass-token');
            ++$error;
        }
        if (!empty($lot['pass']['a'])) {
            if (isset($page['pass'])) {
                $a = (string) $lot['pass']['a'] ?? "";
                $b = (string) isset($page['pass']['a']) ? $page['pass']['a'] : $page['pass'];
                $enter = false;
                if (\strpos($b, \P) === 0) {
                    $enter = \password_verify($a, \substr($b, 1));
                } else {
                    $enter = $a === $b;
                }
                if ($enter && $error === 0) {
                    \Cookie::set('page.pass', $a, '1 day');
                    \Alert::success('pass');
                } else {
                    \Alert::error('pass');
                }
            } else {
                \Alert::error('pass');
            }
        } else {
            \Alert::error('pass-void-field', $language->pass, true);
        }
        \Guard::kick(\explode('/', $this[0], 2)[1]);
    }
    \State::set('has.pass', true);
    $this->status(403);
    $this->content(__DIR__ . \DS . 'content' . \DS . 'page.php');
}

// Override the `*` route address
\Route::set('*', __NAMESPACE__ . "\\route");