<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Harga_barang extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Barang";
        $data['barang'] = $this->admin->get('barang');
        $data['supplier'] = $this->admin->get('supplier');
        $this->template->load('templates/dashboard', 'harga/data', $data);
    }

    private function _validasi()
    {
 

        $this->form_validation->set_rules('barang', 'Barang', 'required|trim');
        $this->form_validation->set_rules('supplier[]', 'Supplier', 'required');
        $this->form_validation->set_rules('harga_beli[]', 'Harga Beli', 'required');
        $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required');
    }

    public function simpan()
    {
       $this->_validasi();
       $supplier = $this->input->post('supplier');
       if ($this->form_validation->run() == false) {
            $jeson = [
                'status' => false,
                'message' => validation_errors()
            ];
        } else {
            $data = [];
            $id_barang = $this->input->post('barang');
            $harga_jual = $this->input->post('harga_jual');
            if (is_array($supplier)) {
                for($i = 0; $i < count($supplier); $i++){
                    array_push($data, ['id_barang' => $id_barang]);
                    $data[$i]['harga_jual'] = $harga_jual;
                    $data[$i]['id_supplier'] = $this->input->post('supplier')[$i];
                    $data[$i]['harga_satuan'] = $this->input->post('harga_beli')[$i];
                }
                $save = $this->db->insert_batch('harga_barang', $data);
                if ($save) {
                    $jeson = [
                        'status' => true,
                        'message' => 'Success ! berhasil merubah harga barang'
                    ];
                } else {
                    $jeson = [
                        'status' => false,
                        'message' => 'Failed ! Gagal merubaha ahrga barang'
                    ];
                }
            } else {
                    $data['id_barang'] =  $this->input->post('barang');
                    $data['harga_jual'] =  $this->input->post('harga_jual');
                    $data['id_supplier'] = $this->input->post('supplier');
                    $data['harga_satuan'] = $this->input->post('harga_beli');
                   $save = $this->admin->insert('harga_barang', $data);
                if ($save) {
                    $jeson = [
                        'status' => true,
                        'message' => 'Success ! berhasil merubah harga barang'
                    ];
                } else {
                    $jeson = [
                        'status' => false,
                        'message' => 'Failed ! Gagal merubaha ahrga barang'
                    ];
                } 
            }
            
        }

        echo json_encode($jeson);
    }

    public function getHargaOld()
    {
        $id = $this->input->post('id');
        $id_barang = $this->input->post('id_barang');

        $this->db->where(['id_supplier' => $id, 'id_barang' => $id_barang]);
        $harga = $this->db->get('harga_barang');
        if ($harga->num_rows() > 0) {
            $json = ['harga' =>$harga->row()];
        } else {
            $json = ['harga' => $this->input->post(null, true)];
        }

        echo json_encode($json);
    }

    public function add()
    {
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Barang";
            $data['supplier'] = $this->admin->get('supplier');

            // Mengenerate ID Barang
            $kode_terakhir = $this->admin->getMax('barang', 'id_barang');
            $kode_tambah = substr($kode_terakhir, -6, 6);
            $kode_tambah++;
            $number = str_pad($kode_tambah, 6, '0', STR_PAD_LEFT);
            $data['id_barang'] = 'B' . $number;
            $data['barang'] = $this->admin->get('barang');
            $this->template->load('templates/dashboard', 'harga/add', $data);
        } else {
            $input = $this->input->post(null, true);
            $insert = $this->admin->insert('barang', $input);

            if ($insert) {
                set_pesan('data berhasil disimpan');
                redirect('barang');
            } else {
                set_pesan('gagal menyimpan data');
                redirect('barang/add');
            }
        }
    }

    public function edit($getId)
    {
        $id = encode_php_tags($getId);
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Barang";
            $data['jenis'] = $this->admin->get('jenis');
            $data['satuan'] = $this->admin->get('satuan');
            $data['barang'] = $this->admin->get('barang', ['id_barang' => $id]);
            $this->template->load('templates/dashboard', 'barang/edit', $data);
        } else {
            $input = $this->input->post(null, true);
            $update = $this->admin->update('barang', 'id_barang', $id, $input);

            if ($update) {
                set_pesan('data berhasil disimpan');
                redirect('barang');
            } else {
                set_pesan('gagal menyimpan data');
                redirect('barang/edit/' . $id);
            }
        }
    }

    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('barang', 'id_barang', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('barang');
    }

    public function getstok($getId)
    {
        $id = encode_php_tags($getId);
        $query = $this->admin->cekStok($id);
        output_json($query);
    }

}

/* End of file Harga_barang.php */
/* Location: ./application/controllers/Harga_barang.php */