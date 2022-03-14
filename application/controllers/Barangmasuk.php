<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barangmasuk extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->model('M_toko', '_db');
        $this->load->model('Supplier_model', 'supplier');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Barang Masuk";
        $data['barangmasuk'] = $this->admin->getBM();

        $this->template->load('templates/dashboard', 'barang_masuk/index', $data);
    }

    public function detail($id)
    {
        $data['title'] = "Barang Masuk";
        $data['barangmasuk'] = $this->admin->getBarangMasuk(null,null,null,$id);
        $this->template->load('templates/dashboard', 'barang_masuk/data', $data);
    }

    private function _validasi()
    {
        $this->form_validation->set_rules('tanggal_masuk', 'Tanggal Masuk', 'required|trim');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('barang_id', 'Barang', 'required');
        $this->form_validation->set_rules('jumlah_masuk', 'Jumlah Masuk', 'required|trim|numeric|greater_than[0]');
    }

    public function getSupplierdata()
    {
       
        $id = $this->input->post('id');
        $id_barang = $this->input->post('id_barang');
        $this->db->where(['id_supplier' => $id, 'id_barang' => $id_barang]);
        $supplier = $this->db->get('supplier_barang');
        if ($supplier->num_rows() > 0) {
            //Ada data
            $json = ['status' => false, 'data' => $supplier->row()];
        } else {
            //Tidak ada data
            $json = ['status' => true, 'data' => $supplier->row()];
        }
        ob_start();
        header('Content-Type: application/json');
        echo json_encode($json);
        ob_end_flush();
    }

    public function add()
    {
        $this->_validasi();
        if ($this->form_validation->run() == false) {
            $data['title'] = "Barang Masuk";
            $data['supplier'] = $this->admin->get('supplier');
            $data['barang'] = $this->admin->get('barang');

            // Mendapatkan dan men-generate kode transaksi barang masuk
            $kode = 'T-BM-' . date('ymd');
            $kode_terakhir = $this->admin->getMax('barang_masuk', 'id_barang_masuk', $kode);
            $kode_tambah = substr($kode_terakhir, -5, 5);
            $kode_tambah++;
            $number = str_pad($kode_tambah, 5, '0', STR_PAD_LEFT);
            $data['id_barang_masuk'] = $kode . $number;
            if ($this->data_keranjang($data['id_barang_masuk'])) {
                $data['keranjang'] = $this->data_keranjang($data['id_barang_masuk']);  
            }
            $this->template->load('templates/dashboard', 'barang_masuk/sale_form', $data);
        } else {
            $input = $this->input->post(null, true);
            $insert = $this->admin->insert('barang_masuk', $input);

            if ($insert) {
                set_pesan('data berhasil disimpan.');
                redirect('barangmasuk');
            } else {
                set_pesan('Opps ada kesalahan!');
                redirect('barangmasuk/add');
            }
        }
    }

    public function save()
    {
        $id_transaksi = $this->input->post('no_terima');


        $barang_masuk = $this->db->select('temporary_transaction.id_transaksi, temporary_transaction.kode_barang, temporary_transaction.harga_satuan as harga_satuan_transaksi, temporary_transaction.qty, temporary_transaction.total, barang.nama_barang, barang.stok,barang.harga_satuan as harga_satuan_barang, satuan.nama_satuan, jenis.nama_jenis, barang.satuan_id, barang.jenis_id');
        $this->db->from('temporary_transaction');
        $this->db->join('barang', 'barang.id_barang = temporary_transaction.kode_barang', 'left');
        $this->db->join('satuan', 'satuan.id_satuan = barang.satuan_id', 'left');
        $this->db->join('jenis', 'jenis.id_jenis = barang.jenis_id', 'left');
        $data = $this->db->where(['temporary_transaction.id_transaksi'=>$id_transaksi])->get();
        if ($data->num_rows() > 0) {
             $data_barang_masuk = [
                'id_barang_masuk' => $this->input->post('no_terima'),
                'supplier_id'   => (empty($this->input->post('nama_supplier')) ? 1 : $this->input->post('nama_supplier') ),
                'user_id' => $this->input->post('id_petugas'),
                'tanggal_masuk' => $this->input->post('tanggal_masuk')
            ];
            $save_barang_masuk = $this->db->insert('barang_masuk', $data_barang_masuk);
            if ($save_barang_masuk) {
               foreach ($data->result() as $row) {
                    $data_terima = [
                        'id_barang_masuk'=> $row->id_transaksi,
                        'nama_barang' => $row->nama_barang,
                        'jumlah' => $row->qty,
                        'harga_satuan' => $row->harga_satuan_transaksi,
                        'satuan_id' => $row->satuan_id
                    ];
                     
                    $save_data_terima = $this->db->insert('detail_terima', $data_terima);
                    if ($save_data_terima) {
                        $kode_barang = $row->kode_barang;
                        $this->db->where('id_barang', $kode_barang);
                        $old = $this->db->get('barang')->result();
                        $total_stok = 0;
                        $harga_satuan = 0;
                        foreach ($old as $r) {
                            $total_stok = $r->stok + $row->qty;
                            $harga_satuan = ($r->harga_satuan == null || $r->harga_satuan == 0 ? $row->harga_satuan_transaksi : $r->harga_satuan) ;
                            $data = [
                                'stok' => $total_stok,
                                'harga_satuan' => $harga_satuan
                            ];
                        }
                        $this->db->where('id_barang', $kode_barang);
                        $update = $this->db->update('barang', $data);
                        if ($update) {
                            //delete Temporary
                            $this->db->where('id_transaksi', $row->id_transaksi);
                            $delete = $this->db->delete('temporary_transaction');
                            $json = [
                                'status' => true,
                                'delete' => $delete,
                                'message' => 'Berhasil mengupdate stok'
                            ];
                        } else {
                            $json = [
                                'status' => false,
                                'message' => 'gagal mengupdate stok'
                            ];
                        }
                    }
                } 
            } else {
                //Gagal barang masuk
            }
            
        }

        echo json_encode($json);
        
    }

    public function data_keranjang($id)
    {
        //$id = $this->input->post('id_transaksi');
        $this->db->select('*');
        $this->db->from('temporary_transaction a');
        $this->db->join('barang', 'barang.id_barang = a.kode_barang', 'left');
        $this->db->join('satuan', 'satuan.id_satuan = barang.satuan_id', 'left');
        $transaksi = $this->db->where(['id_transaksi' => $id])->get();
        if ($transaksi->num_rows() > 0) {
           return $keranjang = $transaksi->result();
        } else {
            return $transaksi->num_rows();
        }
    }

    public function keranjang_barang()
    {
        $post = $this->input->post(null, true);
        //Data Create
        $data = [
            'kode_barang' => $post['kode_barang'],
            'harga_satuan' => $post['harga_satuan'],
            'id_transaksi' => $post['id_transaksi'],
            'qty' => $post['jumlah_masuk'],
            'total' => $post['total_harga'],
        ];
        
        $transaksi = $this->db->where(['id_transaksi' => $post['id_transaksi'], 'kode_barang' => $post['kode_barang']])->get('temporary_transaction');

        if ($transaksi->num_rows > 0 ) {
            //Jika data barang old ada maka di update
        } else {
           $save = $this->db->insert('temporary_transaction',$data);
                $this->db->select('*');
                $this->db->from('temporary_transaction a');
                $this->db->join('barang', 'barang.id_barang = a.kode_barang', 'left');
                $this->db->join('satuan', 'satuan.id_satuan = barang.satuan_id', 'left');
                $data['keranjang'] = $this->db->where('id_transaksi', $post['id_transaksi'])->get()->result();
           $response = [
                'status' => true,
                'message' => 'Barang berhasil dimasukkan ke keranjang',
                'data' => $this->getKeranjang($post['id_transaksi'])
            ];
        }


        // if ($transaksi->num_rows() > 0) {
        //     $transaksi_old = $this->db->where(['id_transaksi' => $post['id_transaksi'], 'kode_barang' => $post['kode_barang']])->get('temporary_transaction');
                
        //         if ($transaksi_old->num_rows > 0) {
        //            //Update
        //             $data = [
        //                 'harga_satuan' => $post['harga_satuan'],
        //                 'qty' => $post['jumlah_masuk'],
        //                 'total' => $post['total_harga'],
        //             ];
        //             $update = $this->db->where(['id_transaksi'=>$post['id_transaksi'], 'kode_barang' => $post['kode_barang']])->update('temporary_transaction',$data);
        //             if ($update) {
        //                 $response = [
        //                     'status' => true,
        //                     'message' => 'Barang berhasil diupdate ',
        //                 ];
        //             } else {
        //                 $response = [
        //                     'status' => true,
        //                     'message' => 'Gagal !Terjadi kesalahan saat mengupdate data barang'
        //                 ];
        //             }

        //         } else {
        //              $transaksi_old = $this->db->where(['id_transaksi' => $post['id_transaksi'], 'kode_barang' => $post['kode_barang']])->get('temporary_transaction');
                
        //                 if ($transaksi_old->num_rows > 0) {
        //                     //Update
        //                     $data = [
        //                         'harga_satuan' => $post['harga_satuan'],
        //                         'qty' => $post['jumlah_masuk'],
        //                         'total' => $post['total_harga'],
        //                     ];
        //                     $update = $this->db->where(['id_transaksi'=>$post['id_transaksi'], 'kode_barang' => $post['kode_barang']])->update('temporary_transaction',$data);
        //                     if ($update) {
        //                         $response = [
        //                             'status' => true,
        //                             'message' => 'Barang berhasil diupdate ',
        //                         ];
        //                     } else {
        //                         $response = [
        //                             'status' => true,
        //                             'message' => 'Gagal !Terjadi kesalahan saat mengupdate data barang'
        //                         ];
        //                     }
        //                 } else {
        //                     //Tambah Barang
        //                     //Create
        //                     $data = [
        //                         'kode_barang' => $post['kode_barang'],
        //                         'harga_satuan' => $post['harga_satuan'],
        //                         'id_transaksi' => $post['id_transaksi'],
        //                         'qty' => $post['jumlah_masuk'],
        //                         'total' => $post['total_harga'],
        //                     ];
        //                     $save = $this->db->insert('temporary_transaction',$data);
        //                     if ($save) {
        //                         $this->db->select('*');
        //                         $this->db->from('temporary_transaction a');
        //                         $this->db->join('barang', 'barang.id_barang = a.kode_barang', 'left');
        //                         $this->db->join('satuan', 'satuan.id_satuan = barang.satuan_id', 'left');
        //                         $data['keranjang'] = $this->db->where('id_transaksi', $post['id_transaksi'])->get()->result();
        //                         $response = [
        //                             'status' => true,
        //                             'message' => 'Barang berhasil dimasukkan ke keranjang',
        //                             'data' => $this->load->view('barang_masuk/keranjang', $data)
        //                         ];
        //                     } else {
        //                         $response = [
        //                             'status' => true,
        //                             'message' => 'Gagal !Terjadi kesalahan saat menambah data barang'
        //                         ];
        //                     }
        //                 }
                    
        //         }
        // } else {
        //     //Create
        //     $data = [
        //         'kode_barang' => $post['kode_barang'],
        //         'harga_satuan' => $post['harga_satuan'],
        //         'id_transaksi' => $post['id_transaksi'],
        //         'qty' => $post['jumlah_masuk'],
        //         'total' => $post['total_harga'],
        //     ];

        //   $save = $this->db->insert('temporary_transaction',$data);
        //     if ($save) {
        //         $this->db->select('*');
        //         $this->db->from('temporary_transaction a');
        //         $this->db->join('barang', 'barang.id_barang = a.kode_barang', 'left');
        //         $this->db->join('satuan', 'satuan.id_satuan = barang.satuan_id', 'left');
        //         $data['keranjang'] = $this->db->where('id_transaksi', $post['id_transaksi'])->get()->result();
        //         $response = [
        //             'status' => true,
        //             'message' => 'Barang berhasil dimasukkan ke keranjang',
        //             'data' => $this->load->view('barang_masuk/keranjang', $data)
        //         ];
        //     } else {
        //         $response = [
        //             'status' => true,
        //             'message' => 'Gagal !Terjadi kesalahan saat menambah data barang'
        //         ];
        //     }
        // }
        
        

        //Update

        $this->load->view('barang_masuk/keranjang', $data);
    }

    public function getKeranjang($data_id)
    {
        $this->_db->where = ['temporary_transaction.id_transaksi' => $data_id];
        $data_menu = $this->_db->get_datatables();
                $data = array();

            $no = $_POST['start'];

            foreach ($data_menu as $r) {

                $no++;
                $row = array();
                $row[] = $r->kode_barang;
                $row[] = $r->nama_barang;
                $row[] = $r->harga_satuan_transaksi;
                $row[] = $r->qty;
                //$row[] = $r->sesi_kuliah;
                $row[] = $r->total;
                //add html for action
                $row[] = '
                <div class="text-center"><button data-toggle="modal" data-target="#modal" type="button" class="btn btn-sm btn-warning edit" title="Edit" id="tombol-edit" data-id = "'.$r->kode_barang.'" data-transaksi = "'.$r->id_transaksi.'"><i class="fa fa-pencil"></i>Edit </button>
                <button type="button" class="btn btn-sm btn-danger delete" title="Delete" id="delete" data-id = "'.$r->kode_barang.'" data-transaksi = "'.$r->id_transaksi.'"><i class="fa fa-trash"></i>Delete</button></div>';
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

    public function getTotal()
    {
        $id_transaksi = $this->input->post('id_transaksi');
        $this->db->where(['id_transaksi' => $id_transaksi]);
        $this->db->select('SUM(total) AS total_bayar');
        $this->db->from('temporary_transaction');
        $data = $this->db->get()->row();
        $json = ['status' => true, 'data'=> $data];
        echo json_encode($json);
    }

    public function getEdit()
    {
        $post = $this->input->post(null,true);

        $id_transaksi = $post['transaksi'];
        $kode_barang = $post['kode_barang'];
        $this->db->select('temporary_transaction.id_transaksi, temporary_transaction.kode_barang, temporary_transaction.harga_satuan as harga_satuan_transaksi, temporary_transaction.qty, temporary_transaction.total, barang.nama_barang, barang.stok,barang.harga_satuan as harga_satuan_barang, satuan.nama_satuan, jenis.nama_jenis');
        $this->db->from('temporary_transaction');
        $this->db->join('barang', 'barang.id_barang = temporary_transaction.kode_barang', 'left');
        $this->db->join('satuan', 'satuan.id_satuan = barang.satuan_id', 'left');
        $this->db->join('jenis', 'jenis.id_jenis = barang.jenis_id', 'left');
        $data = $this->db->where(['temporary_transaction.id_transaksi'=>$id_transaksi, 'temporary_transaction.kode_barang' => $kode_barang])->get();

        if ($data->num_rows() > 0) {
            $json_data = [
                "status" => true,
                "data" => $data->row(),
            ];
        } else {
            $json_data = [
                "status" => false,
                "message" => 'Gagal meload data',
            ];
        }

        echo json_encode($json_data);
    }

    public function delete_keranjang()
    {
        $post = $this->input->post(null,true);
        $id_transaksi = $post['transaksi'];
        $kode_barang = $post['kode_barang'];

        $data = $this->db->where(['temporary_transaction.id_transaksi'=>$id_transaksi, 'temporary_transaction.kode_barang' => $kode_barang])->get('temporary_transaction');
        if ($data->num_rows() > 0) {
            $nama_barang = $this->db->where(['id_barang' => $kode_barang])->get('barang')->row();
            $delete = $this->db->where(['temporary_transaction.id_transaksi'=>$id_transaksi, 'temporary_transaction.kode_barang' => $kode_barang]);
            $delete->delete('temporary_transaction');
            if ($delete) {
                  $json_data = [
                    "data" => $nama_barang,
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
        echo json_encode($json_data);
    }

    public function keranjang_edit()
    {
        $id_transaksi = $this->input->post("id_transaksi_edit");
        $kode_barang = $this->input->post("kode_barang_edit");
        $harga_satuan = $this->input->post("harga_satuan_edit");
        $qty = $this->input->post("jumlah_masuk_edit");
        $sub_total = $this->input->post("sub_total");

        $data = [
            'harga_satuan' => $harga_satuan,
            'qty'   => $qty,
            'total' => $sub_total
        ];
        //Update data temporary
        $this->db->where(['id_transaksi' => $id_transaksi, 'kode_barang'=>$kode_barang]);
        $update = $this->db->update('temporary_transaction', $data);

        if ($update) {
            $json = ['status' => true];
        } else {
            $json = ['status' => false];
        }

        echo json_encode($json);
    }

    public function keranjang()
    {
        $this->db->select('*');
        $this->db->from('temporary_transaction a');
        $this->db->join('barang', 'barang.id_barang = a.kode_barang', 'left');
        $this->db->join('satuan', 'satuan.id_satuan = barang.satuan_id', 'left');
        $keranjang = $this->db->where('id_transaksi', $post['id_transaksi'])->get();
        if ($keranjang->num_rows > 0) {
          $data['keranjang'] =   $keranjang->result();
            $this->load->view('barang_masuk/keranjang', $data);

        }
    }

    public function process()
    {
        $jumlah_barang_diterima = count($this->input->post('nama_barang_hidden'));
        $data_detail_terima = [];
        for ($i = 0; $i < $jumlah_barang_diterima; $i++) {
            array_push($data_detail_terima, ['id_barang_masuk' => $this->input->post('no_terima')]);
            $data_detail_terima[$i]['nama_barang'] = $this->input->post('nama_barang_hidden')[$i];
            $data_detail_terima[$i]['jumlah_masuk'] = $this->input->post('jumlah_masuk_hidden')[$i];
            $data_detail_terima[$i]['harga_satuan'] = $this->input->post('satuan_hidden')[$i];
        }

        echo json_encode($data_detail_terima);
    }

    public function dataTemp()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $token = $this->security->get_csrf_token_name();
            $faktur = $this->input->post('faktur');
            $data = ['datatemp' => $this->admin->getBarangMasuk()];
            $json = ['status' => true, 'data' => $this->load->view('barang_masuk/modal', $data), 'token' => $token];

            echo json_encode($json);
        }
    }

    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('barang_masuk', 'id_barang_masuk', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('barangmasuk');
    }

    public function ambilDatabarang()
    {
        $token = $this->security->get_csrf_token_name();
        $id = $this->input->post('kdbarang');

        $data_barang = $this->admin->get('barang', ['id_barang' => $id]);

        $data = [
            'status'    => 'success',
            'nama_barang' => $data_barang['nama_barang'],
            'token' => $token
        ];

        echo json_encode($data);
    }

    public function proses_tambah()
    {

        $jumlah_barang_diterima = count($this->input->post('nama_barang_hidden'));
        $token = $this->security->get_csrf_token_name();
        $data_terima = [
            'id_barang_masuk' => $this->input->post('no_terima'),
            'tanggal_masuk' => $this->input->post('tanggal_masuk'),
            'supplier_id' => $this->input->post('nama_supplier'),
            'user_id' => $this->input->post('id_petugas'),
        ];

        $data_detail_terima = [];

        for ($i = 0; $i < $jumlah_barang_diterima; $i++) {
            array_push($data_detail_terima, ['id_barang_masuk' => $this->input->post('no_terima')]);
            $data_detail_terima[$i]['nama_barang'] = $this->input->post('kode_barang_hidden')[$i];
            $data_detail_terima[$i]['jumlah'] = $this->input->post('jumlah_masuk_hidden')[$i];
            $data_detail_terima[$i]['harga_satuan'] = $this->input->post('harga_satuan')[$i];
            $data_detail_terima[$i]['satuan_id'] = $this->input->post('satuan_id_hidden')[$i];
        }
        // echo "<pre>";
        // print_r($data_terima);
        // die();
        if ($this->admin->insert('barang_masuk', $data_terima) && $this->admin->insert('detail_terima', $data_detail_terima, true)) {

            for ($i = 0; $i < $jumlah_barang_diterima; $i++) {
                $this->admin->plus_stok($data_detail_terima[$i]['jumlah'], $data_detail_terima[$i]['nama_barang']) or die('gagal min stok');
            }
            $this->session->set_flashdata('success', 'Invoice <strong>Penerimaan</strong> Berhasil Dibuat!');
            redirect('barangmasuk/add');
        }
    }

    public function get_all_barang()
    {
        $data = $this->admin->lihat_nama_barang($_POST['nama_barang'], 'barang');
        echo json_encode($data);
    }
}
