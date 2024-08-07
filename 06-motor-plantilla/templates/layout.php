<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title ?? 'Mi Sitio Web'; ?></title>
  <?php echo $this->getBlock('styles'); ?>
</head>

<body>
  <?php $this->include('templates/header.php'); ?>
  <main>
    <?php echo $content ?? ''; ?>
  </main>
  <?php $this->include('templates/footer.php'); ?>
  <?php echo $this->getBlock('scripts'); ?>
</body>

</html>