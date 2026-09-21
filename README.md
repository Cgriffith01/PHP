# Lee's Landscape Billing Notifier

A small PHP script that connects to a MySQL database, pulls customer billing records, and emails each customer either a payment reminder or a thank you note depending on their balance.

## Files

- **sendBill.php** - the live version. Sends real emails through PHP's `mail()` function.
- **testing.php** - a safe testing version. Instead of sending mail, it prints each email's recipient, subject, and body to the browser so you can check the output before going live.

Both files run the same logic. The only difference is what `sendEmail()` does at the end.

## What it does

1. Connects to a MySQL database named `landscape`.
2. Runs a query joining the `customers` and `billing` tables to get each customer's name, email, total bill, and amount paid.
3. For each customer, calculates the amount still due.
4. If money is owed, sends a "Your Bill is Due" email with the balance.
5. If the account is paid in full, sends a "Thank You for Your Payment" email.
6. Closes the database connection when done.

## Requirements

- PHP with the `mysqli` extension enabled
- A MySQL server (default setup here assumes `localhost` with no password, adjust as needed)
- A `landscape` database with at least two tables:
  - `customers` - needs `customer_ID`, `customer_Title`, `customer_F_Name`, `customer_L_Name`, `customer_Email`
  - `billing` - needs `customer_ID`, `customer_bill`, `amt_paid`
- For `sendBill.php` specifically: a working mail setup on the server (local mail server or SMTP configured for PHP's `mail()` function)

## Setup

1. Update the database connection variables at the top of the script if your setup differs:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "landscape";
   ```
2. Make sure your `customers` and `billing` tables are populated with real data.
3. Run `testing.php` first in a browser to confirm the emails look right before switching to `sendBill.php`.

## Usage

Run either file through a PHP-enabled web server, for example:

```
php -S localhost:8000
```

Then visit `testing.php` in your browser to preview the output, or `sendBill.php` to actually send the billing emails.

## Notes and known limitations

- Database credentials are hardcoded in the file. For anything beyond local testing, move these into an environment file or config outside the web root.
- The `mail()` function depends entirely on the server's mail configuration. It often fails silently or gets flagged as spam without a proper SMTP setup (consider PHPMailer with SMTP for production use).
- There's no input sanitization needed here since the query doesn't take user input, but if this script ever accepts outside data, use prepared statements instead of raw string queries.
- No error handling around individual email failures beyond the success/fail echo. A failed send for one customer doesn't stop the loop, which is good, but there's no log kept of who failed.
