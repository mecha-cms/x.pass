<form action="<?= $url . '/.pass' . $url->path . $url->query('&amp;'); ?>" class="form-pass form-pass:enter" method="post" name="pass">
  <?= $alert; ?>
  <?php if (!empty($page->pass['q'])): ?>
  <p><?= $page->pass['q']; ?></p>
  <?php endif; ?>
  <p title="<?= $page->pass['h'] ?? $language->pass; ?>"><input autofocus class="input width" name="pass[a]" placeholder="<?= $page->pass['h'] ?? (is_array($page->pass) ? "" : $language->pass); ?>" type="password"></p>
  <p><button class="button" type="submit"><?= $language->doEnter; ?></button></p>
  <input name="token" type="hidden" value="<?= Guard::token('pass'); ?>">
</form>