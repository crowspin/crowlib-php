<?php

if ($handle = opendir(__DIR__)){
    while (($file = readdir($handle)) !== false){
        if ($file != "incl.php" && filetype($file) == "PHP") require_once $file;
    }
    closedir($handle);
}