<?php

require __DIR__ . '/../../config/bootstrap.php';

use Core\Database\Database;
use Database\Populate\FleetsPopulate;
use Database\Populate\RoutesPopulate;
use Database\Populate\TruckBrandsPopulate;
use Database\Populate\UsersPopulate;

Database::migrate();
UsersPopulate::populate();
FleetsPopulate::populate();
TruckBrandsPopulate::populate();
RoutesPopulate::populate();