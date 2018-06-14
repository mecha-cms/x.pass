<!DOCTYPE html>
<html dir="<?php echo $site->direction; ?>">
  <head>
    <meta charset="<?php echo $site->charset; ?>">
    <title><?php echo To::text($site->trace); ?></title>
    <link href="<?php echo $url; ?>/favicon.ico" rel="shortcut icon">
  </head>
  <body><?php Shield::get('page-pass.form'); ?></body>
</html>