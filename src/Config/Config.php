<?php
namespace App\Config;

// DB Credentials
define("HOST",      $_ENV['DATABASE_HOST']);
define("USER",      $_ENV['DATABASE_USER']);
define("PASSWORD",  $_ENV['DATABASE_PASSWORD']);
define("DB",        $_ENV['DATABASE_NAME']);

// App Settings
define("VAT",  .21);

define("TURNOVER_REPORT_NAME_PREFIX", "daily-turnover");

define("CURRENCY", "usd");