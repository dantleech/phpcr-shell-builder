<?php

$BUILD_DIR='_build';
$REPO='/dantleech/phpcr-shell';
$TARGET_DIR=$BUILD_DIR.'/phpcr-shell';
$CWD=__DIR__;

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
} else {
    $ip = 'CLI';
}

$logRes = fopen('request.log', 'a');

function doLog($message) {
    global $logRes;
    fwrite($logRes, $message."\n");
}

function doExec($command) {
    doLog('Executing: '.$command);
    exec($command);
}


doLog("Received request from $ip");

if (!file_exists($BUILD_DIR)) {
    mkdir($BUILD_DIR);
}

if (file_exists($TARGET_DIR)) {
    doExec('rm -Rf '.$TARGET_DIR);
}

doExec('git clone git@github.com:'.$REPO.' '.$TARGET_DIR);
chdir($TARGET_DIR);

doExec('composer install');
doExec('box build');
doExec('ghman release:create dantleech phpcr-shell bleeding-edge --name="Latest" --body="Bleeding Edge version" dev-master --assetFile=phpcr.phar --assetName=phpcr');

fclose($logRes);
