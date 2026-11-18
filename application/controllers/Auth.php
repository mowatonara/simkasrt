<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Simkas_model', 'simkas');
    }

    public function login()
    {
        if (!$this->simkas->admin_exists()) {
            redirect('setup');
            return;
        }
        $loggedIn = $this->session->userdata('user');
        if ($loggedIn) {
            redirect('dashboard');
        }

        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[60]');
            $this->form_validation->set_rules('password', 'Password', 'required');
            if ($this->form_validation->run()) {
                $username = trim((string) $this->input->post('username', TRUE));
                $password = (string) $this->input->post('password');
                $user = $this->simkas->by_username($username);
                if ($user && (int) $user->is_active === 1 &&
                    password_verify($password, $user->password_hash)) {
                    $this->session->sess_regenerate(TRUE);
                    $this->session->set_userdata('user', array(
                        'id' => (int) $user->id,
                        'nama' => $user->nama,
                        'role' => $user->role,
                        'data_warga_id' => $user->data_warga_id ? (int) $user->data_warga_id : NULL
                    ));
                    redirect('dashboard');
                }
                $this->session->set_flashdata('error', 'Username atau password tidak sesuai.');
                redirect('login');
            }
        }
        $this->load->view('auth/login');
    }

    public function lupa_password()
    {
        $message = "Halo, Admin\n" .
            "Saya Warga RT 01 MMTR lupa password Aplikasi SIMKAS-RT,\n" .
            "Atas Nama : (Isi Nama Warga)\n" .
            "Mohon bantu reset password\n\n" .
            "Terima Kasih";

        redirect('https://wa.me/6281278727025?text=' . rawurlencode($message));
    }

    public function logout()
    {
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Metode tidak diizinkan.', 405);
        }
        $this->session->sess_destroy();
        redirect('login');
    }
}
