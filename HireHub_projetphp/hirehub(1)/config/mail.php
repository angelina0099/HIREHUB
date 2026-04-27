<?php
// FORCER MAILHOG
ini_set('SMTP', '127.0.0.1');
ini_set('smtp_port', '1025');
ini_set('sendmail_path', '');

// Fonction globale d'envoi
function sendMail($to, $subject, $message) {

    $headers = "From: HireHub <no-reply@hirehub.local>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

    return mail($to, $subject, $message, $headers);
}
