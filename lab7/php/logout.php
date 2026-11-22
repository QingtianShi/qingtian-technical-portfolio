<?php
require_once __DIR__.'/../includes/auth.php';
requireAccount();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Use the Log Out button.');
}
verifyCsrf();
endAccountSession();
go('/php/login.php');
