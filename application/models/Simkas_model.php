<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Simkas_model extends CI_Model
{
    public function by_username($username)
    {
        return $this->db->where('username', $username)->limit(1)->get('users')->row();
    }

    public function admin_exists()
    {
        return $this->db->where('role', 'admin')->count_all_results('users') > 0;
    }

    public function create_first_admin($username, $name, $password)
    {
        if ($this->admin_exists()) {
            return FALSE;
        }
        return $this->db->insert('users', array(
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'nama' => $name,
            'role' => 'admin',
            'is_active' => 1
        ));
    }

    public function user_by_id($id)
    {
        return $this->db->where('id', (int) $id)->get('users')->row();
    }

    public function warga_account($wargaId)
    {
        return $this->db->where('data_warga_id', (int) $wargaId)->limit(1)->get('users')->row();
    }

    public function username_exists($username, $excludeUserId = 0)
    {
        $this->db->where('username', (string) $username);
        if ($excludeUserId) {
            $this->db->where('id !=', (int) $excludeUserId);
        }
        return $this->db->count_all_results('users') > 0;
    }

    public function warga_account_exists($wargaId, $excludeUserId = 0)
    {
        $this->db->where('data_warga_id', (int) $wargaId);
        if ($excludeUserId) {
            $this->db->where('id !=', (int) $excludeUserId);
        }
        return $this->db->count_all_results('users') > 0;
    }

    public function dashboard_summary($year, $month)
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $next = date('Y-m-d', strtotime($start . ' +1 month'));

        $all = $this->db->query(
            "SELECT
                COALESCE(SUM(CASE WHEN arah='masuk' THEN jumlah ELSE 0 END), 0) AS masuk,
                COALESCE(SUM(CASE WHEN arah='keluar' THEN jumlah ELSE 0 END), 0) AS keluar
             FROM transaksi_kas"
        )->row();
        $thisMonth = $this->db->query(
            "SELECT
                COALESCE(SUM(CASE WHEN arah='masuk' THEN jumlah ELSE 0 END), 0) AS masuk,
                COALESCE(SUM(CASE WHEN arah='keluar' THEN jumlah ELSE 0 END), 0) AS keluar
             FROM transaksi_kas WHERE tanggal >= ? AND tanggal < ?",
            array($start, $next)
        )->row();

        $months = $this->db->query(
            "SELECT MONTH(tanggal) AS bulan,
                SUM(CASE WHEN arah='masuk' THEN jumlah ELSE 0 END) AS masuk,
                SUM(CASE WHEN arah='keluar' THEN jumlah ELSE 0 END) AS keluar
             FROM transaksi_kas
             WHERE tanggal >= ? AND tanggal < ?
             GROUP BY MONTH(tanggal)",
            array($year . '-01-01', ($year + 1) . '-01-01')
        )->result();

        $graph = array_fill(1, 12, array('masuk' => 0, 'keluar' => 0));
        foreach ($months as $row) {
            $graph[(int) $row->bulan] = array(
                'masuk' => (float) $row->masuk,
                'keluar' => (float) $row->keluar
            );
        }
        $pending = $this->db->where('status', 'menunggu')->count_all_results('pembayaran');

        return array(
            'saldo' => (float) $all->masuk - (float) $all->keluar,
            'masuk_bulan' => (float) $thisMonth->masuk,
            'keluar_bulan' => (float) $thisMonth->keluar,
            'jumlah_warga' => $this->db->where('status_aktif', 1)->count_all_results('data_warga'),
            'pembayaran_menunggu' => $pending,
            'grafik_masuk' => array_values(array_map(function ($item) { return $item['masuk']; }, $graph)),
            'grafik_keluar' => array_values(array_map(function ($item) { return $item['keluar']; }, $graph)),
            'transaksi_terakhir' => $this->db->order_by('tanggal', 'DESC')->order_by('id', 'DESC')
                ->limit(7)->get('transaksi_kas')->result()
        );
    }

    public function warga_count($search = '')
    {
        $this->db->where('status_aktif', 1);
        $this->apply_warga_search($search);
        return $this->db->count_all_results('data_warga');
    }

    public function warga_list($search = '', $limit = 10, $offset = 0)
    {
        $this->db->where('status_aktif', 1);
        $this->apply_warga_search($search);
        return $this->db->order_by('nama', 'ASC')->limit((int) $limit, (int) $offset)
            ->get('data_warga')->result();
    }

    private function apply_warga_search($search)
    {
        $search = trim((string) $search);
        if ($search !== '') {
            $this->db->group_start()
                ->like('nama', $search)
                ->or_like('nik', $search)
                ->or_like('no_hp', $search)
                ->or_like('blok', $search)
                ->or_like('no_rumah', $search)
                ->group_end();
        }
    }

    public function warga_by_id($id)
    {
        return $this->db->where('id', (int) $id)->get('data_warga')->row();
    }

    public function save_warga($data, $id = NULL, $password = '')
    {
        if (!$id && $password === '') {
            return array('ok' => FALSE, 'message' => 'Password wajib diisi untuk membuat akun login warga.');
        }

        $this->db->trans_begin();
        if ($id) {
            $this->db->where('id', (int) $id)->update('data_warga', $data);
            $wargaId = (int) $id;
        } else {
            $this->db->insert('data_warga', $data);
            $wargaId = (int) $this->db->insert_id();
        }

        if (!$wargaId || $this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('ok' => FALSE, 'message' => 'Data warga gagal disimpan; perubahan dibatalkan.');
        }

        $account = $this->warga_account($wargaId);
        $accountData = array(
            'username' => $data['no_hp'],
            'nama' => $data['nama'],
            'role' => 'warga',
            'data_warga_id' => $wargaId
        );
        if ($password !== '') {
            $accountData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($account) {
            $this->db->where('id', (int) $account->id)->update('users', $accountData);
        } else {
            if ($password === '') {
                $this->db->trans_rollback();
                return array('ok' => FALSE, 'message' => 'Password wajib diisi untuk membuat akun login warga.');
            }
            $accountData['is_active'] = 1;
            $this->db->insert('users', $accountData);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('ok' => FALSE, 'message' => 'Data warga atau akun login gagal disimpan; perubahan dibatalkan.');
        }
        $this->db->trans_commit();
        return array('ok' => TRUE, 'warga_id' => $wargaId);
    }

    public function nik_exists($nik, $excludeId = 0)
    {
        $this->db->where('nik', $nik);
        if ($excludeId) {
            $this->db->where('id !=', (int) $excludeId);
        }
        return $this->db->count_all_results('data_warga') > 0;
    }

    public function phone_exists($phone, $excludeId = 0)
    {
        $rows = $this->db->select('id, no_hp')->get('data_warga')->result();
        foreach ($rows as $row) {
            if ((int) $row->id !== (int) $excludeId && normalize_phone_login($row->no_hp) === (string) $phone) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function deactivate_warga($id)
    {
        return $this->db->where('id', (int) $id)->update('data_warga', array('status_aktif' => 0));
    }

    public function tagihan_warga($wargaId)
    {
        return $this->db->select('t.*, i.nama AS nama_iuran')
            ->from('tagihan t')->join('jenis_iuran i', 'i.id=t.jenis_iuran_id')
            ->where('t.data_warga_id', (int) $wargaId)
            ->order_by('t.periode', 'DESC')->order_by('i.nama', 'ASC')
            ->get()->result();
    }

    public function create_tagihan($wargaId, $periode, $adminId)
    {
        $warga = $this->warga_by_id($wargaId);
        if (!$warga || !(int) $warga->status_aktif) {
            return array('count' => 0, 'error' => 'Data warga tidak aktif.');
        }
        $periodDate = $periode . '-01';
        $dueDate = date('Y-m-t', strtotime($periodDate));
        $jenis = $this->db->where(array('aktif' => 1, 'frekuensi' => 'bulanan'))
            ->order_by('id', 'ASC')->get('jenis_iuran')->result();
        if (!$jenis) {
            return array('count' => 0, 'error' => 'Belum ada jenis iuran bulanan aktif.');
        }

        $this->db->trans_begin();
        $created = 0;
        $user = $this->db->where('data_warga_id', (int) $wargaId)->get('users')->row();
        foreach ($jenis as $item) {
            $this->db->query(
                "INSERT IGNORE INTO tagihan
                 (data_warga_id, jenis_iuran_id, periode, jatuh_tempo, nominal, status, dibuat_oleh)
                 VALUES (?, ?, ?, ?, ?, 'belum_bayar', ?)",
                array($wargaId, $item->id, $periodDate, $dueDate, $item->nominal, $adminId)
            );
            if ($this->db->affected_rows() === 1) {
                $created++;
                if ($user) {
                    $message = 'Halo Bapak/Ibu ' . $warga->nama . '. Anda memiliki tagihan '
                        . $item->nama . ' sebesar Rp ' . number_format($item->nominal, 0, ',', '.')
                        . ' untuk periode ' . date('m/Y', strtotime($periodDate))
                        . '. Harap segera melakukan pembayaran. Terima kasih.';
                    $tagihanId = $this->db->insert_id();
                    $this->db->insert('notifikasi', array(
                        'user_id' => $user->id,
                        'tagihan_id' => $tagihanId,
                        'pesan' => $message
                    ));
                }
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('count' => 0, 'error' => 'Pembuatan tagihan gagal.');
        }
        $this->db->trans_commit();
        return array('count' => $created, 'error' => NULL);
    }

    public function iuran_list($includeInactive = TRUE)
    {
        if (!$includeInactive) {
            $this->db->where('aktif', 1);
        }
        return $this->db->order_by('nama', 'ASC')->get('jenis_iuran')->result();
    }

    public function iuran_by_id($id)
    {
        return $this->db->where('id', (int) $id)->get('jenis_iuran')->row();
    }

    public function save_iuran($data, $id = NULL)
    {
        if ($id) {
            $this->db->where('id', (int) $id)->update('jenis_iuran', $data);
            return (int) $id;
        }
        $this->db->insert('jenis_iuran', $data);
        return $this->db->insert_id();
    }

    public function remove_iuran($id)
    {
        $used = $this->db->where('jenis_iuran_id', (int) $id)->count_all_results('tagihan')
            + $this->db->where('jenis_iuran_id', (int) $id)->count_all_results('pembayaran');
        if ($used > 0) {
            $this->db->where('id', (int) $id)->update('jenis_iuran', array('aktif' => 0));
            return 'Jenis iuran memiliki riwayat dan dinonaktifkan.';
        }
        $this->db->where('id', (int) $id)->delete('jenis_iuran');
        return 'Jenis iuran dihapus.';
    }

    public function pending_payments()
    {
        return $this->db->select('p.*, w.nama AS nama_warga, i.nama AS nama_iuran, t.periode')
            ->from('pembayaran p')->join('data_warga w', 'w.id=p.data_warga_id')
            ->join('jenis_iuran i', 'i.id=p.jenis_iuran_id')
            ->join('tagihan t', 't.id=p.tagihan_id', 'left')
            ->where('p.status', 'menunggu')->order_by('p.dibuat_pada', 'ASC')->get()->result();
    }

    public function pending_payment_count()
    {
        return $this->db->where('status', 'menunggu')->count_all_results('pembayaran');
    }

    public function payment_history($wargaId)
    {
        return $this->db->select('p.*, i.nama AS nama_iuran, t.periode')
            ->from('pembayaran p')->join('jenis_iuran i', 'i.id=p.jenis_iuran_id')
            ->join('tagihan t', 't.id=p.tagihan_id', 'left')
            ->where('p.data_warga_id', (int) $wargaId)
            ->order_by('p.dibuat_pada', 'DESC')->get()->result();
    }

    public function payment_by_id($id)
    {
        return $this->db->where('id', (int) $id)->get('pembayaran')->row();
    }

    public function unpaid_bills($wargaId)
    {
        return $this->db->select('t.*, i.nama AS nama_iuran')
            ->from('tagihan t')->join('jenis_iuran i', 'i.id=t.jenis_iuran_id')
            ->where('t.data_warga_id', (int) $wargaId)
            ->where('t.status', 'belum_bayar')->order_by('t.periode', 'ASC')->get()->result();
    }

    public function submit_payment($data, $billId = NULL)
    {
        $this->db->trans_begin();
        if ($billId) {
            $bill = $this->db->query(
                'SELECT * FROM tagihan WHERE id = ? AND data_warga_id = ? FOR UPDATE',
                array($billId, $data['data_warga_id'])
            )->row();
            if (!$bill || $bill->status !== 'belum_bayar') {
                $this->db->trans_rollback();
                return array('ok' => FALSE, 'message' => 'Tagihan sudah diproses atau bukan milik akun ini.');
            }
            $data['tagihan_id'] = $bill->id;
            $data['jenis_iuran_id'] = $bill->jenis_iuran_id;
            $data['jumlah'] = $bill->nominal;
            $this->db->insert('pembayaran', $data);
            $this->db->where('id', $bill->id)->update('tagihan', array('status' => 'menunggu_verifikasi'));
        } else {
            $jenis = $this->iuran_by_id($data['jenis_iuran_id']);
            if (!$jenis || !$jenis->aktif || $jenis->frekuensi !== 'sekali') {
                $this->db->trans_rollback();
                return array('ok' => FALSE, 'message' => 'Jenis pembayaran tidak valid.');
            }
            $this->db->insert('pembayaran', $data);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('ok' => FALSE, 'message' => 'Pembayaran tidak dapat disimpan.');
        }
        $this->db->trans_commit();
        return array('ok' => TRUE, 'message' => 'Pengajuan pembayaran dikirim untuk diverifikasi.');
    }

    public function verify_payment($paymentId, $adminId, $approve, $reason = '')
    {
        $this->db->trans_begin();
        $payment = $this->db->query(
            'SELECT * FROM pembayaran WHERE id = ? FOR UPDATE',
            array((int) $paymentId)
        )->row();
        if (!$payment || $payment->status !== 'menunggu') {
            $this->db->trans_rollback();
            return array('ok' => FALSE, 'message' => 'Pembayaran sudah diproses atau tidak ditemukan.');
        }
        if (!$approve && trim($reason) === '') {
            $this->db->trans_rollback();
            return array('ok' => FALSE, 'message' => 'Alasan penolakan harus diisi.');
        }

        $newStatus = $approve ? 'terverifikasi' : 'ditolak';
        $this->db->where('id', $payment->id)->update('pembayaran', array(
            'status' => $newStatus,
            'alasan_penolakan' => $approve ? NULL : trim($reason),
            'diverifikasi_oleh' => $adminId,
            'diverifikasi_pada' => date('Y-m-d H:i:s')
        ));

        if ($payment->tagihan_id) {
            $billStatus = $approve ? 'lunas' : 'belum_bayar';
            $this->db->where('id', $payment->tagihan_id)
                ->update('tagihan', array('status' => $billStatus));
        }

        if ($approve) {
            $jenis = $this->iuran_by_id($payment->jenis_iuran_id);
            $warga = $this->warga_by_id($payment->data_warga_id);
            $this->db->insert('transaksi_kas', array(
                'pembayaran_id' => $payment->id,
                'arah' => 'masuk',
                'kategori' => $jenis ? $jenis->nama : 'Pembayaran warga',
                'deskripsi' => 'Pembayaran ' . ($warga ? $warga->nama : 'warga #' . $payment->data_warga_id),
                'jumlah' => $payment->jumlah,
                'tanggal' => $payment->tanggal_bayar,
                'dicatat_oleh' => $adminId
            ));
        }

        $account = $this->db->where('data_warga_id', $payment->data_warga_id)->get('users')->row();
        if ($account) {
            $notice = $approve
                ? 'Pembayaran Anda telah diverifikasi.'
                : 'Pembayaran ditolak. Alasan: ' . trim($reason);
            $this->db->insert('notifikasi', array(
                'user_id' => $account->id,
                'tagihan_id' => $payment->tagihan_id,
                'pesan' => $notice
            ));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('ok' => FALSE, 'message' => 'Verifikasi gagal; perubahan dibatalkan.');
        }
        $this->db->trans_commit();
        return array('ok' => TRUE, 'message' => $approve ? 'Pembayaran disetujui.' : 'Pembayaran ditolak.');
    }

    public function cash_rows($year, $month = 0)
    {
        $this->db->select(
            "tk.*, CASE
                WHEN tk.pembayaran_id IS NOT NULL
                    THEN CONCAT('Pembayaran ', COALESCE(w.nama, CONCAT('warga #', p.data_warga_id)))
                ELSE tk.deskripsi
             END AS deskripsi_tampil",
            FALSE
        )->from('transaksi_kas tk')
            ->join('pembayaran p', 'p.id = tk.pembayaran_id', 'left')
            ->join('data_warga w', 'w.id = p.data_warga_id', 'left')
            ->where('tk.tanggal >=', $year . '-01-01')
            ->where('tk.tanggal <', ($year + 1) . '-01-01');
        if ($month >= 1 && $month <= 12) {
            $this->db->where('MONTH(tk.tanggal) =', (int) $month, FALSE);
        }
        return $this->db->order_by('tk.tanggal', 'DESC')->order_by('tk.id', 'DESC')->get()->result();
    }

    public function add_expense($data)
    {
        $data['arah'] = 'keluar';
        $this->db->insert('transaksi_kas', $data);
        return $this->db->insert_id();
    }

    public function add_income($data)
    {
        $data['arah'] = 'masuk';
        $data['pembayaran_id'] = NULL;
        $this->db->insert('transaksi_kas', $data);
        return $this->db->insert_id();
    }

    public function admin_users()
    {
        return $this->db->select('u.*, w.nama AS nama_warga')
            ->from('users u')->join('data_warga w', 'w.id=u.data_warga_id', 'left')
            ->order_by('u.role', 'ASC')->order_by('u.nama', 'ASC')->get()->result();
    }

    public function warga_without_account($excludeUserId = 0)
    {
        // Fetch the account before building the warga query; both calls share one Query Builder.
        $current = $excludeUserId ? $this->user_by_id($excludeUserId) : NULL;

        $this->db->select('w.*')->from('data_warga w')
            ->join('users u', 'u.data_warga_id=w.id', 'left')
            ->where('w.status_aktif', 1)->group_start()->where('u.id IS NULL', NULL, FALSE);
        if ($current && $current->data_warga_id) {
            $this->db->or_where('w.id', (int) $current->data_warga_id);
        }
        $this->db->group_end();
        return $this->db->order_by('w.nama', 'ASC')->get()->result();
    }

    public function save_user($data, $id = NULL)
    {
        $password = isset($data['password']) ? $data['password'] : '';
        unset($data['password']);
        if ($data['role'] === 'admin') {
            $data['data_warga_id'] = NULL;
        }
        if ($id) {
            if ($password !== '') {
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $this->db->where('id', (int) $id)->update('users', $data);
            return (int) $id;
        }
        $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function delete_user($id)
    {
        $this->db->where('id', (int) $id)->delete('users');
    }

    public function notifications($userId, $limit = 8)
    {
        return $this->db->where('user_id', (int) $userId)
            ->order_by('dibuat_pada', 'DESC')->limit((int) $limit)->get('notifikasi')->result();
    }

    public function unread_count($userId)
    {
        return $this->db->where('user_id', (int) $userId)
            ->where('dibaca_pada IS NULL', NULL, FALSE)->count_all_results('notifikasi');
    }

    public function read_notification($id, $userId)
    {
        $notice = $this->db->where(array('id' => (int) $id, 'user_id' => (int) $userId))
            ->get('notifikasi')->row();
        if (!$notice) {
            return NULL;
        }
        $this->db->where('id', $notice->id)->update('notifikasi', array('dibaca_pada' => date('Y-m-d H:i:s')));
        return $notice;
    }

    public function update_profile($userId, $userData, $wargaData = NULL)
    {
        $this->db->trans_begin();
        if ($wargaData && isset($wargaData['no_hp'])) {
            $userData['username'] = $wargaData['no_hp'];
        }
        $this->db->where('id', (int) $userId)->update('users', $userData);
        if ($wargaData) {
            $user = $this->user_by_id($userId);
            if ($user && $user->data_warga_id) {
                $this->db->where('id', $user->data_warga_id)->update('data_warga', $wargaData);
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }
        $this->db->trans_commit();
        return TRUE;
    }
}
