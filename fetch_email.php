<?php
// Email account details
$email_address = 'your-email@example.com'; // Email address to fetch emails from
$email_password = 'your-email-password'; // Password for the email account

// Connect to the IMAP server
$mailbox = imap_open("{imap.example.com:993/imap/ssl}INBOX", $email_address, $email_password);

// Check for new emails
$mail_check = imap_check($mailbox);

if ($mail_check->Nmsgs > 0) {
    // Fetch the most recent email
    $message = imap_fetchbody($mailbox, $mail_check->Nmsgs, 1.2);
    
    // Example: Extract email address from the message (you might need more sophisticated parsing)
    preg_match('/From: (.*)\<(.*?)\>/', $message, $matches);
    $from_email = $matches[2]; // Extracted email address
    
    // Output the email address (for AJAX request to fetch)
    echo $from_email;
} else {
    echo 'No new emails found.';
}

// Close the mailbox
imap_close($mailbox);
?>
