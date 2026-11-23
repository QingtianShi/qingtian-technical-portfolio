<?php
function accountByEmail(PDO $db, string $email): array|false {
    $query = $db->prepare('SELECT * FROM users WHERE email = ?');
    $query->execute([$email]);
    return $query->fetch();
}

function accountById(PDO $db, int $id): array|false {
    $query = $db->prepare('SELECT * FROM users WHERE user_id = ?');
    $query->execute([$id]);
    return $query->fetch();
}

function insertAccount(PDO $db, string $name, string $email, string $hash, string $role): int {
    $query = $db->prepare('INSERT INTO users(full_name,email,password_hash,role) VALUES(?,?,?,?)');
    $query->execute([$name,$email,$hash,$role]);
    return (int) $db->lastInsertId();
}

function searchAccounts(PDO $db, string $search, string $role, string $status): array {
    $query = $db->prepare("SELECT user_id,full_name,email,role,status FROM users
        WHERE (instr(lower(full_name),lower(?)) > 0 OR instr(lower(email),lower(?)) > 0 OR ? = '')
          AND (? = '' OR role = ?) AND (? = '' OR status = ?) ORDER BY full_name,user_id");
    $query->execute([$search,$search,$search,$role,$role,$status,$status]);
    return $query->fetchAll();
}

function saveAccountProfile(PDO $db, int $id, string $name, string $email): void {
    $query = $db->prepare('UPDATE users SET full_name=?,email=? WHERE user_id=?');
    $query->execute([$name,$email,$id]);
}

function saveAccountPassword(PDO $db, int $id, string $oldHash, string $newHash): bool {
    $query = $db->prepare('UPDATE users SET password_hash=? WHERE user_id=? AND password_hash=?');
    $query->execute([$newHash,$id,$oldHash]);
    return $query->rowCount() === 1;
}

function saveAccountAccess(PDO $db, int $id, string $role, string $status): bool {
    $query = $db->prepare("UPDATE users SET role=?,status=? WHERE user_id=?
        AND (role <> 'Administrator' OR status <> 'Active' OR (? = 'Administrator' AND ? = 'Active')
        OR EXISTS (SELECT 1 FROM users WHERE role='Administrator' AND status='Active' AND user_id<>?))");
    $query->execute([$role,$status,$id,$role,$status,$id]);
    return $query->rowCount() === 1;
}

function accountHasRequests(PDO $db, int $id): bool {
    $query = $db->prepare('SELECT EXISTS(SELECT 1 FROM students JOIN tutoring_requests USING(student_id) WHERE user_id=?)');
    $query->execute([$id]);
    return (bool) $query->fetchColumn();
}
