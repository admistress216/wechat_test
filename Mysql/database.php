<?php
$active_group = 'default';
$query_builder = TRUE;
$db['default'] = array(
//    'hostname' => '172.31.16.8',
//    'username' => 'cctvnewsplatform',
//    'password' => '!Q@W#E$R',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '123456aA',
    'database' => 'test',
    'dbprefix' => '',
    'port'     => '3306',
    'pconnect' => FALSE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
);
$db['db_slave'] = array(
    'hostname' => '172.31.252.132',
    'username' => 'cctvnewsplatform',
    'password' => '!Q@W#E$R',
    'database' => 'cctvnewsplatform',
    'dbprefix' => 'cctvnewsplatform_',
    'port'     => '3306',
    'pconnect' => FALSE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
);
$db['vgc'] = array(
    'hostname' => '172.31.16.8',
    'username' => 'cctvnews',
    'password' => '!Q@W#E$R',
    'database' => 'cctvnews',
    'dbprefix' => '',
    'port'     => '3306',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
);
$db['log'] = array(
    'hostname' => '172.31.48.26',
    'username' => 'log',
    'password' => 'MasasdfDasdiskwasd023k3sdf93s',
    'database' => 'log',
    'dbprefix' => 'cctvnewsplatform_',
    'port'     => '3306',
    'pconnect' => FALSE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
);


