<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function index()
    {
        $this->require_login();
        $year = (int) $this->input->get('tahun');
        $month = (int) $this->input->get('bulan');
        $currentYear = (int) date('Y');
        if ($year < 2000 || $year > $currentYear) {
            $year = $currentYear;
        }
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        $data = $this->simkas->dashboard_summary($year, $month);
        $data['judul'] = 'Dashboard';
        $data['tahun'] = $year;
        $data['bulan'] = $month;
        $data['daftar_tahun'] = range(max(2020, $currentYear - 4), $currentYear);
        $this->render('dashboard/index', $data);
    }
}
