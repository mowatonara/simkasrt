<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warga extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_login('warga');
        if (empty($this->currentUser['data_warga_id'])) {
            show_error('Akun warga belum ditautkan ke data warga.', 403);
        }
    }

    public function history()
    {
        $this->render('warga/history', array(
            'judul' => 'History Pembayaran',
            'history' => $this->simkas->payment_history($this->currentUser['data_warga_id']),
            'tagihan' => $this->simkas->unpaid_bills($this->currentUser['data_warga_id'])
        ));
    }

    public function pembayaran()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('tanggal_bayar', 'Tanggal bayar', 'required|callback_tanggal_valid');
            $this->form_validation->set_rules('metode', 'Metode pembayaran', 'required|in_list[transfer,qris,tunai]');
            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', trim(strip_tags(validation_errors(' ', ' '))));
                redirect('warga/pembayaran');
            }

            $billId = (int) $this->input->post('tagihan_id');
            $jenisId = (int) $this->input->post('jenis_iuran_id');
            $amount = (float) $this->input->post('jumlah');
            if (!$billId && $amount <= 0) {
                $this->session->set_flashdata('error', 'Nominal sumbangan harus lebih dari nol.');
                redirect('warga/pembayaran');
            }

            $upload = $this->upload_file('bukti', 'private_uploads/bukti', 'jpg|jpeg|png|pdf', 2048);
            if (isset($upload['error'])) {
                $this->session->set_flashdata('error', $upload['error']);
                redirect('warga/pembayaran');
            }
            $payload = array(
                'data_warga_id' => $this->currentUser['data_warga_id'],
                'jenis_iuran_id' => $jenisId,
                'tanggal_bayar' => $this->input->post('tanggal_bayar', TRUE),
                'metode' => $this->input->post('metode', TRUE),
                'jumlah' => $amount,
                'bukti_file' => $upload['file'],
                'status' => 'menunggu',
                'dibuat_oleh' => $this->currentUser['id']
            );
            $result = $this->simkas->submit_payment($payload, $billId ?: NULL);
            if (!$result['ok']) {
                $path = APPPATH . '../private_uploads/bukti/' . basename($upload['file']);
                if (is_file($path)) {
                    unlink($path);
                }
            }
            $this->session->set_flashdata($result['ok'] ? 'success' : 'error', $result['message']);
            redirect('warga/history');
        }

        $allTypes = $this->simkas->iuran_list(FALSE);
        $donations = array_values(array_filter($allTypes, function ($item) {
            return $item->frekuensi === 'sekali' && (int) $item->aktif === 1;
        }));
        $this->render('warga/pembayaran', array(
            'judul' => 'Halaman Pembayaran',
            'tagihan' => $this->simkas->unpaid_bills($this->currentUser['data_warga_id']),
            'donasi' => $donations,
            'selected_bill' => (int) $this->input->get('tagihan_id')
        ));
    }

    public function buku_kas()
    {
        $year = (int) $this->input->get('tahun');
        $month = (int) $this->input->get('bulan');
        if ($year < 2000 || $year > (int) date('Y')) {
            $year = (int) date('Y');
        }
        $rows = $this->simkas->cash_rows($year, $month);
        $income = 0;
        $expense = 0;
        foreach ($rows as $row) {
            if ($row->arah === 'masuk') {
                $income += (float) $row->jumlah;
            } else {
                $expense += (float) $row->jumlah;
            }
        }
        $this->render('cash/index', array(
            'judul' => 'Buku Kas',
            'transaksi' => $rows,
            'tahun' => $year,
            'bulan' => $month,
            'total_masuk' => $income,
            'total_keluar' => $expense,
            'read_only' => TRUE
        ));
    }

    public function bukti($id)
    {
        $this->stream_payment_proof($this->simkas->payment_by_id($id));
    }

    public function baca_notifikasi($id)
    {
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Metode tidak diizinkan.', 405);
        }
        $notice = $this->simkas->read_notification($id, $this->currentUser['id']);
        if (!$notice) {
            show_404();
        }
        if ($notice->tagihan_id) {
            redirect('warga/pembayaran?tagihan_id=' . (int) $notice->tagihan_id);
        }
        redirect('warga/history');
    }

    public function setting()
    {
        $wargaId = $this->currentUser['data_warga_id'];
        $person = $this->simkas->warga_by_id($wargaId);
        $account = $this->simkas->user_by_id($this->currentUser['id']);
        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('nama', 'Nama', 'required|trim|max_length[120]');
            $this->form_validation->set_rules('nik', 'NIK', 'required|trim|exact_length[16]|numeric');
            $this->form_validation->set_rules('no_hp', 'Nomor HP', 'required|trim|max_length[25]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[150]');
            $this->form_validation->set_rules('jenis_kelamin', 'Jenis kelamin', 'required|in_list[L,P]');
            $this->form_validation->set_rules('blok', 'Blok', 'required|trim|max_length[20]');
            $this->form_validation->set_rules('no_rumah', 'Nomor rumah', 'required|trim|max_length[20]');
            $this->form_validation->set_rules('password_baru', 'Password baru', 'min_length[8]');
            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', trim(strip_tags(validation_errors(' ', ' '))));
                redirect('warga/setting');
            }

            $nik = trim((string) $this->input->post('nik', TRUE));
            if ($this->simkas->nik_exists($nik, $wargaId)) {
                $this->session->set_flashdata('error', 'NIK tersebut sudah digunakan.');
                redirect('warga/setting');
            }
            $phone = normalize_phone_login($this->input->post('no_hp', TRUE));
            if (!preg_match('/^[0-9]{8,15}$/', $phone)) {
                $this->session->set_flashdata('error', 'Nomor HP harus berisi 8 sampai 15 digit.');
                redirect('warga/setting');
            }
            if ($this->simkas->phone_exists($phone, $wargaId)) {
                $this->session->set_flashdata('error', 'Nomor HP tersebut sudah digunakan warga lain.');
                redirect('warga/setting');
            }
            if ($this->simkas->username_exists($phone, $this->currentUser['id'])) {
                $this->session->set_flashdata('error', 'Nomor HP tersebut sudah digunakan sebagai username akun lain.');
                redirect('warga/setting');
            }
            $userData = array('nama' => trim((string) $this->input->post('nama', TRUE)));
            $wargaData = array(
                'nama' => $userData['nama'],
                'nik' => $nik,
                'no_hp' => $phone,
                'email' => trim((string) $this->input->post('email', TRUE)) ?: NULL,
                'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
                'blok' => trim((string) $this->input->post('blok', TRUE)),
                'no_rumah' => trim((string) $this->input->post('no_rumah', TRUE))
            );
            $upload = $this->upload_file('foto', 'uploads/profil', 'jpg|jpeg|png', 2048);
            if (isset($upload['error']) && isset($_FILES['foto']['name']) && $_FILES['foto']['name'] !== '') {
                $this->session->set_flashdata('error', $upload['error']);
                redirect('warga/setting');
            }
            if (isset($upload['file'])) {
                $userData['foto_profil'] = $upload['file'];
            }
            $newPassword = (string) $this->input->post('password_baru');
            if ($newPassword !== '') {
                if (!password_verify((string) $this->input->post('password_lama'), $account->password_hash)) {
                    $this->session->set_flashdata('error', 'Password lama tidak cocok.');
                    redirect('warga/setting');
                }
                $userData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            if ($this->simkas->update_profile($this->currentUser['id'], $userData, $wargaData)) {
                $sessionUser = $this->currentUser;
                $sessionUser['nama'] = $userData['nama'];
                $this->session->set_userdata('user', $sessionUser);
                $this->session->set_flashdata('success', 'Profil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Profil tidak dapat disimpan.');
            }
            redirect('warga/setting');
        }
        $this->render('profile', array('judul' => 'Pengaturan Profil', 'account' => $account, 'person' => $person));
    }

    public function tanggal_valid($value)
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}
