<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Simkas_model', 'simkas');
    }

    public function index()
    {
        if ($this->simkas->admin_exists()) {
            $this->session->set_flashdata('error', 'Akun pengurus sudah tersedia. Silakan masuk dengan akun admin.');
            redirect('login');
            return;
        }
        $data = array('errors' => '');
        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('nama', 'Nama pengurus', 'required|trim|max_length[120]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|alpha_numeric|min_length[4]|max_length[60]|is_unique[users.username]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[10]');
            $this->form_validation->set_rules('password_confirm', 'Konfirmasi password', 'required|matches[password]');
            if ($this->form_validation->run()) {
                $created = $this->simkas->create_first_admin(
                    trim((string) $this->input->post('username', TRUE)),
                    trim((string) $this->input->post('nama', TRUE)),
                    (string) $this->input->post('password')
                );
                if ($created) {
                    $this->session->set_flashdata('success', 'Akun pengurus pertama berhasil dibuat. Silakan masuk.');
                    redirect('login');
                }
                $data['errors'] = 'Akun admin sudah tersedia. Silakan masuk.';
            } else {
                $data['errors'] = trim(strip_tags(validation_errors(' ', ' ')));
            }
        }
        $this->load->view('auth/setup', $data);
    }
}
