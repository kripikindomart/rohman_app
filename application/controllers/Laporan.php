<?php
defined('BASEPATH') or exit('No direct script access allowed');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Laporan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form_validation->set_rules('transaksi', 'Transaksi', 'required|in_list[barang_masuk,barang_keluar,penjualan]');
        $this->form_validation->set_rules('tanggal', 'Periode Tanggal', 'required');

        if ($this->form_validation->run() == false) {
            $data['title'] = "Laporan Transaksi";
            $this->template->load('templates/dashboard', 'laporan/form', $data);
        } else {
            $input = $this->input->post(null, true);
            $table = $input['transaksi'];
            $tanggal = $input['tanggal'];
            $pecah = explode(' - ', $tanggal);
            $mulai = date('Y-m-d', strtotime($pecah[0]));
            $akhir = date('Y-m-d', strtotime(end($pecah)));

            $query = '';
            if ($table == 'barang_masuk') {
                $query = $this->admin->getBarangMasuk(null, null, ['mulai' => $mulai, 'akhir' => $akhir]);
            } else if($table == 'barang_keluar') {
                $query = $this->admin->getBarangKeluar(null, null, ['mulai' => $mulai, 'akhir' => $akhir]);
            } else {
                 $query = $this->admin->getPenjualan(null, null, ['mulai' => $mulai, 'akhir' => $akhir]);
            }

            // echo "<pre>";
            // print_r($query);
            // die();
           //$this->_cetak($query, $table, $tanggal, $this->input->post('csrf_test_name'));
           $this->excel($query, $table, $tanggal, $this->input->post('csrf_test_name'));

        }
    }

    private function _cetak($data, $table_, $tanggal, $csrf)
    {
        $csrf;

        $this->load->library('CustomPDF');
        $table = $table_ == 'barang_masuk' ? 'Barang Masuk' : 'Barang Keluar';

        $pdf = new FPDF();
        $pdf->AddPage('L', 'A4');
        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(190, 7, 'Laporan ' . $table, 0, 1, 'C');
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(190, 4, 'Tanggal : ' . $tanggal, 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 10);

        if ($table_ == 'barang_masuk') :
            $pdf->Cell(10, 7, 'No.', 1, 0, 'C');
            $pdf->Cell(25, 7, 'Tgl Masuk', 1, 0, 'C');
            $pdf->Cell(35, 7, 'ID Transaksi', 1, 0, 'C');
            $pdf->Cell(55, 7, 'Nama Barang', 1, 0, 'C');
            $pdf->Cell(40, 7, 'Supplier', 1, 0, 'C');
            $pdf->Cell(30, 7, 'Jumlah Masuk', 1, 0, 'C');
            $pdf->Ln();

            $no = 1;
            foreach ($data as $d) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 7, $no++ . '.', 1, 0, 'C');
                $pdf->Cell(25, 7, $d['tanggal_masuk'], 1, 0, 'C');
                $pdf->Cell(35, 7, $d['id_barang_masuk'], 1, 0, 'C');
                $pdf->Cell(55, 7, $d['nama_barang'], 1, 0, 'L');
                $pdf->Cell(40, 7, $d['nama_supplier'], 1, 0, 'L');
                $pdf->Cell(30, 7, $d['jumlah'] . ' ' . $d['nama_satuan'], 1, 0, 'C');
                $pdf->Ln();
            } else :
            $pdf->Cell(10, 7, 'No.', 1, 0, 'C');
            $pdf->Cell(25, 7, 'Tgl Keluar', 1, 0, 'C');
            $pdf->Cell(35, 7, 'ID Transaksi', 1, 0, 'C');
            $pdf->Cell(95, 7, 'Nama Barang', 1, 0, 'C');
            $pdf->Cell(30, 7, 'Jumlah Keluar', 1, 0, 'C');
            $pdf->Cell(30, 7, 'Catatan', 1, 0, 'C');
            $pdf->Ln();

            $no = 1;
            foreach ($data as $d) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 7, $no++ . '.', 1, 0, 'C');
                $pdf->Cell(25, 7, $d['tanggal_keluar'], 1, 0, 'C');
                $pdf->Cell(35, 7, $d['id_barang_keluar'], 1, 0, 'C');
                $pdf->Cell(95, 7, $d['nama_barang'], 1, 0, 'L');
                $pdf->Cell(30, 7, $d['jumlah_keluar'] . ' ' . $d['nama_satuan'], 1, 0, 'C');
                $pdf->Cell(30, 7, $d['catatan'] , 1, 0, 'C');
                $pdf->Ln();
            }
        endif;

        $file_name = $table . ' ' . $tanggal;
        $pdf->Output('I', $file_name);
    }

    public function excel($data, $table_, $tanggal, $csrf)
    {
       
        if ($table_ == 'barang_masuk') {
            $table = 'Barang Masuk';
        } else if ($table_ == 'barang_keluar') {
            $table = 'Barang Keluar';
        } else {
            $table = 'Penjualan';
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $style_col = [
          'font' => ['bold' => true], // Set font nya jadi bold
          'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ],
          'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
          ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
          'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ],
          'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
          ]
        ];
       


        if ($table_ == 'barang_masuk') {
            $sheet->setCellValue('A1', "Laporan Barang Masuk"); 
            $sheet->mergeCells('A1:H1'); // Set Merge Cell pada kolom A1 sampai E1
            $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1

            $sheet->setCellValue('A3', 'No');
            $sheet->setCellValue('B3', 'No Transaksi');
            $sheet->setCellValue('C3', 'Supplier');
            $sheet->setCellValue('D3', 'Item');
            $sheet->setCellValue('E3', 'jumlah Masuk');
            $sheet->setCellValue('F3', 'Harga Supplier');
            $sheet->setCellValue('G3', 'Total Pembelian');
            $sheet->setCellValue('H3', 'Harga Jual');
            $sheet->setCellValue('I3', 'Tanggal Masuk');

             // Apply style header yang telah kita buat tadi ke masing-masing kolom header
            $sheet->getStyle('A3')->applyFromArray($style_col);
            $sheet->getStyle('B3')->applyFromArray($style_col);
            $sheet->getStyle('C3')->applyFromArray($style_col);
            $sheet->getStyle('D3')->applyFromArray($style_col);
            $sheet->getStyle('E3')->applyFromArray($style_col);
            $sheet->getStyle('F3')->applyFromArray($style_col);
            $sheet->getStyle('G3')->applyFromArray($style_col);
            $sheet->getStyle('H3')->applyFromArray($style_col);
            $sheet->getStyle('I3')->applyFromArray($style_col);

             $no = 1; // Untuk penomoran tabel, di awal set dengan 1
            $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
            foreach($data as $row){ // Lakukan looping pada variabel siswa
              $sheet->setCellValue('A'.$numrow, $no);
              $sheet->setCellValue('B'.$numrow, $row['id_barang_masuk']);
              $sheet->setCellValue('C'.$numrow, $row['nama_supplier']);
              $sheet->setCellValue('D'.$numrow, $row['id_barang'].' - '.$row['nama_barang']);
              $sheet->setCellValue('E'.$numrow, $row['jumlah']);
              $sheet->setCellValue('F'.$numrow, $row['harga_supplier']);
              $sheet->setCellValue('G'.$numrow, ($row['jumlah'] * $row['harga_supplier']));
              $sheet->setCellValue('H'.$numrow, $row['harga_jual']);
              $sheet->setCellValue('I'.$numrow, $row['tanggal_masuk']);
              
              // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
              $sheet->getStyle('A'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('B'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('C'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('D'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('E'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('F'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('G'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('H'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('I'.$numrow)->applyFromArray($style_row);
              
              $no++; // Tambah 1 setiap kali looping
              $numrow++; // Tambah 1 setiap kali looping
            }

             // Set width kolom
            $sheet->getColumnDimension('A')->setWidth(5); // Set width kolom A
            $sheet->getColumnDimension('B')->setWidth(15); // Set width kolom B
            $sheet->getColumnDimension('C')->setWidth(25); // Set width kolom C
            $sheet->getColumnDimension('D')->setWidth(20); // Set width kolom D
            $sheet->getColumnDimension('E')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('F')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('G')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('H')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('I')->setWidth(30); // Set width kolom E

             // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            // Set orientasi kertas jadi LANDSCAPE
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

             $file_name = $table . ' ' . $tanggal;
                    // Set judul file excel nya
            $sheet->setTitle($table);
            // Proses file excel
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$file_name.'-'.date('Ymdhs').'.xlsx"'); // Set nama file excel nya
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        } else if ($table_ == 'barang_keluar') {
            $sheet->setCellValue('A1', "Laporan Barang Keluar"); 
            $sheet->mergeCells('A1:H1'); // Set Merge Cell pada kolom A1 sampai E1
            $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1


            $sheet->setCellValue('A2', "*Jumlah Kerugian di hitung berdasarkan harga jual"); 
            $sheet->setCellValue('A3', 'No');
            $sheet->setCellValue('B3', 'No Transaksi');
            $sheet->setCellValue('C3', 'Item');
            $sheet->setCellValue('D3', 'Jumlah Keluar');
            $sheet->setCellValue('E3', 'Catatan');
            $sheet->setCellValue('F3', 'Laba Rugi');
            $sheet->setCellValue('G3', 'Tanggal');

             // Apply style header yang telah kita buat tadi ke masing-masing kolom header
            $sheet->getStyle('A3')->applyFromArray($style_col);
            $sheet->getStyle('B3')->applyFromArray($style_col);
            $sheet->getStyle('C3')->applyFromArray($style_col);
            $sheet->getStyle('D3')->applyFromArray($style_col);
            $sheet->getStyle('E3')->applyFromArray($style_col);
            $sheet->getStyle('F3')->applyFromArray($style_col);
            $sheet->getStyle('G3')->applyFromArray($style_col);

            $no = 1; // Untuk penomoran tabel, di awal set dengan 1
            $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
            foreach($data as $row){ // Lakukan looping pada variabel siswa
              $sheet->setCellValue('A'.$numrow, $no);
              $sheet->setCellValue('B'.$numrow, $row['id_barang_keluar']);
              $sheet->setCellValue('C'.$numrow, $row['id_barang'].' - '.$row['nama_barang']);
              $sheet->setCellValue('D'.$numrow, $row['jumlah_keluar']);
              $sheet->setCellValue('E'.$numrow, $row['catatan']);
              $sheet->setCellValue('F'.$numrow, $row['harga_jual']* $row['jumlah_keluar']);
              $sheet->setCellValue('G'.$numrow, $row['tanggal_keluar']);

              
              // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
              $sheet->getStyle('A'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('B'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('C'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('D'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('E'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('F'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('G'.$numrow)->applyFromArray($style_row);
              
              $no++; // Tambah 1 setiap kali looping
              $numrow++; // Tambah 1 setiap kali looping
            }
             // Set width kolom
            $sheet->getColumnDimension('A')->setWidth(5); // Set width kolom A
            $sheet->getColumnDimension('B')->setWidth(15); // Set width kolom B
            $sheet->getColumnDimension('C')->setWidth(25); // Set width kolom C
            $sheet->getColumnDimension('D')->setWidth(20); // Set width kolom D
            $sheet->getColumnDimension('E')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('F')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('G')->setWidth(30); // Set width kolom E
            // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            // Set orientasi kertas jadi LANDSCAPE
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
             $file_name = $table . '-' . $tanggal;
                    // Set judul file excel nya
            $sheet->setTitle('laporan');
            // Proses file excel
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$table.'-'.date('Ymdhs').'.xlsx"'); // Set nama file excel nya
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');


        } else {
            $sheet->setCellValue('A1', "Laporan Penjualan"); 
            $sheet->mergeCells('A1:H1'); // Set Merge Cell pada kolom A1 sampai E1
            $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
            $sheet->setCellValue('A3', 'No');
            $sheet->setCellValue('B3', 'No Transaksi');
            $sheet->setCellValue('C3', 'tanggal');
            $sheet->setCellValue('D3', 'admin');
            $sheet->setCellValue('E3', 'Costumer');
            $sheet->setCellValue('F3', 'Item');
            $sheet->setCellValue('G3', 'Qty');
            $sheet->setCellValue('H3', 'Satuan');
            $sheet->setCellValue('I3', 'Harga');
            $sheet->setCellValue('J3', 'total');
            $sheet->setCellValue('K3', 'status');
            $sheet->setCellValue('L3', 'kurang bayar');
            $sheet->setCellValue('M3', 'note');

            $sheet->getStyle('A3')->applyFromArray($style_col);
            $sheet->getStyle('B3')->applyFromArray($style_col);
            $sheet->getStyle('C3')->applyFromArray($style_col);
            $sheet->getStyle('D3')->applyFromArray($style_col);
            $sheet->getStyle('E3')->applyFromArray($style_col);
            $sheet->getStyle('F3')->applyFromArray($style_col);
            $sheet->getStyle('G3')->applyFromArray($style_col);
            $sheet->getStyle('H3')->applyFromArray($style_col);
            $sheet->getStyle('I3')->applyFromArray($style_col);
            $sheet->getStyle('J3')->applyFromArray($style_col);
            $sheet->getStyle('K3')->applyFromArray($style_col);
            $sheet->getStyle('L3')->applyFromArray($style_col);
            $sheet->getStyle('M3')->applyFromArray($style_col);

            $no = 1; // Untuk penomoran tabel, di awal set dengan 1
            $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
            foreach($data as $row){ // Lakukan looping pada variabel siswa
                $status = 'PAID';
                if ($row['kurang_bayar'] > 0) {
                    $status = 'Kurang Bayar';
                } else {
                    $status = 'PAID';
                }
              $sheet->setCellValue('A'.$numrow, $no);
              $sheet->setCellValue('B'.$numrow, $row['kode_transaksi']);
              $sheet->setCellValue('C'.$numrow, $row['tanggal_masuk']);
              $sheet->setCellValue('D'.$numrow, $row['nama']);
              $sheet->setCellValue('E'.$numrow, $row['nama_pelanggan'].' - '.$row['alamat'].' - '.$row['no_hp']);
              $sheet->setCellValue('F'.$numrow, $row['id_barang'].'-'.$row['nama_barang']);
              $sheet->setCellValue('G'.$numrow, ($row['qty']));
              $sheet->setCellValue('H'.$numrow, $row['nama_satuan']);
              $sheet->setCellValue('I'.$numrow, $row['harga_jual']);
              $sheet->setCellValue('J'.$numrow, $row['total']);
              $sheet->setCellValue('K'.$numrow, $status);
              $sheet->setCellValue('L'.$numrow, $row['kurang_bayar']);
              $sheet->setCellValue('M'.$numrow, $row['note']);
              
              // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
              $sheet->getStyle('A'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('B'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('C'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('D'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('E'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('F'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('G'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('H'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('I'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('J'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('K'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('L'.$numrow)->applyFromArray($style_row);
              $sheet->getStyle('M'.$numrow)->applyFromArray($style_row);
              
              $no++; // Tambah 1 setiap kali looping
              $numrow++; // Tambah 1 setiap kali looping
            }

             // Set width kolom
            $sheet->getColumnDimension('A')->setWidth(5); // Set width kolom A
            $sheet->getColumnDimension('B')->setWidth(15); // Set width kolom B
            $sheet->getColumnDimension('C')->setWidth(25); // Set width kolom C
            $sheet->getColumnDimension('D')->setWidth(20); // Set width kolom D
            $sheet->getColumnDimension('E')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('F')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('G')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('H')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('I')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('J')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('K')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('L')->setWidth(30); // Set width kolom E
            $sheet->getColumnDimension('M')->setWidth(30); // Set width kolom E

             // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            // Set orientasi kertas jadi LANDSCAPE
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

             $file_name = $table . ' ' . $tanggal;
                    // Set judul file excel nya
            $sheet->setTitle($table);
            // Proses file excel
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$table.'-'.date('Ymdhs').'.xlsx"'); // Set nama file excel nya
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

        }
        die();
        

    }
}
