<?php
require_once("settings.php");
require_once("lang/".$language.".php");
//database connection

// make a database class to handle all database operations including connection
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $conn;
    //make constructor to connect to the database, assume host=localhost, user=root, pass="" and let the constructor take the database name as parameter
    public function __construct($db) {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $db);
        if ($this->conn->connect_error) {
            // if connection fails, return the error message into a global session variable for later use, and use message from language file (lang/en.php)
            $_SESSION['error'] = "Database connection failed: " . $this->conn->connect_error;
            // redirect to the error page
            header("Location: error.php");
        }
    }
    public function query($sql) {
        //clean the sql
        $sql = $this->conn->real_escape_string($sql);
        //remove any html tags
        $sql = strip_tags($sql);
        //execute safe query
        return $this->conn->query($sql);
    }
    public function __destruct() {
        $this->conn->close();
    }

    // make a function to handle all user input, and return the cleaned input
    public function fix($input) {
        //remove any html tags
        $input = strip_tags($input);
        //remove any slashes
        $input = stripslashes($input);
        //remove any spaces
        $input = trim($input);
        //return the cleaned input
    return $input;
}
}
// make a class to handle user login, user level, user restriction, session handling and cookie handling
class User {

    private $db;

    public function __construct($db_name) {
        $this->db = new Database($db_name);
    }


    public function login($username, $password) {
        //clean the username and password
        $username = $this->db->fix($username);
        $password = $this->db->fix($password);
        //hash the password
        $password = md5($password);
        //make a query to check if the username and password match
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $this->db->query($sql);
        //if the query returns a row, then the user is authenticated
        if ($result->num_rows > 0) {
            //get the user data
            $row = $result->fetch_assoc();
            //set the session variables
            $_SESSION['username'] = $row['username'];
            $_SESSION['level'] = $row['level'];
            //set the cookie
            setcookie("username", $row['username'], time() + 3600);
            setcookie("level", $row['level'], time() + 3600);
            //redirect to the home page
            header("Location: index.php");
        } else {
            //if the query returns no rows, then the user is not authenticated
            //set the error message
            $_SESSION['error'] = $lang['login_error'];
            //redirect to the error page
            header("Location: error.php");
        }
    }
    public function logout() {
        //unset the session variables
        unset($_SESSION['username']);
        unset($_SESSION['level']);
        //unset the cookies
        setcookie("username", "", time() - 3600);
        setcookie("level", "", time() - 3600);
        //redirect to the login page
        header("Location: login.php");
    }
    public function check() {
        //check if the session variables are set
        if (!isset($_SESSION['username']) || !isset($_SESSION['level'])) {
            //if the session variables are not set, check the cookies
            if (isset($_COOKIE['username']) && isset($_COOKIE['level'])) {
                //if the cookies are set, set the session variables
                $_SESSION['username'] = $_COOKIE['username'];
                $_SESSION['level'] = $_COOKIE['level'];
            } else {
                //if the cookies are not set, redirect to the login page
                header("Location: login.php");
            }
        }
    }
    public function restrict($level) {
        //check if the user level is less than the required level
        if ($_SESSION['level'] < $level) {
            //if the user level is less than the required level, set the error message
            $_SESSION['error'] = $lang['restrict_error'];
            //redirect to the error page
            header("Location: error.php");
        }
    }
}
class Email {
    private $to;
    private $subject;
    private $body;
    private $headers;

    public function __construct() {
        $this->headers = "From: webmaster@example.com\r\n";
        $this->headers .= "MIME-Version: 1.0\r\n";
        $this->headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function send() {
        if (mail($this->to, $this->subject, $this->body, $this->headers)) {
            return true;
        } else {
            return false;
        }
    }

    public function readMailIMAP($mailbox, $username, $password) {
        $inbox = imap_open($mailbox, $username, $password) or die('Cannot connect to mailbox: ' . imap_last_error());
        $emails = imap_search($inbox, 'ALL');
        $output = [];
        if ($emails) {
            rsort($emails);
            foreach ($emails as $email_number) {
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                $message = imap_fetchbody($inbox, $email_number, 2);
                $output[] = ['overview' => $overview, 'message' => $message];
            }
        }
        imap_close($inbox);
        return $output;
    }

    public function readMailPOP3($hostname, $username, $password) {
        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to mailbox: ' . imap_last_error());
        $emails = imap_search($inbox, 'ALL');
        $output = [];
        if ($emails) {
            rsort($emails);
            foreach ($emails as $email_number) {
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                $message = imap_fetchbody($inbox, $email_number, 2);
                $output[] = ['overview' => $overview, 'message' => $message];
            }
        }
        imap_close($inbox);
        return $output;
    }

    public function sendMailSMTP($host, $port, $username, $password, $to, $subject, $body) {
        $headers = "From: " . $username . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $smtp = fsockopen($host, $port);
        fputs($smtp, "EHLO " . $host . "\r\n");
        fputs($smtp, "AUTH LOGIN\r\n");
        fputs($smtp, base64_encode($username) . "\r\n");
        fputs($smtp, base64_encode($password) . "\r\n");
        fputs($smtp, "MAIL FROM: <" . $username . ">\r\n");
        fputs($smtp, "RCPT TO: <" . $to . ">\r\n");
        fputs($smtp, "DATA\r\n");
        fputs($smtp, "Subject: " . $subject . "\r\n" . $headers . "\r\n" . $body . "\r\n.\r\n");
        fputs($smtp, "QUIT\r\n");
        fclose($smtp);
    }
}

// make a class to handle encryption and decryption of data using AES256 encryption
class Crypt
{
    private $key ="This is my perfectly secure key 100%!";
    //let the constructor set another key than the private $key if parameter is passed
    public function __construct($key = null)
    {
        if ($key != null) {
            $this->key = $key;
        }
    }

    public function encrypt_AES($data)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    public function decrypt_AES($data)
    {
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $this->key, 0, $iv);
    }
    //make two functions to encrypt and decrypt using twofish encryption, let the key be set in the constructor
    public function encrypt_twofish($data)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('twofish-cbc'));
        $encrypted = openssl_encrypt($data, 'twofish-cbc', $this->key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    public function decrypt_twofish($data)
    {
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'twofish-cbc', $this->key, 0, $iv);
    }
}



?>