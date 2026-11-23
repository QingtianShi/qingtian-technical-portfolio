<?php
function findStudentByEmail($pdo, $email) {
    $stmt = $pdo->prepare('SELECT student_id, student_name, student_email FROM students WHERE student_email = ?');
    $stmt->execute([$email]); return $stmt->fetch();
}
function insertStudentIfAbsent($pdo, $name, $email) {
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO students(student_name,student_email) VALUES(?,?)');
    $stmt->execute([$name,$email]);
}

function studentByAccount(PDO $db, int $id): array|false {
    $query = $db->prepare('SELECT * FROM students WHERE user_id=?');
    $query->execute([$id]);
    return $query->fetch();
}

function insertStudentProfile(PDO $db, int $id, string $name, string $email): void {
    $query = $db->prepare('INSERT INTO students(user_id,student_name,student_email) VALUES(?,?,?)');
    $query->execute([$id,$name,$email]);
}

function saveStudentProfile(PDO $db, int $id, string $name, string $email): void {
    $query = $db->prepare('UPDATE students SET student_name=?,student_email=? WHERE user_id=?');
    $query->execute([$name,$email,$id]);
}
