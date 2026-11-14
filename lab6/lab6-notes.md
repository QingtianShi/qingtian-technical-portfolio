# Lab 6 · PHP, PDO, and SQLite Integration

## 1. Database setup

From the `lab6` directory:

```sh
sqlite3 database/tutoring.db < database/setup-database.sql
sqlite3 database/tutoring.db ".tables"
```

Expected tables: `students` and `tutoring_requests`.  
Observed output: `students           tutoring_requests`

## 2. Run the application

I ran the supplied application on the local PHP development server at `http://127.0.0.1:8000/`. The root redirected to `/php/request-form.php`. I submitted Alex Test (`alex@example.com`), course IS 6495, reason “Please help me practice SQL joins,” topic Practice problems, preferred date 2026-11-23, and Morning. The server redirected to `http://127.0.0.1:8000/php/request-details.php?request_id=2`. The returned detail page showed the saved request and status Open.

### Codespaces browser verification

- [Forwarded port 8000 form](https://ominous-robot-jrr676997gj525wgg-8000.app.github.dev/php/request-form.php)
- [Request #2 detail page](https://ominous-robot-jrr676997gj525wgg-8000.app.github.dev/php/request-details.php?request_id=2)
- [Screenshot of Request #2 with status Open](https://chatgpt.com/library?file_id=libfile_a4d2d2ff16d881918e2a875c9c36d077&account_id=personal) (also attach the original PNG in Canvas).

The Codespaces browser displayed the submitted request for Alex Test in IS 6495 with status Open. The Codespace editor link still needs to be copied from GitHub for submission.

## 3. Browser workflow tests

I verified the workflows below in the Codespaces browser. I also checked the edit and cancellation restrictions directly with the local PHP server.

| Test | What I Did | Expected Result | Actual Result | Pass or Fail |
|---|---|---|---|---|
| Submit a valid request | Submitted the sample form in the Codespaces browser. | New request has status Open. | The forwarded detail page for request #2 showed Alex Test and Open. | Pass |
| View all requests | Opened the unfiltered request list in Codespaces. | The new request appears. | Request #2 (Alex Test, IS 6495, Open) appeared alongside the seeded Request #1. | Pass |
| Filter requests | In Codespaces, filtered for IS 6495 and switched the status from Open to Cancelled before cancelling the request. | The filters show matching requests only. | Open returned Alex Test and excluded the IS 6465 seed. Cancelled returned “No matching requests.” | Pass |
| Edit an Open request | Loaded the edit form and saved a revised reason. | The detail page displays the revision. | The reason changed to “Please help me practice SQLite joins and filters.” | Pass |
| Cancel an Open request | Opened the cancellation confirmation and submitted it. | Status becomes Cancelled. | The detail page showed Cancelled. | Pass |
| Try to edit or cancel a Cancelled request | Checked the cancelled detail page in Codespaces and reopened both action pages during local HTTP testing. | Both actions are unavailable. | The Codespaces detail page showed Cancelled with only the All requests button. In the local check, both action pages displayed Open-only messages. | Pass |

## 4. Request submission workflow

| Question | Answer |
|---|---|
| Which file displays the form? | `php/request-form.php` |
| Which file receives the submitted data? | `php/process-request.php` |
| Which file opens the PDO connection to SQLite? | `config/db.php`, where `openDatabase()` constructs the SQLite PDO connection and enables foreign keys. |
| Which file coordinates validation and the transaction? | `services/requestService.php` |
| Which files contain the SQL statements? | `repositories/studentPDO.php` and `repositories/requestPDO.php` contain application queries. `database/setup-database.sql` defines the schema. |
| Which table stores student information? | `students` |
| Which table stores tutoring requests? | `tutoring_requests` |
| How does a request connect to its student? | `tutoring_requests.student_id` refers to `students.student_id`. |

The repositories centralize database queries so the form page can focus on rendering fields and presenting feedback. This makes queries reusable and allows database changes to be made in the data-access layer without changing the form markup.

## 5. Integration tests

I ran `php tests/integration-tests.php` against the original starter. Complete output:

```text
PASS UC01 create and Open
PASS Lab 4 checkbox values preserved
FAIL Duplicate email reuses student
FAIL UC02 course and status filter
PASS Invalid reason rejected
FAIL Invalid submission writes nothing
PASS Edit Open request
PASS Updated reason persisted
PASS Cancel Open request
PASS Cancelled row retained
PASS Second cancellation rejected
PASS Unrelated request unchanged
PASS FK enforced
PASS UC02 empty filter result
FAILURES: 3
```

The setup SQL seeds one student and one Open IS 6465 request before the tests create anything. Three assertions used counts based on an empty database. In a copy of the test file, I captured the initial counts after setup and compared them to the expected increases. I kept the application and SQL seed unchanged. Complete output from the corrected test:

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

## 6. AI Skills Pack

### Application Builder

**Prompt**  
Explain how a tutoring request moves from php/request-form.php through PHP, PDO, and SQLite in this project. Identify the responsibility of each file.

**AI response**  
The form in `php/request-form.php` collects the student and tutoring request details. `php/process-request.php` receives the submission and passes it to `services/requestService.php`, which coordinates validation and the transaction. The student and request repositories use PDO to execute the SQL that persists student information and request details in SQLite. The request stores the associated student's ID.

**Evaluation**  
The AI correctly identified the submission handler. Line 9 of `php/request-form.php` uses `action="process-request.php"` and `method="post"`. Line 5 of `php/process-request.php` calls `submitRequest(openDatabase(), $_POST)` and redirects to the request detail page. Lines 30–40 in `services/requestService.php` show that validation precedes the transaction and that failures roll back database writes.

### Traceability Auditor

**Prompt**  
Trace the requirement “a student can submit a tutoring request” through the PHP files, service, repositories, SQLite tables, and integration tests in this project.

**AI response**  
The form collects input and sends it to the submission handler. The service validates the input and coordinates database writes through the repositories. A successful creation should store a tutoring request linked to the student. An integration check for creation should confirm that the saved request can be retrieved and that its initial status is Open.

**Evaluation and traceability**

| Requirement | Application Page | Service or Validation | Repository or SQL | Database Table | Test Evidence |
|---|---|---|---|---|---|
| Student submits a tutoring request | `php/request-form.php` and `php/process-request.php` | `services/requestService.php` | `repositories/studentPDO.php` and `repositories/requestPDO.php` | `students` and `tutoring_requests` | The suite calls `submitRequest()`, retrieves the new record with `findRequest()`, and asserts that its status is Open. Result: `PASS UC01 create and Open` on both runs. The creation workflow was also verified in the Codespaces browser. |

### Test Harness Generator

**Prompt**  
Suggest one useful test case for the Peer Tutoring Hub application. The test must relate to request creation, filtering, editing, cancellation, validation, or database integrity.

**AI response**  
Create two Open requests in different courses, then filter by one course. Confirm that the matching request appears and the other request does not.

**Evaluation**  
Coverage: The supplied suite checks a combined course/status count and an empty course result. It does not create two Open requests in different courses and assert which IDs are included and excluded. To run the test manually, create two requests with distinct courses, choose one course in the list filter, and compare the visible request IDs against the two test IDs. The test is appropriate because a filter that returns every request could still pass a check for the presence of a matching row.

### Skills Pack Reflection

AI helped me map the submission flow from the form to the SQLite tables. It also suggested checking which records a course filter excludes, which made the test more precise. Developers must verify AI-generated explanations against the code and the assignment because a plausible description can omit an important detail or describe behavior that the application does not implement.

## 7. Inspect the database

```sql
SELECT
    r.request_id,
    s.student_name,
    s.student_email,
    r.course,
    r.topic,
    r.preferred_date,
    r.preferred_time,
    r.status
FROM tutoring_requests AS r
JOIN students AS s
    ON r.student_id = s.student_id
ORDER BY r.request_id;
```

The inner JOIN matches each request's `student_id` with the same ID in the `students` table. The result combines the request details and status with the submitting student's name and email. It includes only requests with a matching student row and sorts them by request ID.

I ran the query after the workflow checks. Request #1 joined to the seeded student Maria Lopez and remained Open. Request #2 joined to Alex Test and showed the updated reason and Cancelled status.
