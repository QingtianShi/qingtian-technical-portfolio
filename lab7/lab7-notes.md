# Lab 7 · Authentication and Authorization

## 1. Application generation

**AI prompt used as the implementation brief**

> Generate code for UM-01 through UM-10 using the Peer Tutoring Hub User Management Design Document and extend the existing Lab 6 PHP/PDO/SQLite application. Preserve its three-tier architecture and add registration, login, logout, profile and password changes, administrator account management, and protected tutoring request workflows. Follow the Lab 7 student and administrator scope and return the updated application.

**Source materials used** · `lab6.zip`, `LAB 7_ Authentication & Authorization(1).pdf`, and `Peer_Tutoring_Hub_User_Management_With_Diagrams.docx`. The separate Authentication Reading Handout and AI Skills Pack files were unavailable during this implementation.

Lab 7 adds a `users` table and links student profiles to accounts. It protects tutoring pages with session, role, and ownership checks. I checked `registerStudent()` against UM-01 and the assignment scope. The public path always uses the Student role and rejects a submitted Administrator role.

## 2. Database setup

```sh
cd lab7/database
sqlite3 tutoring.db < setup-database.sql
sqlite3 tutoring.db < upgrade-to-user-management.sql
sqlite3 tutoring.db '.tables'
```

The commands were verified against a temporary local SQLite database. All three tables appeared, the existing request remained, and `PRAGMA foreign_key_check` returned no violations.

```text
students           tutoring_requests  users
```

## 3. Administrator account

```sh
php create-admin.php "Test Administrator" admin@example.com "<test-password>"
```

Test email is `admin@example.com`, with role Administrator. The command succeeded in a disposable local SQLite database and printed `Administrator account created.` The Codespaces run remains pending.

## 4. Run the application

```sh
cd ..
php -S 0.0.0.0:8000 php/router.php
```

**Pending** · Record the Codespaces editor link and forwarded browser URL. A local HTTP test verified that a signed-out visit to the application redirected to `/php/login.php` with HTTP 303.

## 5. Student workflow

| Test | Action to verify | Expected result | Actual result | Pass or fail |
|---|---|---|---|---|
| Register | Submit Create Account with test data | Student account created | Registration redirected to login and displayed the confirmation. | Pass (local HTTP) |
| Log in | Submit the registered email and password | My Requests opens | Login redirected to My Requests and issued a new session ID. | Pass (local HTTP) |
| View or update profile | Open My Profile and save a revised name | Updated name appears | The profile showed Alex Test and accepted the revised name Alex Updated. | Pass (local HTTP) |
| Create request | Submit a request for a course | Request is Open | The IS 6495 request detail displayed status Open. | Pass (local HTTP) |
| Edit Open request | Change the request details | Updated details appear | The detail view showed the revised SQLite joins and filters text. | Pass (local HTTP) |
| View my requests | Open My Requests | Only the student's requests appear | The list showed Alex Updated’s request and excluded the seeded request. | Pass (local HTTP) |
| Log out | Select Log Out, then revisit the request list | Login page appears | Logout and a subsequent protected-page visit both redirected to Log In. | Pass (local HTTP) |

**Student screenshot** · [My Requests](screenshots/lab7-student-my-requests.jpg). In the local Chrome run, Alex Lab7 created and edited request #2 for IS 6465. It appeared as Open.

## 6. Administrator workflow

Local HTTP checks confirmed that the administrator could sign in, see both accounts in Manage Users, see the student and seeded requests in All Tutoring Requests, and sign out. A signed-in student received HTTP 403 when opening Manage Users. **Administrator screenshot** · [Manage Users](screenshots/lab7-admin-manage-users.jpg). Local Chrome displayed the active Alex Lab7 and Test Administrator accounts. The administrator could also view both tutoring requests.

