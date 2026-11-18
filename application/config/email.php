<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| SMTP settings for the citizen password-reset email.
| Fill these values with the outgoing mail settings provided by your host.
| Do not commit or share the smtp_pass value publicly.
*/
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'mail.naracorp.web.id';
$config['smtp_user'] = 'simkasrt@naracorp.web.id';
$config['smtp_pass'] = '';
$config['smtp_port'] = 465;
$config['smtp_timeout'] = 15;
$config['smtp_crypto'] = 'ssl';
$config['mailtype'] = 'text';
$config['charset'] = 'UTF-8';
$config['wordwrap'] = TRUE;
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";
$config['reset_from_email'] = 'simkasrt@naracorp.web.id';
$config['reset_from_name'] = 'SIMKAS-RT';
