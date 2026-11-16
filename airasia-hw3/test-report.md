# HW3 Test Report

I ran the validation harness and tested the gift card workflows in GitHub Codespaces using PHP 8.3.33 with PDO SQLite. Before the CRUD tests, SQLite reported `ok` for `PRAGMA integrity_check`. The foreign key check returned no violations. The database contained 5 ACCOUNT rows and 20 GIFTCARD rows.

| ID | Steps and input | Expected result | Actual result | Status |
|---|---|---|---|---|
| T01 | Run `php tests/validation-tests.php`. | All seven cases print PASS. | All seven validation checks printed PASS in Codespaces. | Pass |
| T02 | Open the seeded gift card list. | The list displays card images, linked names, and points. | The list loaded the seeded cards with images, linked names, and points. SQLite reported 20 gift cards before testing. | Pass |
| T03 | Open Bookshop 75 from the list. | The details page shows card ID 19, its image and attributes, and navigation links. | The page displayed Bookshop 75, ID 19, Shopping, value 75, 7,500 points, and links to update, delete, and return to the list. | Pass |
| T04 | Add QA Demo Card, Shopping, value 12.50, and 1,250 points. | Return to the list with the new card visible. | The list displayed QA Demo Card with Shopping and 1,250 required points after submission. | Pass |
| T05 | Change QA Demo Card to QA Updated Card and save. | Display the revised name and preserve the other values. | The detail page displayed QA Updated Card (ID 21), Shopping, value 12.5, and 1,250 points. | Pass |
| T06 | Open the deletion confirmation for QA Updated Card (ID 21) and confirm. | The confirmation identifies the card. After confirmation the list no longer shows it. | The confirmation named QA Updated Card (ID 21). After confirmation the app returned to the list, where the test card was gone. | Pass |
| T07 | Submit a card name containing only spaces with Shopping, value 12.50, and 1,250 points. | The server rejects the submission and explains the name error. | The form displayed “Enter a card name of 1 to 100 characters” and retained the other values. | Pass |
| T08 | Open details with `card_id=abc`, then with `card_id=999999`. | Show distinct messages for a malformed ID and a missing record. | The app displayed “Invalid card ID” for `abc` and “Gift card not found” for `999999`. | Pass |

## Validation harness output

```text
PASS Valid Gift Card data
PASS Blank Card Name
PASS Invalid Card Type
PASS Zero Card Value
PASS Negative Card Value
PASS Invalid Points Required
PASS Malformed card ID
```
