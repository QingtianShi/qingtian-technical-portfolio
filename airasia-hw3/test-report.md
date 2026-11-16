# HW3 Test Report

The packaged SQLite database returned `ok` for `PRAGMA integrity_check` and contained 5 ACCOUNT rows and 20 GIFTCARD rows. A fresh rebuild from both SQL scripts was compared against the packaged database. The revised schema matches the documented column names. Foreign-key checks passed. PHP runtime and Codespaces browser results are pending.

| ID | Steps and input | Expected result | Actual result | Status |
|---|---|---|---|---|
| T01 | Run `php tests/validation-tests.php`. | All seven cases print PASS. | Pending PHP execution. | Pending |
| T02 | Open `/card-list.php` with the seeded database. | See 20 cards with images, names, points, and details links. | SQLite contains 20 cards. Browser result pending. | Pending |
| T03 | Open card 1 from the list. | Cafe 25, Dining, value 25, 2,500 points, and navigation links appear. | Seed row verified in SQLite. Browser result pending. | Pending |
| T04 | Add QA Demo Card, Shopping, 12.50, 1250 points. | Redirect to list and show the new card. | Pending Codespaces test. | Pending |
| T05 | Open the new card's update page. Check existing values and change the name to QA Updated Card. | The detail page displays the updated name. | Pending Codespaces test. | Pending |
| T06 | Open Delete Gift Card, go back to confirm the card still exists, then reopen deletion and confirm. | The initial page preserves the card. Confirmation removes the card and returns to the list. | Pending Codespaces test. | Pending |
| T07 | Submit a whitespace card name. For an altered-input check, edit a dropdown option in browser dev tools and submit it. | Server displays validation feedback and rejects the write. | Pending Codespaces test. | Pending |
| T08 | Open details with `card_id=abc`, then `card_id=999999`. | Invalid ID returns 400. Missing record returns 404. | Pending Codespaces test. | Pending |
