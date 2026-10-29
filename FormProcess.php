<?php
// Set this to an inbox that can receive messages from your PHP web host.
$email_to = 'lkorzeni@student.scf.edu';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

function form_value($key) {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

$firstName = form_value('FirstName');
$lastName = form_value('LastName');
$city = form_value('City');
$state = form_value('ST');
$zip = form_value('Zip');
$email = form_value('Email');
$gender = form_value('Gender');
$as = form_value('AS');
$bs = form_value('BS');
$comments = form_value('Comments');

if ($firstName === '' || $lastName === '' || $comments === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    ($zip !== '' && !preg_match('/^[0-9]{5}$/', $zip)) ||
    ($state !== '' && !preg_match('/^[A-Z]{2}$/', $state)) ||
    ($gender !== '' && !in_array($gender, ['M', 'F'], true)) ||
    ($as !== '' && $as !== 'AS') || ($bs !== '' && $bs !== 'BS')) {
    http_response_code(400);
    exit('Please return to the contact form and enter valid required information.');
}

// Strip control characters from text fields before constructing mail headers/body.
$clean = function ($value) {
    return preg_replace('/[\x00-\x1F\x7F]+/', ' ', $value);
};
$body = "New website contact submission\n\n"
    . "First Name: " . $clean($firstName) . "\n"
    . "Last Name: " . $clean($lastName) . "\n"
    . "City: " . $clean($city) . "\n"
    . "State: " . $clean($state) . "\n"
    . "Zip: " . $clean($zip) . "\n"
    . "Email: " . $clean($email) . "\n"
    . "Gender: " . $clean($gender) . "\n"
    . "Education: " . implode(', ', array_filter([$as, $bs])) . "\n"
    . "Comments:\n" . $comments . "\n";

// A PHP-capable web host must provide working outbound mail delivery.
$sent = mail($email_to, 'Lucas Korzeniewski Contact Submission', $body,
    ['Content-Type' => 'text/plain; charset=UTF-8', 'Reply-To' => $email]);

if (!$sent) {
    http_response_code(500);
    exit('Sorry, your message could not be sent. Please try again later.');
}

header('Location: contactSent.htm', true, 303);
exit;
