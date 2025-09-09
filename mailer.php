<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (!isset($GLOBALS['SMTP_LAST_ERROR'])) { $GLOBALS['SMTP_LAST_ERROR'] = ''; }
// Optional local SMTP config file for environments without env vars (e.g., XAMPP)
// Create smtp_config.php with constants: SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_FROM, SMTP_SECURE
try { if (file_exists(__DIR__.'/smtp_config.php')) { include_once __DIR__.'/smtp_config.php'; } } catch (Throwable $e) {}
function smtp_get(string $key, ?string $default = null): ?string {
    if (defined($key)) { return constant($key); }
    $env = getenv($key);
    if ($env !== false && $env !== '') { return $env; }
    return $default;
}
function send_email(string $to, string $subject, string $htmlBody, ?string $textBody = null): bool {
    // If SMTP is configured, prefer SMTP
    $smtpHost = smtp_get('SMTP_HOST', '');
    if ($smtpHost) {
        return smtp_send($to, $subject, $htmlBody, $textBody);
    }
    $from = smtp_get('SMTP_FROM', 'no-reply@yourdomain.com');
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: '.$from;
    $headers[] = 'Reply-To: '.$from;
    $headers[] = 'X-Mailer: PHP/'.phpversion();
    $headersStr = implode("\r\n", $headers);
    $safeSubject = 'NEUST Guidance: '.$subject;
    $html = '<!doctype html><html><body style="font-family:Arial,Helvetica,sans-serif;">'.$htmlBody.'</body></html>';
    $ok = @mail($to, $safeSubject, $html, $headersStr);
    if (!$ok && $textBody) {
        $plainHeaders = 'From: '.$from."\r\n".'X-Mailer: PHP/'.phpversion();
        return @mail($to, $safeSubject, $textBody, $plainHeaders);
    }
    return $ok;
}
// Branding and ICS helpers
if (!defined('APP_EMAIL_FROM')) { define('APP_EMAIL_FROM', smtp_get('SMTP_FROM', 'no-reply@yourdomain.com')); }
if (!defined('APP_BASE_URL')) {
   
    // Attempt to infer base URL including subdirectory (e.g., /student_services_system2/)
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
   
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\','/', dirname($script));
    $basePath = rtrim($dir, '/');
    $basePath = ($basePath === '') ? '/' : ($basePath.'/');
    $baseUrl = $host ? ($scheme.'://'.$host.$basePath) : $basePath;
    define('APP_BASE_URL', $baseUrl);
}
if (!defined('APP_ICS_SECRET')) { define('APP_ICS_SECRET', 'change_this_secret_key'); }

