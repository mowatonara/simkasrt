<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_login('admin');
    }

    public function warga()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $action = $this->input->post('_action', TRUE);
            if ($action === 'save') {
                if (!$this->save_warga()) {
                    $this->session->set_flashdata('open_warga_modal', TRUE);
                    $editId = (int) $this->input->post('id');
                    if ($editId) {
                        $this->session->set_flashdata('edit_warga_id', $editId);
                    }
                }
            } elseif ($action === 'buat_tagihan') {
                $this->buat_tagihan();
            }
            redirect('admin/warga');
        }

        $search = trim((string) $this->input->get('q', TRUE));
        $perPage = (int) $this->input->get('per_page');
        if (!in_array($perPage, array(10, 20, 50, 100), TRUE)) {
            $perPage = 10;
        }
        $page = max(1, (int) $this->input->get('page'));
        $warga = $this->simkas->warga_list($search, $perPage, ($page - 1) * $perPage);
        $statuses = array();
        foreach ($warga as $person) {
            $rows = $this->simkas->tagihan_warga($person->id);
            $statuses[$person->id] = array(
                'menunggak' => count(array_filter($rows, function ($row) {
                    return $row->status === 'belum_bayar';
                })),
                'menunggu' => count(array_filter($rows, function ($row) {
                    return $row->status === 'menunggu_verifikasi';
                }))
            );
        }
        $editId = (int) $this->input->get('edit');
        if (!$editId) {
            $editId = (int) $this->session->flashdata('edit_warga_id');
        }
        $edit = $this->simkas->warga_by_id($editId);
        $openModal = (bool) $this->session->flashdata('open_warga_modal') || (bool) $edit;
        $data = array(
            'judul' => 'Data Warga',
            'warga' => $warga,
            'statuses' => $statuses,
            'q' => $search,
            'per_page' => $perPage,
            'page' => $page,
            'total' => $this->simkas->warga_count($search),
            'edit' => $edit,
            'edit_account' => $edit ? $this->simkas->warga_account($edit->id) : NULL,
            'open_modal' => $openModal
        );
        $this->render('admin/warga', $data);
    }

    private function save_warga()
    {
        $id = (int) $this->input->post('id');
        if ($id && !$this->simkas->warga_by_id($id)) {
            $this->session->set_flashdata('error', 'Data warga yang akan diperbarui tidak ditemukan.');
            return FALSE;
        }
        $editAccount = $id ? $this->simkas->warga_account($id) : NULL;
        $rawPhone = trim((string) $this->input->post('no_hp', TRUE));
        $phone = normalize_phone_login($rawPhone);
        $password = (string) $this->input->post('password');

        $this->form_validation->set_rules('nama', 'Nama warga', 'required|trim|max_length[120]');
        $this->form_validation->set_rules('nik', 'NIK', 'required|trim|exact_length[16]|numeric');
        $this->form_validation->set_rules('no_hp', 'Nomor HP', 'required|trim|max_length[25]');
        $this->form_validation->set_rules(
            'password',
            'Password warga',
            (!$id || !$editAccount) ? 'required|min_length[8]' : 'min_length[8]'
        );
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('blok', 'Blok', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('no_rumah', 'Nomor rumah', 'required|trim|max_length[20]');
        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', trim(strip_tags(validation_errors(' ', ' '))));
            return FALSE;
        }
        $nik = trim((string) $this->input->post('nik', TRUE));
        if ($this->simkas->nik_exists($nik, $id)) {
            $this->session->set_flashdata('error', 'NIK tersebut sudah terdaftar.');
            return FALSE;
        }
        if (!preg_match('/^[0-9]{8,15}$/', $phone)) {
            $this->session->set_flashdata('error', 'Nomor HP harus berisi 8 sampai 15 digit.');
            return FALSE;
        }
        if ($this->simkas->phone_exists($phone, $id)) {
            $this->session->set_flashdata('error', 'Nomor HP tersebut sudah digunakan warga lain.');
            return FALSE;
        }
        if ($this->simkas->username_exists($phone, $editAccount ? $editAccount->id : 0)) {
            $this->session->set_flashdata('error', 'Nomor HP tersebut sudah digunakan sebagai username akun lain.');
            return FALSE;
        }
        $row = array(
            'nama' => trim((string) $this->input->post('nama', TRUE)),
            'nik' => $nik,
            'no_hp' => $phone,
            'email' => trim((string) $this->input->post('email', TRUE)) ?: NULL,
            'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
            'blok' => trim((string) $this->input->post('blok', TRUE)),
            'no_rumah' => trim((string) $this->input->post('no_rumah', TRUE)),
            'status_aktif' => 1
        );
        $result = $this->simkas->save_warga($row, $id ?: NULL, $password);
        if (!$result['ok']) {
            $this->session->set_flashdata('error', $result['message']);
            return FALSE;
        }
        $this->session->set_flashdata(
            'success',
            $id ? 'Data warga dan akun login diperbarui.' : 'Data warga dan akun login berhasil dibuat.'
        );
        return TRUE;
    }

    private function buat_tagihan()
    {
        $wargaId = (int) $this->input->post('data_warga_id');
        $periode = (string) $this->input->post('periode', TRUE);
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            $this->session->set_flashdata('error', 'Periode tagihan tidak valid.');
            return;
        }
        $result = $this->simkas->create_tagihan($wargaId, $periode, $this->currentUser['id']);
        $this->session->set_flashdata(
            $result['error'] ? 'error' : 'success',
            $result['error'] ?: ($result['count'] ? $result['count'] . ' tagihan dibuat dan notifikasi dikirim.' : 'Tagihan periode tersebut sudah tersedia.')
        );
    }

    public function status_warga($id)
    {
        $person = $this->simkas->warga_by_id($id);
        if (!$person) {
            show_404();
        }
        $this->render('admin/status_warga', array(
            'judul' => 'Status Iuran Warga',
            'person' => $person,
            'tagihan' => $this->simkas->tagihan_warga($id)
        ));
    }

    public function hapus_warga($id)
    {
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Metode tidak diizinkan.', 405);
        }
        $this->simkas->deactivate_warga($id);
        $this->session->set_flashdata('success', 'Warga dinonaktifkan; riwayat kas tetap disimpan.');
        redirect('admin/warga');
    }

    public function pembayaran()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $id = (int) $this->input->post('pembayaran_id');
            $decision = $this->input->post('keputusan', TRUE);
            $reason = trim((string) $this->input->post('alasan', TRUE));
            if (!in_array($decision, array('setujui', 'tolak'), TRUE)) {
                $this->session->set_flashdata('error', 'Keputusan verifikasi tidak valid.');
            } else {
                $result = $this->simkas->verify_payment(
                    $id,
                    $this->currentUser['id'],
                    $decision === 'setujui',
                    $reason
                );
                $this->session->set_flashdata($result['ok'] ? 'success' : 'error', $result['message']);
            }
            redirect('admin/pembayaran');
        }
        $this->render('admin/pembayaran', array(
            'judul' => 'Verifikasi Pembayaran',
            'pembayaran' => $this->simkas->pending_payments()
        ));
    }

    public function bukti($id)
    {
        $this->stream_payment_proof($this->simkas->payment_by_id($id));
    }

    public function iuran()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $action = $this->input->post('_action', TRUE);
            $id = (int) $this->input->post('id');
            if ($action === 'hapus') {
                $this->session->set_flashdata('success', $this->simkas->remove_iuran($id));
            } elseif ($action === 'simpan') {
                $this->form_validation->set_rules('nama', 'Nama iuran', 'required|trim|max_length[100]');
                $this->form_validation->set_rules('nominal', 'Nominal', 'required|numeric|greater_than_equal_to[0]');
                $this->form_validation->set_rules('frekuensi', 'Frekuensi', 'required|in_list[bulanan,sekali]');
                if (!$this->form_validation->run()) {
                    $this->session->set_flashdata('error', trim(strip_tags(validation_errors(' ', ' '))));
                } else {
                    $this->simkas->save_iuran(array(
                        'nama' => trim((string) $this->input->post('nama', TRUE)),
                        'nominal' => (float) $this->input->post('nominal'),
                        'frekuensi' => $this->input->post('frekuensi', TRUE),
                        'aktif' => 1
                    ), $id ?: NULL);
                    $this->session->set_flashdata('success', 'Jenis iuran disimpan.');
                }
            }
            redirect('admin/iuran');
        }
        $this->render('admin/iuran', array(
            'judul' => 'Iuran dan Tagihan',
            'iuran' => $this->simkas->iuran_list(),
            'edit' => $this->simkas->iuran_by_id((int) $this->input->get('edit'))
        ));
    }

    public function buku_kas()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('tanggal', 'Tanggal', 'required|callback_tanggal_valid');
            $this->form_validation->set_rules('kategori', 'Kategori', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required|trim|max_length[500]');
            $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric|greater_than[0]');
            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', trim(strip_tags(validation_errors(' ', ' '))));
            } else {
                $this->simkas->add_expense(array(
                    'kategori' => trim((string) $this->input->post('kategori', TRUE)),
                    'deskripsi' => trim((string) $this->input->post('deskripsi', TRUE)),
                    'jumlah' => (float) $this->input->post('jumlah'),
                    'tanggal' => $this->input->post('tanggal', TRUE),
                    'dicatat_oleh' => $this->currentUser['id']
                ));
                $this->session->set_flashdata('success', 'Pengeluaran dicatat.');
            }
            redirect('admin/buku-kas');
        }
        $year = (int) $this->input->get('tahun');
        $month = (int) $this->input->get('bulan');
        if ($year < 2000 || $year > (int) date('Y')) {
            $year = (int) date('Y');
        }
        $rows = $this->simkas->cash_rows($year, $month);
        $totals = array('masuk' => 0, 'keluar' => 0);
        foreach ($rows as $row) {
            $totals[$row->arah] += (float) $row->jumlah;
        }
        $this->render('cash/index', array(
            'judul' => 'Buku Kas',
            'transaksi' => $rows,
            'tahun' => $year,
            'bulan' => $month,
            'total_masuk' => $totals['masuk'],
            'total_keluar' => $totals['keluar']
        ));
    }

    public function cetak_kas()
    {
        $year = max(2000, min((int) $this->input->get('tahun'), (int) date('Y')));
        $month = (int) $this->input->get('bulan');
        $this->load->view('cash/print', array(
            'transaksi' => $this->simkas->cash_rows($year, $month),
            'tahun' => $year,
            'bulan' => $month
        ));
    }

    public function users()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $action = $this->input->post('_action', TRUE);
            $id = (int) $this->input->post('id');
            if ($action === 'hapus') {
                if ($id === (int) $this->currentUser['id']) {
                    $this->session->set_flashdata('error', 'Akun yang sedang dipakai tidak dapat dinonaktifkan.');
                } else {
                    $this->db->where('id', $id)->update('users', array('is_active' => 0));
                    $this->session->set_flashdata('success', 'Akun dinonaktifkan.');
                }
            } elseif ($action === 'simpan') {
                $postedRole = $this->input->post('role', TRUE);
                if ($postedRole === 'admin') {
                    $this->form_validation->set_rules('username', 'Username', 'required|trim|alpha_numeric|min_length[4]|max_length[60]');
                }
                $this->form_validation->set_rules('nama', 'Nama', 'required|trim|max_length[120]');
                $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,warga]');
                $this->form_validation->set_rules(
                    'password',
                    'Password',
                    $id ? 'min_length[8]' : 'required|min_length[8]'
                );
                if ($this->form_validation->run()) {
                    $role = $this->input->post('role', TRUE);
                    $wargaId = (int) $this->input->post('data_warga_id');
                    if ($role === 'warga' && !$wargaId) {
                        $this->session->set_flashdata('error', 'Pilih data warga untuk akun warga.');
                    } elseif ($role === 'warga' && $this->simkas->warga_account_exists($wargaId, $id)) {
                        $this->session->set_flashdata('error', 'Data warga tersebut sudah ditautkan ke akun lain.');
                    } else {
                        $warga = $role === 'warga' ? $this->simkas->warga_by_id($wargaId) : NULL;
                        if ($role === 'warga' && !$warga) {
                            $this->session->set_flashdata('error', 'Data warga tidak ditemukan.');
                        } else {
                            $username = $role === 'warga'
                                ? normalize_phone_login($warga->no_hp)
                                : trim((string) $this->input->post('username', TRUE));
                            if ($role === 'warga' && !preg_match('/^[0-9]{8,15}$/', $username)) {
                                $this->session->set_flashdata('error', 'Nomor HP warga harus berisi 8 sampai 15 digit sebelum akun dibuat.');
                                redirect('admin/users');
                                return;
                            }
                            $duplicate = $this->simkas->username_exists($username, $id);
                            if ($duplicate) {
                                $this->session->set_flashdata('error', 'Username sudah dipakai.');
                            } else {
                                $account = array(
                                    'username' => $username,
                                    'nama' => $role === 'warga' ? $warga->nama : trim((string) $this->input->post('nama', TRUE)),
                                    'role' => $role,
                                    'data_warga_id' => $role === 'warga' ? $wargaId : NULL,
                                    'is_active' => 1,
                                    'password' => (string) $this->input->post('password')
                                );
                                $this->simkas->save_user($account, $id ?: NULL);
                                $message = $role === 'warga'
                                    ? 'Akun warga disimpan. Username login mengikuti nomor HP.'
                                    : 'Akun pengguna disimpan.';
                                $this->session->set_flashdata('success', $message);
                            }
                        }
                    }
                } else {
                    $this->session->set_flashdata('error', trim(strip_tags(validation_errors(' ', ' '))));
                }
            }
            redirect('admin/users');
        }
        $this->render('admin/users', array(
            'judul' => 'Pengguna',
            'users' => $this->simkas->admin_users(),
            'edit' => $this->simkas->user_by_id((int) $this->input->get('edit')),
            'warga_tersedia' => $this->simkas->warga_without_account((int) $this->input->get('edit'))
        ));
    }

    public function setting()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('nama', 'Nama', 'required|trim|max_length[120]');
            $this->form_validation->set_rules('password_baru', 'Password baru', 'min_length[8]');
            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', trim(strip_tags(validation_errors(' ', ' '))));
                redirect('admin/setting');
            }
            $account = $this->simkas->user_by_id($this->currentUser['id']);
            $userData = array('nama' => trim((string) $this->input->post('nama', TRUE)));
            $photo = $this->upload_file('foto', 'uploads/profil', 'jpg|jpeg|png', 2048);
            if (isset($photo['error']) && isset($_FILES['foto']['name']) && $_FILES['foto']['name'] !== '') {
                $this->session->set_flashdata('error', $photo['error']);
                redirect('admin/setting');
            }
            if (isset($photo['file'])) {
                $userData['foto_profil'] = $photo['file'];
            }
            $newPassword = (string) $this->input->post('password_baru');
            if ($newPassword !== '') {
                if (!password_verify((string) $this->input->post('password_lama'), $account->password_hash)) {
                    $this->session->set_flashdata('error', 'Password lama tidak cocok.');
                    redirect('admin/setting');
                }
                $userData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
            $this->simkas->update_profile($this->currentUser['id'], $userData);
            $sessionUser = $this->currentUser;
            $sessionUser['nama'] = $userData['nama'];
            $this->session->set_userdata('user', $sessionUser);
            $this->session->set_flashdata('success', 'Profil diperbarui.');
            redirect('admin/setting');
        }
        $this->render('profile', array(
            'judul' => 'Pengaturan Profil',
            'account' => $this->simkas->user_by_id($this->currentUser['id']),
            'person' => NULL
        ));
    }

    public function tanggal_valid($value)
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}
