<?php

require('vendor/autoload.php');

use Core\Console\TableManager;
use Core\Console\MakeCRUD;

$tableCreator = new TableManager();
$tableCreator->run($argv);

$createController = new MakeCRUD($argv);
