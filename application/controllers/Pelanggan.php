<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pelanggan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->model('Supplier_model', '_db');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Pelanggan";
        $data['supplier'] = $this->admin->get('pelanggan');
        $this->template->load('templates/dashboard', 'pelanggan/data', $data);
    }

    public function barang()
    {
        $data['title'] = "Supplier";
        $data['supplier'] = $this->admin->get('supplier');
        $data['barang'] = $this->admin->get('barang');
        $this->template->load('templates/dashboard', 'supplier/barang', $data);
    }

    public function getDataBarang()
    {

       $post =  ($this->input->post(null,true));
       $id = $post['id_barang'];
       $this->db->where(['id_barang' => $id]);
       $get = $this->db->get('barang');
       if ($get->num_rows() > 0) {
           $json = ['status' => true, 'data' => $get->row()];
       } else {
            $json = ['status' => false, 'data' => 'Error data'];
       }

        ob_start();
        header('Content-Type: application/json');
        echo json_encode($json);
        ob_end_flush();
    }

    public function getBarangSupplier()
     {
        $data_id = $this->input->post('id');
        $this->_db->where = ['supplier_barang.id_supplier' => $data_id];
        $data_menu = $this->_db->get_datatables();
                $data = array();

            $no = $_POST['start'];

            foreach ($data_menu as $r) {

                $no++;
                $row = array();
                $row[] = $r->id_barang;
                $row[] = $r->nama_barang;
                $row[] = '<input id-barang = "'.$r->id_barang.'" id-supplier = "'.$r->id_supplier.'" type="number" class="form-control harga_supplier uang"  value="'.$r->harga_supplier.'">';
                $row[] = '<input id-barang = "'.$r->id_barang.'" id-supplier = "'.$r->id_supplier.'" type="number" class="form-control harga_jual uang"  value="'.$r->harga_satuan.'" disabled>';
                //$row[] = $r->sesi_kuliah;
                //add html for action
                $row[] = '
                <button type="button" class="btn btn-sm btn-danger delete" title="Delete" id="delete" data-id = "'.$r->id_barang.'" data-supplier = "'.$r->id_supplier.'"><i class="fa fa-trash"></i> Delete</button></div>';
                $data[] = $row;
            }

            $json_data = [
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->_db->count_all(),
                "recordsFiltered" => $this->_db->count_filtered(),
                'data' => $data
            ];

            echo json_encode($json_data);
    }

    public function barang_to_supplier()
    {
      
        
        $post = $this->input->post(null, true);
        if (empty($post) || !($this->input->post('id_barang')) || !($this->input->post('supplier'))) {
           $json = [
                'status' => false,
                'message' => 'Failed ! Supplier dan Barang tidak boleh kosong'
            ]; 
            ob_start();
            header('Content-Type: application/json');
            echo json_encode($json);
            ob_end_flush();
            return false;
        } 
        $data = [
            'id_supplier' => $post['supplier'],
            'id_barang' => $post['id_barang'],
            'harga_supplier' => str_replace('.', '',$post['harga_beli']),
        ];    
        //cek data
        $cek = $this->db->where(['id_supplier' => $post['supplier'], 'id_barang' => $post['id_barang'],])->get('supplier_barang');
        if ($cek->num_rows() > 0) {
            $json = [
                        'status' => false,
                        'message' => 'Failed ! Gagal data sudah ada'
                    ];
        } else {
            $save = $this->db->insert('supplier_barang', $data);
            if ($save) {
                $json = [
                    'status' => true,
                    'message' => 'Berhasil Menyimpan data'
                ];
            } else {
                $json = [
                    'status' => false,
                    'message' => 'gagal Menyimpan data'
                ];
            }
        }
        
        ob_start();
        header('Content-Type: application/json');
        echo json_encode($json);
        ob_end_flush();
    }

    public function update_harga_beli()
    {
        $id_barang = $this->input->post('id_barang');
        $harga_supplier = $this->input->post('harga_supplier');
        $supplier = $this->input->post('supplier');

        $data = [
            'harga_supplier' => str_replace('.', '',$harga_supplier),
        ];
        $cek = $this->db->where(['id_barang' => $id_barang, 'id_supplier' => $supplier]);
        if ($cek->get('supplier_barang')->num_rows() > 0) {
            $this->db->where(['id_barang' => $id_barang, 'id_supplier' => $supplier]);
           $up =  $cek->update('supplier_barang', $data);
           if ($up) {
               $json = [
                    'status' => true,
                    'message' => 'Berhasil mengupdate data'
                ];
            } else {
                $json = [
                    'status' => false,
                    'message' => 'gagal mengupdate data'
                ];
            }
        } else {
            $json = [
                'status' => false,
                'message' => 'Error ! tidak menemukan data yg di update'
            ];
        }


        ob_start();
        header('Content-Type: application/json');
        echo json_encode($json);
        ob_end_flush();
    }

     public function update_harga_jual()
    {
        $id_barang = $this->input->post('id_barang');
        $harga_jual = $this->input->post('harga_jual');
        $supplier = $this->input->post('supplier');

        $data = [
            'harga_jual' => str_replace('.', '',$harga_jual)
        ];
        $cek = $this->db->where(['id_barang' => $id_barang, 'id_supplier' => $supplier]);
        if ($cek->get('supplier_barang')->num_rows() > 0) {
            $this->db->where(['id_barang' => $id_barang, 'id_supplier' => $supplier]);
           $up =  $cek->update('supplier_barang', $data);
           if ($up) {
               $json = [
                    'status' => true,
                    'message' => 'Berhasil mengupdate data'
                ];
            } else {
                $json = [
                    'status' => false,
                    'message' => 'gagal mengupdate data'
                ];
            }
        } else {
            $json = [
                'status' => false,
                'message' => 'Gagal ! tidak menemukan data yang di update'
            ];
        }


        ob_start();
        header('Content-Type: application/json');
        echo json_encode($json);
        ob_end_flush();
    }


    private function _validasi()
    {
        $this->form_validation->set_rules('nama_pelanggan', 'Nama Pelanggan', 'required|trim');
        $this->form_validation->set_rules('no_telp', 'Nomor Telepon', 'required|trim|numeric');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');
    }

    public function add()
    {

        $this->_validasi();
        if ($this->form_validation->run() == false) {
            $data['title'] = "Pelanggan";
            $this->template->load('templates/dashboard', 'pelanggan/add', $data);
        } else {

            $input = $this->input->post(null, true);
            $data = [
                'nama' => $input['nama_pelanggan'],
                'no_hp' => $input['no_telp'],
                'alamat' => $input['alamat']
            ];
            $save = $this->admin->insert('pelanggan', $data);
            if ($save) {
                set_pesan('data berhasil disimpan.');
                redirect('pelanggan');
            } else {
                set_pesan('data gagal disimpan', false);
                redirect('pelanggan/add');
            }
        }
    }


    public function edit($getId)
    {
        $id = encode_php_tags($getId);
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Supplier";
            $data['supplier'] = $this->admin->get('pelanggan', ['id_pelanggan' => $id]);
            $this->template->load('templates/dashboard', 'pelanggan/edit', $data);
        } else {
            $input = $this->input->post(null, true);
            $data = [
                'nama' => $input['nama_pelanggan'],
                'no_hp' => $input['no_telp'],
                'alamat' => $input['alamat']
            ];
            $update = $this->admin->update('pelanggan', 'id_pelanggan', $id, $data);

            if ($update) {
                set_pesan('data berhasil diedit.');
                redirect('pelanggan');
            } else {
                set_pesan('data gagal diedit.');
                redirect('supplier/edit/' . $id);
            }
        }
    }

    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('pelanggan', 'id_pelanggan', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('pelanggan');
    }

    public function delete_barang_stok()
    {
        $post = $this->input->post(null,true);
        $supplier = $post['supplier'];
        $kode_barang = $post['kode_barang'];

        $data = $this->db->where(['id_supplier'=>$supplier, 'id_barang' => $kode_barang])->get('supplier_barang');
        if ($data->num_rows() > 0) {
            $delete = $this->db->where(['id_supplier'=>$supplier, 'id_barang' => $kode_barang]);
            $delete->delete('supplier_barang');
            if ($delete) {
                  $json_data = [
                    "message" => 'Berhasil menghapus data',
                    "status" => true,
                ];
            } else {
                $json_data = [
                    "status" => false,
                    "message" => 'Gagal menghapus data',
                ];
            }
        } else {
            $json_data = [
                    "status" => false,
                    "message" => 'Data tidak di temukan',
                ];
        }
         ob_start();
        header('Content-Type: application/json');
        echo json_encode($json_data);
        ob_end_flush();
    }
}