function render_branded_email(string $title, string $contentHtml): string {
    $styles = 'font-family:Arial,Helvetica,sans-serif; background:#f6f9fc; padding:24px;';
    $card = 'max-width:560px; margin:0 auto; background:#ffffff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.06); overflow:hidden;';
    $header = 'background:#003366; color:#FFD700; padding:16px 20px; font-size:18px; font-weight:bold;';
    $body = 'padding:20px; color:#222; line-height:1.55;';
    $footer = 'padding:16px 20px; color:#6c757d; font-size:12px; border-top:1px solid #eef2f7;';
    return '<!doctype html><html><body style="'.$styles.'">'
      .'<div style="'.$card.'">'
      .'<div style="'.$header.'">NEUST Guidance Services</div>'
      .'<div style="'.$body.'"><h2 style="margin:0 0 12px 0; font-size:20px; color:#003366;">'.htmlspecialchars($title).'</h2>'.$contentHtml.'</div>'
      .'<div style="'.$footer.'">This is an automated message. Please do not reply.</div>'
      .'</div>'
      .'</body></html>';
}
function send_branded_email(string $to, string $subject, string $title, string $contentHtml, ?string $textBody = null): bool {
    $html = render_branded_email($title, $contentHtml);
    return send_email($to, $subject, $html, $textBody ?? strip_tags($contentHtml));
}
function ics_hmac(int $appointmentId, string $startUtc, string $endUtc): string {
    return hash_hmac('sha256', $appointmentId.'|'.$startUtc.'|'.$endUtc, APP_ICS_SECRET);
}
function ics_format_datetime_utc(string $datetimeLocal): array {
    $dt = new DateTime($datetimeLocal);
    $dt->setTimezone(new DateTimeZone('UTC'));
    $start = $dt->format('Ymd\THis\Z');
    $dtEnd = clone $dt; $dtEnd->modify('+1 hour');
    $end = $dtEnd->format('Ymd\THis\Z');
    return [$start, $end];
}
function ics_download_link(int $appointmentId, string $startLocal): string {
    [$startUtc, $endUtc] = ics_format_datetime_utc($startLocal);
    $sig = ics_hmac($appointmentId, $startUtc, $endUtc);
    return APP_BASE_URL.'download_appointment_ics.php?id='.$appointmentId.'&s='.$startUtc.'&e='.$endUtc.'&sig='.$sig;
}
// Minimal SMTP implementation (LOGIN/PLAIN over TLS/SSL) without external deps
function smtp_send(string $to, string $subject, string $htmlBody, ?string $textBody = null): bool {
    $host = smtp_get('SMTP_HOST', '');
    $port = (int)(smtp_get('SMTP_PORT', '587'));
    $user = smtp_get('SMTP_USER', '');
    $pass = smtp_get('SMTP_PASS', '');
    $from = smtp_get('SMTP_FROM', APP_EMAIL_FROM);
    $secure = strtolower(smtp_get('SMTP_SECURE', 'tls')); // tls, ssl, or none
    if (!$host || !$user || !$pass) { return false; }
    $transport = ($secure === 'ssl') ? "ssl://$host:$port" : "tcp://$host:$port";
    $sock = @stream_socket_client($transport, $errno, $errstr, 20);
    if (!$sock) { $GLOBALS['SMTP_LAST_ERROR'] = "Connect failed: $errstr ($errno)"; return false; }
    $read = function() use ($sock) { return fgets($sock, 515); };
    $send = function($cmd) use ($sock) { fwrite($sock, $cmd."\r\n"); return true; };
    $starts_with = function($hay, $needle){ return substr($hay, 0, strlen((string)$needle)) === (string)$needle; };
    $expect = function($code) use ($read, $starts_with) { $resp = ''; while ($line = $read()) { $resp .= $line; if (isset($line[3]) && $line[3] === ' ') break; } return [$starts_with($resp, (string)$code), $resp]; };
    [$ok, $resp] = $expect(220); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "Server banner: $resp"; fclose($sock); return false; }
    $send('EHLO localhost'); [$ok,$resp] = $expect(250); if (!$ok) { $send('HELO localhost'); [$ok,$resp] = $expect(250); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "HELO/EHLO failed: $resp"; fclose($sock); return false; } }
    if ($secure === 'tls') { $send('STARTTLS'); [$ok,$resp] = $expect(220); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "STARTTLS failed: $resp"; fclose($sock); return false; } if (!stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { $GLOBALS['SMTP_LAST_ERROR'] = 'TLS crypto enable failed'; fclose($sock); return false; } $send('EHLO localhost'); [$ok,$resp] = $expect(250); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "Post-TLS EHLO failed: $resp"; fclose($sock); return false; } }
    // Try AUTH LOGIN, fallback to AUTH PLAIN
    $send('AUTH LOGIN'); [$ok,$resp] = $expect(334);
    if ($ok) {
        $send(base64_encode($user)); [$ok,$resp] = $expect(334); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "Username not accepted: $resp"; fclose($sock); return false; }
        $send(base64_encode($pass)); [$ok,$resp] = $expect(235); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "Password not accepted: $resp"; fclose($sock); return false; }
    } else {
        $authPlain = base64_encode("\0$user\0$pass");
        $send('AUTH PLAIN '.$authPlain); [$ok,$resp] = $expect(235); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "AUTH failed: $resp"; fclose($sock); return false; }
    }
    $send('MAIL FROM:<'.$from.'>'); [$ok,$resp] = $expect(250); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "MAIL FROM failed: $resp"; fclose($sock); return false; }
    $send('RCPT TO:<'.$to.'>'); [$ok,$resp] = $expect(250); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "RCPT TO failed: $resp"; fclose($sock); return false; }
    $send('DATA'); [$ok,$resp] = $expect(354); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "DATA command failed: $resp"; fclose($sock); return false; }
    $boundary = 'bnd_'.bin2hex(random_bytes(6));
    $safeSubject = 'NEUST Guidance: '.$subject;
    $headers = 'From: '.$from."\r\n".'MIME-Version: 1.0' . "\r\n" . 'Content-Type: multipart/alternative; boundary="'.$boundary.'"';
    $text = $textBody ?: strip_tags($htmlBody);
    $html = '<!doctype html><html><body style="font-family:Arial,Helvetica,sans-serif;">'.$htmlBody.'</body></html>';
    $data = 'Subject: '.$safeSubject."\r\n".$headers."\r\n\r\n".
            '--'.$boundary."\r\n".'Content-Type: text/plain; charset=UTF-8' . "\r\n\r\n".$text."\r\n".
            '--'.$boundary."\r\n".'Content-Type: text/html; charset=UTF-8' . "\r\n\r\n".$html."\r\n".
            '--'.$boundary.'--' . "\r\n.";
    fwrite($sock, $data."\r\n");
    $send('.'); [$ok,$resp] = $expect(250); if (!$ok) { $GLOBALS['SMTP_LAST_ERROR'] = "Message not accepted: $resp"; fclose($sock); return false; }
    $send('QUIT'); fclose($sock); return true;
}
?>