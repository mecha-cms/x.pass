<form action="<?php echo $url->current . '/' . Plugin::state('pass', 'path') . $url->query('&amp;'); ?>" method="post">
  <p><?php echo !empty($page->pass['q']) ? $page->pass['q'] : $language->plugin_pass->q; ?></p>
  <p><?php echo Form::password('a', null, $language->plugin_pass->a, ['autofocus' => true]) . ' ' . Form::submit(null, null, $language->enter); ?></p>
  <?php echo Form::hidden('token', $token) . $message; ?>
</form>