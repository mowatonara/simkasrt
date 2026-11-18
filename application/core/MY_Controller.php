<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $currentUser;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Simkas_model', 'simkas');
        $this->currentUser = $this->session->userdata('user');
    }

    protected function require_login($roles = array('admin', 'warga'))
    {
        if (!$this->currentUser) {
            redirect('login');
            exit;
        }
        $account = $this->simkas->user_by_id($this->currentUser['id']);
        if (!$account || (int) $account->is_active !== 1) {
            $this->session->sess_destroy();
            redirect('login');
            exit;
        }
        $this->currentUser = array(
            'id' => (int) $account->id,
            'nama' => $account->nama,
            'role' => $account->role,
            'data_warga_id' => $account->data_warga_id ? (int) $account->data_warga_id : NULL
        );
        $this->session->set_userdata('user', $this->currentUser);
        if (!in_array($this->currentUser['role'], (array) $roles, TRUE)) {
            show_error('Anda tidak memiliki akses ke halaman ini.', 403);
        }
    }

    protected function render($view, $data = array())
    {
        $data['current_user'] = $this->currentUser;
        $data['role'] = $this->currentUser ? $this->currentUser['role'] : NULL;
        $data['notices'] = array();
        $data['unread_count'] = 0;
        $data['pending_payment_count'] = 0;
        if ($this->currentUser && $this->currentUser['role'] === 'warga') {
            $data['notices'] = $this->simkas->notifications($this->currentUser['id']);
            $data['unread_count'] = $this->simkas->unread_count($this->currentUser['id']);
        } elseif ($this->currentUser && $this->currentUser['role'] === 'admin') {
            $data['pending_payment_count'] = $this->simkas->pending_payment_count();
        }
        $this->load->view('layout/header', $data);
        $this->load->view($view, $data);
        $this->load->view('layout/footer', $data);
    }

    protected function upload_file($field, $folder, $types, $maxSize = 2048)
    {
        $path = FCPATH . trim($folder, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0755, TRUE);
        }
        $this->load->library('upload');
        $this->upload->initialize(array(
            'upload_path' => $path,
            'allowed_types' => $types,
            'max_size' => $maxSize,
            'encrypt_name' => TRUE,
            'detect_mime' => TRUE,
            'mod_mime_fix' => TRUE
        ), TRUE);
        if (!$this->upload->do_upload($field)) {
            return array('error' => strip_tags($this->upload->display_errors('', '')));
        }
        return array('file' => $this->upload->data('file_name'));
    }

    protected function stream_payment_proof($payment)
    {
        if (!$payment || !$payment->bukti_file) {
            show_404();
        }
        if ($this->currentUser['role'] === 'warga' &&
            (int) $payment->data_warga_id !== (int) $this->currentUser['data_warga_id']) {
            show_error('Anda tidak memiliki akses ke bukti pembayaran ini.', 403);
        }
        $path = APPPATH . '../private_uploads/bukti/' . basename($payment->bukti_file);
        if (!is_file($path)) {
            show_404();
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $path) : 'application/octet-stream';
        if ($finfo) {
            finfo_close($finfo);
        }
        if (!in_array($mime, array('image/jpeg', 'image/png', 'application/pdf'), TRUE)) {
            $mime = 'application/octet-stream';
        }
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="bukti-' . (int) $payment->id . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
