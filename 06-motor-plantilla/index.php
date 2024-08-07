<?php
require 'TemplateEngine.php';

$templateEngine = new TemplateEngine();
$templateEngine->set('title', 'Home');
$templateEngine->set('content', $templateEngine->render('templates/content.php'));

echo $templateEngine->render('templates/layout.php');
