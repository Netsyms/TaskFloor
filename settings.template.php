<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

// Whether to show debugging data in output.
// DO NOT SET TO TRUE IN PRODUCTION!!!
define("DEBUG", false);

// Database connection settings
// See http://medoo.in/api/new for info
define("DB_TYPE", "mysql");
define("DB_NAME", "taskfloor");
define("DB_SERVER", "localhost");
define("DB_USER", "taskfloor");
define("DB_PASS", "");
define("DB_CHARSET", "utf8");

define("SITE_TITLE", "TaskFloor");

// Used to identify the system in OTP and other places
define("SYSTEM_NAME", "TaskFloor");


// URL of the AccountHub API endpoint
define("PORTAL_API", "http://localhost/accounthub/api.php");
// URL of the AccountHub home page
define("PORTAL_URL", "http://localhost/accounthub/home.php");
// AccountHub API Key
define("PORTAL_KEY", "123");

// For supported values, see http://php.net/manual/en/timezones.php
define("TIMEZONE", "America/Denver");

// Base URL for site links.
define('URL', '.');

// Use Captcheck on login screen
// https://captcheck.netsyms.com
define("CAPTCHA_ENABLED", FALSE);
define('CAPTCHA_SERVER', 'https://captcheck.netsyms.com');

// See lang folder for language options
define('LANGUAGE', "en_us");


define("FOOTER_TEXT", "");
define("COPYRIGHT_NAME", "Netsyms Technologies");
