<?php
namespace PHPMailer\PHPMailer;
use PHPMailer\Exception\Exception;

class PHPMailer {
    public $SMTPDebug = 0;
    public $isSMTP = false;
    public $Host;
    public $SMTPAuth = true;
    public $Username;
    public $Password;
    public $SMTPSecure;
    public $Port;
    public $From;
    public $FromName;
    public $Subject;
    public $Body;
    public $AltBody;
    public $Mailer = 'smtp';
    private $to = [];
    public function isSMTP(){ $this->isSMTP = true; }
    public function addAddress($addr, $name=''){ $this->to[] = $addr; }
    public function addStringAttachment($string, $filename){ file_put_contents(sys_get_temp_dir().'/'.$filename, $string); }
    public function send() {
        // Very small SMTP send wrapper using PHP mail() as fallback for localhost testing.
        $to = implode(',', $this->to);
        $headers = 'From: ' . $this->From . "\r\n" . 'Reply-To: ' . $this->From . "\r\n" . 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        return mail($to, $this->Subject, $this->Body, $headers);
    }
}
?>