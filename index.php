<?php

namespace App;

use App\Route;
use App\Db;
use App\Student;
use App\Students;

include "autoloader.php";

Route::router($_SERVER['REQUEST_URI']);
