<?php namespace _\lot\x\pass;

function route($form, $k) {
    global $config, $language, $page;
    $GLOBALS['t'][] = $page->title;
    if ($k === 'post' && \substr($this[0], -6) === '/.pass') {
        $errors = 0;
        if (!isset($form['token']) || !\Guard::check($form['token'], 'pass')) {
            \Alert::error('token');
            ++$errors;
        }
        if (!empty($form['pass']['a'])) {
            if (isset($page['pass'])) {
                $a = (string) $form['pass']['a'] ?? "";
                $b = (string) isset($page['pass']['a']) ? $page['pass']['a'] : $page['pass'];
                $enter = false;
                if (\strpos($b, P) === 0) {
                    $enter = \password_verify($a, \substr($b, 1));
                } else {
                    $enter = $a === $b;
                }
                if ($enter) {
                    \Cookie::set('page.pass', $a, '1 day');
                    \Alert::success('pass');
                } else {
                    \Alert::error('pass');
                }
            } else {
                \Alert::error('pass');
            }
        } else {
            \Alert::error('void-field', $language->pass, true);
        }
        \Guard::kick(\dirname($this[0]));
    }
    $this->status(403);
    $this->content(__DIR__ . DS . 'content' . DS . 'page.php');
}

// Override the `*` route address
\Route::set('*', __NAMESPACE__ . "\\route");