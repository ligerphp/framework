<?php
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$adapter = new Local(ROOT);
$filesystem = new Filesystem($adapter);