<form action="<?= strtr($page->url, [$url . '/' => $url . '/.pass/']) . htmlspecialchars($url->query ?? ""); ?>" class="form-pass form-pass:enter" method="post" name="pass">
  <?= self::alert(); ?>
  <?php if (!empty($page->pass['q'])): ?>
    <p>
      <?= $page->pass['q']; ?>
    </p>
  <?php endif; ?>
  <p title="<?= $page->pass['h'] ?? i('Pass'); ?>">
    <input autofocus name="pass[a]" placeholder="<?= $page->pass['h'] ?? (is_array($page->pass) ? "" : i('Pass')); ?>" type="password">
  </p>
  <p>
    <button type="submit">
      <?= i('Enter'); ?>
    </button>
  </p>
  <input name="token" type="hidden" value="<?= token('pass'); ?>">
</form>