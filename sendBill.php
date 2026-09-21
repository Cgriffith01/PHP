<?php
// Connect to DB
$servername = "localhost";
$username = "root";
$password = ""; // No password
$dbname = "landscape";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Email sending function
function sendEmail($to, $subject, $message) {
  $headers = "From: noreply@leeslandscape.com\r\n";
  $headers .= "Reply-To: support@leeslandscape.com\r\n";
  $headers .= "Content-Type: text/html\r\n";

  if (mail($to, $subject, $message, $headers)) {
    echo "Email sent to $to.<br>";
  } else {
    echo "Failed to send email to $to.<br>";
  }
}

// Query customer billing info 
$sql = "SELECT c.customer_ID, c.customer_Title, c.customer_L_Name, c.customer_F_Name, c.customer_Email, b.customer_bill, b.amt_paid 
        FROM customers c 
        JOIN billing b ON c.customer_ID = b.customer_ID";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $customerName = $row['customer_Title'] . " " . $row['customer_F_Name'] . " " . $row['customer_L_Name'];
    $email = $row['customer_Email'];
    $billAmount = $row['customer_bill'];
    $amtPaid = $row['amt_paid'];
    $amountDue = number_format($billAmount - $amtPaid, 2); // Amount due

    //if there is bal due
    if ($amountDue > 0) {
      $subject = "Your Bill is Due";
      $message = "
        <html>
        <body>
          <p>Dear $customerName,</p>
          <p>Your current balance is <strong>\$$amountDue</strong>.</p>
          <p>Please make the payment at your earliest convenience.</p>
          <p>Sincerely,<br>Lee's Landscape Team</p>
        </body>
        </html>
      ";
    } else {
      //if there is no bal due - made payment
      $subject = "Thank You for Your Payment";
      $message = "
        <html>
        <body>
          <p>Dear $customerName,</p>
          <p>Thank you for your payment. Your account has been paid in full.</p>
          <p>Sincerely,<br>Lee's Landscape Team</p>
        </body>
        </html>
      ";
    }

    // Send the email
    sendEmail($email, $subject, $message);
  }
} else {
  echo "You don't have any customers."; // customer fail
}

$conn->close(); // close DB connection
?>
