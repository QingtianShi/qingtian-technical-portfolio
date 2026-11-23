<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../services/userService.php';
if ($argc !== 4) {
    fwrite(STDERR,"Usage: php create-admin.php NAME EMAIL PASSWORD\n");
    exit(1);
}
try {
    createAccount(openDatabase(),['full_name'=>$argv[1],'email'=>$argv[2],'password'=>$argv[3],'confirmation'=>$argv[3]],'Administrator');
    echo "Administrator account created.\n";
} catch (InvalidArgumentException $error) {
    fwrite(STDERR,$error->getMessage().PHP_EOL);
    exit(1);
}
