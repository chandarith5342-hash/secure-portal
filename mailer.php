<?php


function sendMail($to, $subject, $message) {

    $logFile  = __DIR__ . '/mail_log.txt';
    $logEntry = date('[Y-m-d H:i:s]') . "\nTo: $to\nSubject: $subject\nMessage:\n$message\n" . str_repeat('-', 60) . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

    $headers  = "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'secureportal.com') . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n";

    $sent = @mail($to, $subject, $message, $headers);

    if (!$sent) {
 
        preg_match('/href=[\'"]([^\'"]+)[\'"]/', $message, $m);
        if (!empty($m[1])) {
            // Store in session so the next page can display it
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['_dev_mail_link'] = $m[1];
            $_SESSION['_dev_mail_subj'] = $subject;
        }
    }
    return $sent;
}
