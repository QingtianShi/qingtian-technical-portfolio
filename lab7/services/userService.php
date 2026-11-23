<?php
require_once __DIR__.'/../repositories/userPDO.php';
require_once __DIR__.'/../repositories/studentPDO.php';

function accountText(array $input, string $field): string {
    return is_string($input[$field] ?? null) ? $input[$field] : '';
}

function accountIdentity(array $input): array {
    $name = trim(accountText($input, 'full_name'));
    $email = strtolower(trim(accountText($input, 'email')));
    if ($name === '' || strlen($name) > 120 || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Enter a name up to 120 bytes and a valid email address.');
    }
    return [$name,$email];
}

function validatedPassword(string $password, string $confirmation): string {
    if (strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
        throw new InvalidArgumentException('Use a password from 12 to 72 bytes.');
    }
    if ($password !== $confirmation) {
        throw new InvalidArgumentException('Passwords must match.');
    }
    return $password;
}

function accountTransaction(PDO $db, callable $operation): mixed {
    $db->beginTransaction();
    try {
        $result = $operation();
        $db->commit();
        return $result;
    } catch (Throwable $error) {
        $db->rollBack();
        if ($error instanceof PDOException && ($error->errorInfo[1] ?? null) === 19
            && str_contains($error->getMessage(), 'UNIQUE constraint failed')) {
            throw new InvalidArgumentException('That email address already belongs to an account or student profile.');
        }
        throw $error;
    }
}

function createAccount(PDO $db, array $input, string $role): int {
    [$name,$email] = accountIdentity($input);
    $password = validatedPassword(accountText($input,'password'), accountText($input,'confirmation'));
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return accountTransaction($db, function () use ($db,$role,$name,$email,$hash) {
        $id = insertAccount($db,$name,$email,$hash,$role);
        if ($role === 'Student') {
            insertStudentProfile($db,$id,$name,$email);
        }
        return $id;
    });
}

function registerStudent(PDO $db, array $input): int {
    if (($input['role'] ?? 'Student') !== 'Student') {
        throw new InvalidArgumentException('Public registration is available for students.');
    }
    return createAccount($db,$input,'Student');
}

function authenticateAccount(PDO $db, string $email, string $password): array|false {
    $account = accountByEmail($db, trim($email));
    if (!$account || !password_verify($password,$account['password_hash']) || $account['status'] !== 'Active') {
        return false;
    }
    return $account;
}

function updateOwnAccount(PDO $db, int $id, array $input): void {
    [$name,$email] = accountIdentity($input);
    accountTransaction($db, function () use ($db,$id,$name,$email) {
        saveAccountProfile($db,$id,$name,$email);
        saveStudentProfile($db,$id,$name,$email);
    });
}

function changeOwnPassword(PDO $db, int $id, array $input): void {
    $account = accountById($db,$id);
    if (!$account || !password_verify(accountText($input,'current_password'),$account['password_hash'])) {
        throw new InvalidArgumentException('The current password is incorrect.');
    }
    $newPassword = validatedPassword(accountText($input,'password'),accountText($input,'confirmation'));
    if (!saveAccountPassword($db,$id,$account['password_hash'],password_hash($newPassword,PASSWORD_DEFAULT))) {
        throw new InvalidArgumentException('The password changed. Please retry.');
    }
}

function changeAccountAccess(PDO $db, int $id, string $action, string $value): void {
    accountTransaction($db, function () use ($db,$id,$action,$value) {
        $target = accountById($db,$id);
        if (!$target) {
            throw new InvalidArgumentException('Account is unavailable.');
        }
        $role = $target['role'];
        $status = $target['status'];
        if ($action === 'role' && in_array($value,['Student','Administrator'],true)) {
            $role = $value;
        } elseif ($action === 'status' && in_array($value,['Active','Inactive'],true) && $value !== $status) {
            $status = $value;
        } else {
            throw new InvalidArgumentException('Select a valid account change.');
        }
        if (!saveAccountAccess($db,$id,$role,$status)) {
            throw new InvalidArgumentException('At least one active administrator must remain.');
        }
        if ($role === 'Student' && !studentByAccount($db,$id)) {
            insertStudentProfile($db,$id,$target['full_name'],$target['email']);
        }
    });
}

function refuseAccountDeletion(PDO $db, int $id): never {
    if (accountHasRequests($db,$id)) {
        throw new InvalidArgumentException('This account has tutoring requests that must be retained.');
    }
    throw new InvalidArgumentException('Permanent deletion requires an approved retention policy. You can deactivate the account.');
}
