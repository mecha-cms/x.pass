<form action="<?= strtr($page->url, [$url . '/' => $url . '/.pass/']) . $url->query('&amp;'); ?>" class="form-pass form-pass:enter" method="post" name="pass">
  <?= $alert; ?>
  <?php if (!empty($page->pass['q'])): ?>
    <p>
      <?= $page->pass['q']; ?>
    </p>
  <?php endif; ?>
  <p title="<?= $page->pass['h'] ?? i('Pass'); ?>">
    <input autofocus class="input width" name="pass[a]" placeholder="<?= $page->pass['h'] ?? (is_array($page->pass) ? "" : i('Pass')); ?>" type="password">
  </p>
  <p>
    <button class="button" type="submit">
      <?= i('Enter'); ?>
    </button>
  </p>
  <input name="token" type="hidden" value="<?= Guard::token('pass'); ?>">
</form>
