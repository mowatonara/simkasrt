<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function mask_nik($nik)
{
    $nik = (string) $nik;
    if (!preg_match('/^\d{16}$/', $nik)) {
        return '****************';
    }
    return substr($nik, 0, 6) . '******' . substr($nik, -4);
}

function normalize_phone_login($phone)
{
    return preg_replace('/[^0-9]/', '', (string) $phone);
}

function whatsapp_number($phone)
{
    $number = normalize_phone_login($phone);
    if (substr($number, 0, 2) === '00') {
        $number = substr($number, 2);
    }
    if (substr($number, 0, 1) === '0') {
        return '62' . substr($number, 1);
    }
    if (substr($number, 0, 2) === '62') {
        return $number;
    }
    if (substr($number, 0, 1) === '8') {
        return '62' . $number;
    }
    return $number;
}

function rupiah($nominal)
{
    return 'Rp ' . number_format((float) $nominal, 0, ',', '.');
}
