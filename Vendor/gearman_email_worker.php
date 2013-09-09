<?php
require(__DIR__ . '/../../../Vendor/autoload.php');
require(__DIR__ . '/EmailWorker.php');

$w = new EmailWorker();

echo "Started at ", date('Y-m-d H:i:s'), "\n";
$w->init();
echo "Finished at ", date('Y-m-d H:i:s'), "\n";
