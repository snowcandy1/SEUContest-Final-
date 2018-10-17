<?php
$redis = new Redis();
$redis->pconnect('127.0.0.1', 6379);
echo "Server is running: ".$redis->ping(); 