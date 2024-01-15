<?php

use Jinya\Plates\Engine;

require_once __DIR__.'/../vendor/autoload.php';

$eng = new Engine();
$eng->addFolder('theme', __DIR__);
echo $eng->render('theme::child');