1. Authentication verifies the submitted email and password against an active account and establishes a session.
2. Authorization checks the account role and, for student requests, the linked student record before granting access.
3. Students can manage their own profile and their own Open requests. Administrators can view requests across students and manage accounts.
4. Manage Users, account details for other users, role and status changes, and deletion review are restricted to administrators. Permanent deletion is blocked under the current retention policy.
5. A protected page redirects to Log In when the session is gone.

## 7. Registration and login trace

| Question | Code evidence |
|---|---|
| What displays and receives the registration form? | `php/register.php` renders the form on GET and receives it on POST. |
| Where are CSRF tokens created and verified? | `csrfToken()`, `csrfField()`, and `verifyCsrf()` in `includes/auth.php`. |
| What validates registration? | `registerStudent()`, `accountIdentity()`, and `validatedPassword()` in `services/userService.php`. |
| Where is the password hashed? | `createAccount()` uses `password_hash()` before database writes. |
| Which repositories insert records? | `insertAccount()` in `repositories/userPDO.php` and `insertStudentProfile()` in `repositories/studentPDO.php`. |
| Which tables receive the registration? | `users` and `students`, in one database transaction. |
| What displays and receives the login form? | `php/login.php` handles both GET and POST. |
| Which repository retrieves the account? | `accountByEmail()` in `repositories/userPDO.php`. |
| Where is the password verified? | `authenticateAccount()` in `services/userService.php` calls `password_verify()` and checks Active status. |
| What happens to the session on login? | `startAccountSession()` regenerates the ID and stores the account ID and last activity timestamp. |
| Why regenerate the ID? | It discards the pre-login session ID to protect against session fixation. |
| How does the application determine role? | `currentAccount()` reloads the account from SQLite and `requireAccount()` checks the required role. |

The application layer owns the credential rules so registration, password changes, and CLI administrator creation use the same validation and hashing. Presentation pages pass input to those services, and the schema stores the resulting hashes. Keeping verification in the authentication service also gives login a single place to check both the password and account status.

## 8. Automated test execution

| Command | Result |
|---|---|
| `php tests/validation-tests.php` | Pass (local PHP CLI) |
| `php tests/user-management-tests.php` | Pass (local PHP CLI) |
| `php tests/integration-tests.php` | Pass (local PHP CLI) |

Both Lab 6 test files were preserved byte-for-byte. The project adds a Lab 7 user-management suite. All commands returned exit code 0 in the local PHP 8.4 CLI runtime.

### Validation

```text
<p>Valid time: PASS</p><p>Missing time: PASS</p><p>Altered value: PASS</p><p>Multiple valid topics: PASS</p><p>Missing topics: PASS</p><p>Altered topic: PASS</p><p>Minimum length: PASS</p><p>Maximum length: PASS</p><p>Too short: PASS</p><p>Too long: PASS</p>
```

### User management

```text
PASS UM-01 creates an active student and linked profile
PASS UM-01 stores a password hash
PASS UM-01 rejects duplicate email
PASS UM-01 rejects public administrator selection
PASS UM-01 rejects mismatched passwords
PASS Administrator has no student profile
PASS UM-03 searches and filters accounts
PASS UM-04 updates profile without changing role
PASS UM-09 accepts valid credentials
PASS UM-09 rejects incorrect password
PASS UM-05 rejects incorrect current password
PASS UM-05 replaces password
PASS UM-06 protects the last active administrator
PASS UM-06 updates account role
PASS UM-07 blocks inactive accounts from logging in
PASS UM-07 restores access after activation
PASS Student request is Open and filtered by profile
PASS UM-08 preserves related tutoring requests
PASS UM-08 blocks permanent deletion pending policy
All user management checks passed.
```

### Integration

```text
PASS UC01 create and Open
PASS Lab 4 checkbox values preserved
PASS Duplicate email reuses student
PASS UC02 course and status filter
PASS Invalid reason rejected
PASS Invalid submission writes nothing
PASS Edit Open request
PASS Updated reason persisted
PASS Cancel Open request
PASS Cancelled row retained
PASS Second cancellation rejected
PASS Unrelated request unchanged
PASS FK enforced
PASS UC02 empty filter result
All integration checks passed
```

