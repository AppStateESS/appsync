<?php

$db = \phpws2\Database::getDB();
$db->exec('drop table if exists appsync_log_entry');
$db->exec('drop sequence if exists appsync_log_entry_seq');
$db->exec('drop table if exists appsync_portal');
$db->exec('drop table if exists appsync_settings');
$db->exec('drop sequence if exists appsync_settings_seq');
$db->exec('drop table if exists appsync_umbrella_admin');
$db->exec('drop sequence if exists appsync_umbrella_admin_seq');
$db->exec('drop table if exists appsync_umbrella');
$db->exec('drop sequence if exists appsync_umbrella_seq');
