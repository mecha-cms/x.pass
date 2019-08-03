<form action="<?php echo $url . '/.pass' . $url->path . $url->query('&amp;'); ?>" class="form-pass form-pass:enter" method="post" name="pass">
  <?php echo $alert; ?>
  <?php if (!empty($page->pass['q'])): ?>
  <p><?php echo $page->pass['q']; ?></p>
  <?php endif; ?>
  <p title="<?php echo $page->pass['h'] ?? $language->pass; ?>"><input autofocus class="input width" name="pass[a]" placeholder="<?php echo $page->pass['h'] ?? (is_array($page->pass) ? "" : $language->pass); ?>" type="password"></p>
  <p><button class="button" type="submit"><?php echo $language->doEnter; ?></button></p>
  <input name="token" type="hidden" value="<?php echo token('pass'); ?>">
</form>