## 9. AI-assisted review

### Application Builder

**Prompt** · Explain how a student registration request moves from register.php through authentication checks, PHP services, repositories, PDO, and SQLite in the Peer Tutoring Hub application. Identify the responsibility of each file or layer.

**Response** · The browser posts the registration form to `register.php`. The page verifies the session's CSRF token through `includes/auth.php`, then passes the input to `registerStudent()`. The service validates the new account, hashes the password, and calls the user and student repositories within a single PDO transaction. The repositories insert the linked account and profile in SQLite.

**Evaluation** · Verified in `createAccount()` that the account ID returned by `insertAccount()` becomes the `students.user_id` value. A failure in either insert rolls back the transaction.

### Traceability Auditor

**Prompt** · Trace the requirement “a student can register, log in, create a tutoring request, and log out” through the presentation pages, authentication code, services, repositories, SQLite tables, and automated tests in this project.

**Response and source check**

| Requirement | Presentation page | Authentication or authorization | Service or validation | Repository or SQL | Table | Test evidence |
|---|---|---|---|---|---|---|
| Register | `register.php` | `verifyCsrf()` | `registerStudent()` | `insertAccount()`, `insertStudentProfile()` | `users`, `students` | User-management suite has registration and duplicate-email cases |
| Log in | `login.php` | `verifyCsrf()`, `startAccountSession()` | `authenticateAccount()` | `accountByEmail()` | `users` | User-management suite checks credentials and inactive status. Local HTTP check verified session ID regeneration |
| Create request | `request-form.php`, `process-request.php` | `requireAccount('Student')`, `verifyCsrf()`, `requestStudentId()` | `submitRequestForStudent()` | `studentByAccount()`, `insertTutoringRequest()` | `students`, `tutoring_requests` | User-management suite checks Open status and student filtering |
| Log out | Navigation button and `logout.php` | `requireAccount()`, `verifyCsrf()`, `endAccountSession()` | PHP session cleanup | No write | None | Local HTTP check verified redirect after logout |

**Evaluation** · The service tests exercise credentials and request persistence. The local HTTP harness also confirmed cookie rotation, redirects, and session invalidation. Codespaces browser evidence is pending.

### Test Harness Generator

**Prompt** · Suggest one useful test case for authentication or authorization in the Peer Tutoring Hub application.

**Response** · Log in as a test student, select Log Out, then revisit My Requests in the same browser. Expect the login page to open and the request list to remain inaccessible.

**Evaluation** · The CLI suites do not exercise the browser cookie lifecycle. The manual workflow above checks UM-10 without altering the provided test files.

### Reflection

AI helped trace registration from the form to the two linked database inserts and distinguish login checks from authorization checks. Reviewing the trace against the source showed which checks run in services and which depend on a browser session. Test descriptions and generated code still need independent verification because a plausible explanation can omit a failed path or claim coverage that a test does not exercise.

## 10. SQLite inspection

```sql
SELECT u.user_id, u.full_name, u.email, u.role, u.status,
       s.student_id, s.student_name, s.student_email
FROM users AS u
LEFT JOIN students AS s ON u.user_id = s.user_id
ORDER BY u.user_id;
```

The LEFT JOIN returns every account and the student's profile columns when a matching row exists. The `students.user_id` foreign key points to `users.user_id`, and a unique index limits each account to one student profile. Keeping accounts separate from profiles lets all roles share credential and status fields while student-specific data stays linked to tutoring requests. An administrator created by the CLI has no student profile, so the student columns are NULL.

Observed local output with fictional accounts

```text
1 | Test Administrator | admin@example.com | Administrator | Active | NULL | NULL | NULL
2 | Alex Updated | alex@example.com | Student | Active | 2 | Alex Updated | alex@example.com
```

## Local browser screenshots

Both screenshots were captured from the local PHP server at `http://127.0.0.1:8000`. The Codespaces link is pending.
