<?php

echo 200;

$data = json_encode($_GET);

file_put_contents('debug.txt', $data);