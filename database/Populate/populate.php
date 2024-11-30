<?php

require __DIR__ . '/../../config/bootstrap.php';

use Core\Database\Database;
use Database\Populate\usersPopulate;

Database::migrate();
UsersPopulate::populate();
