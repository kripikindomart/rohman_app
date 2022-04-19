<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Instansi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
	}

	public function index()
	{
		$data['title'] = 'Profil Instansi';
		$data['toko'] = $this->admin->get('data_instansi', ['id' => 1]);
		$this->template->load('templates/dashboard', 'toko/index', $data);
	}

	public function proses_ubah(){
			$this->form_validation->set_rules('nama_toko', 'Nama Instansi', 'required|trim');
			$this->form_validation->set_rules('nama_aplikasi', 'Nama Aplikasi', 'required|trim');
	        $this->form_validation->set_rules('nama_pemilik', 'Nama Pemilik', 'required');
	        $this->form_validation->set_rules('no_telepon', 'No Telpon', 'required');
		if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
			redirect('instansi');
        } else {
        	$data = [
				'nama' => $this->input->post('nama_toko'),
				'nama_aplikasi' => $this->input->post('nama_aplikasi'),
				'nama_pemilik' => $this->input->post('nama_pemilik'),
				'no_telepon' => $this->input->post('no_telepon'),
				'alamat' => $this->input->post('alamat'),
				'logo' => $this->input->post('alamat'),
			];

			if($update = $this->admin->update('data_instansi', 'id', 1, $data)){
				$this->session->set_flashdata('success', 'Profil Instansi <strong>Berhasil</strong> Diubah!');
				redirect('instansi');
			} else {
				$this->session->set_flashdata('error', 'Profil Instansi <strong>Gagal</strong> Diubah!');
				redirect('instansi');
			}
        }
		
	}

}

/* End of file Toko.php */
/* Location: ./application/controllers/Toko.php */