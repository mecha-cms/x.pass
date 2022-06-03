<form action="<?= strtr($page->url, [$url . '/' => $url . '/' . trim($state->x->pass->route ?? 'pass', '/') . '/']) . From::HTML($url->query ?? ""); ?>" method="post" name="pass" target="_top">
  <?php

  if (!is_array($page->pass)) {
      $a = $page->pass ?? null;
      $h = $q = null;
  } else {
      $a = $page->pass['a'] ?? null;
      $h = $page->pass['h'] ?? null;
      $q = $page->pass['q'] ?? null;
  }

  $tasks = [
      'alert' => self::alert(),
      'description' => ($q = To::description($q)) ? [
          0 => 'p',
          // This `<span>` wrapper is required because parent element currently has `flex` display
          // It aims to prevent the child element(s) like `<em>` and `<strong>` from getting messy
          1 => '<span>' . $q . '</span>'
      ] : null,
      'pass' => [
          0 => 'p',
          1 => (new HTML([
              0 => 'label',
              1 => i('Pass'),
              2 => [
                  'for' => $id = 'f:' . substr(uniqid(), 6)
              ]
          ])) . '<br><span>' . (new HTML([
              0 => 'input',
              1 => false,
              2 => [
                  'autofocus' => true,
                  'id' => $id,
                  'name' => 'pass[a]',
                  'placeholder' => $h,
                  'type' => 'password'
              ]
          ])) . '</span>'
      ]
  ];

  $tasks['tasks'] = [
      0 => 'p',
      1 => (new HTML([
          0 => 'label',
          1 => i('Actions')
      ])) . '<br><span role="group">' . x\pass\hook('pass-form-tasks', [[
          'enter' => [
              0 => 'button',
              1 => i('Enter'),
              2 => [
                  'id' => $id,
                  'type' => 'submit'
              ]
          ]
      ]], ' ') . '</span>'
  ];

  $tasks['token'] = '<input name="pass[token]" type="hidden" value="' . token('pass') . '">';

  if (!empty($kick)) {
      $tasks['kick'] = '<input name="pass[kick]" type="hidden" value="' . From::HTML($kick) . '">';
  }

  ?>
  <?= x\pass\hook('pass-form', [$tasks]); ?>
</form>