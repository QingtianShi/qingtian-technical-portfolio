<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../services/userService.php';
require_once __DIR__.'/../services/requestService.php';

$db = openDatabase(':memory:');
$db->exec(file_get_contents(__DIR__.'/../database/setup-database.sql'));
$migration = file_get_contents(__DIR__.'/../database/upgrade-to-user-management.sql');
$db->exec(substr($migration,strpos($migration,"\n")+1));
$failures = 0;

function check(bool $passed, string $label): void {
    global $failures;
    echo ($passed ? 'PASS ' : 'FAIL ').$label.PHP_EOL;
    $failures += (int)!$passed;
}

function rejected(callable $operation): bool {
    try {
        $operation();
        return false;
    } catch (InvalidArgumentException $exception) {
        return true;
    }
}

$password = 'StudentTestPassword1';
$input = [
    'full_name'=>'Alex Test','email'=>'alex@example.com',
    'password'=>$password,'confirmation'=>$password,
];
$student = registerStudent($db,$input);
$profile = studentByAccount($db,$student);
$user = accountById($db,$student);
check($user['role'] === 'Student' && $user['status'] === 'Active' && $profile['user_id'] == $student,'UM-01 creates an active student and linked profile');
check($user['password_hash'] !== $password && password_verify($password,$user['password_hash']),'UM-01 stores a password hash');
check(rejected(fn()=>registerStudent($db,array_replace($input,['email'=>'ALEX@example.com']))),'UM-01 rejects duplicate email');
check(rejected(fn()=>registerStudent($db,array_replace($input,['role'=>'Administrator']))),'UM-01 rejects public administrator selection');
check(rejected(fn()=>registerStudent($db,array_replace($input,['confirmation'=>'different']))),'UM-01 rejects mismatched passwords');

$admin = createAccount($db,array_replace($input,['full_name'=>'Test Administrator','email'=>'admin@example.com']),'Administrator');
check(studentByAccount($db,$admin) === false, 'Administrator has no student profile');
check(count(searchAccounts($db,'alex','Student','Active')) === 1,'UM-03 searches and filters accounts');
updateOwnAccount($db,$student,['full_name'=>'Alex Updated','email'=>'updated@example.com','role'=>'Administrator']);
check(accountById($db,$student)['role'] === 'Student' && studentByAccount($db,$student)['student_email'] === 'updated@example.com','UM-04 updates profile without changing role');

check((bool)authenticateAccount($db,'UPDATED@example.com',$password),'UM-09 accepts valid credentials');
check(!authenticateAccount($db,'updated@example.com','wrong'),'UM-09 rejects incorrect password');
$newPassword = 'NewStudentPassword1';
check(rejected(fn()=>changeOwnPassword($db,$student,['current_password'=>'wrong','password'=>$newPassword,'confirmation'=>$newPassword])),'UM-05 rejects incorrect current password');
changeOwnPassword($db,$student,['current_password'=>$password,'password'=>$newPassword,'confirmation'=>$newPassword]);
check(!authenticateAccount($db,'updated@example.com',$password) && (bool)authenticateAccount($db,'updated@example.com',$newPassword),'UM-05 replaces password');
check(rejected(fn()=>changeAccountAccess($db,$admin,'role','Student')),'UM-06 protects the last active administrator');
changeAccountAccess($db,$student,'role','Administrator');
check(accountById($db,$student)['role'] === 'Administrator','UM-06 updates account role');
changeAccountAccess($db,$student,'role','Student');
changeAccountAccess($db,$student,'status','Inactive');
check(!authenticateAccount($db,'updated@example.com',$newPassword),'UM-07 blocks inactive accounts from logging in');
changeAccountAccess($db,$student,'status','Active');
check((bool)authenticateAccount($db,'updated@example.com',$newPassword),'UM-07 restores access after activation');

$request = submitRequestForStudent($db,(int)$profile['student_id'],[
    'course'=>'IS 6495','reason'=>'Please help me practice SQL joins.',
    'topics'=>['Practice problems'],'preferreddate'=>(new DateTimeImmutable('+2 days'))->format('Y-m-d'),
    'preferredtime'=>'Morning',
]);
check(findRequest($db,$request)['status'] === 'Open' && count(listRequests($db,'','',(int)$profile['student_id'])) === 1,'Student request is Open and filtered by profile');
check(rejected(fn()=>refuseAccountDeletion($db,$student)) && (bool)findRequest($db,$request),'UM-08 preserves related tutoring requests');
check(rejected(fn()=>refuseAccountDeletion($db,$admin)) && (bool)accountById($db,$admin),'UM-08 blocks permanent deletion pending policy');
echo $failures ? "FAILURES: $failures\n" : "All user management checks passed.\n";
exit($failures ? 1 : 0);
