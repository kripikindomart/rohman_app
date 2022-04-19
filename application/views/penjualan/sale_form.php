 <?php
    $message = $this->session->flashdata('error');
    if (isset($message)) {
        echo '<div class="alert alert-danger">' . $message . '</div>';
         $this->session->unset_userdata('error');
    }

?>

<div id="content" data-url="<?= base_url('penjualan') ?>">
    <?= form_open(base_url('penjualan/proses_tambah'),  [
        'id' => 'form_tambah',
        'enctype' => 'multipart/form-data'
    ]); ?>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-bottom-primary">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">ID Transaksi</label>
                        <div class="col-sm-8">
                            <input value="<?= $kode_transaksi; ?>" type="text" readonly="readonly" class="form-control" name="no_terima">
                            <?= form_error('kode_transaksi', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama Petugas</label>
                        <div class="col-sm-8">
                            <input type="hidden" name="id_petugas" value="<?= userdata('id_user') ?>">
                            <input type="text" name="nama_petugas" value="<?= userdata('username') ?>" readonly class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tanggal </label>
                        <div class="col-sm-8">
                            <input value="<?= set_value('tanggal_masuk', date('Y-m-d')); ?>" name="tanggal_masuk" id="tanggal_masuk" type="text" class="form-control date" placeholder="Tanggal Masuk...">
                            <?= form_error('tanggal_masuk', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Pelanggan</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <select name="nama_supplier" id="nama_supplier" class="custom-select">
                                    <option value="" selected disabled>Pilih Pelanggan</option>
                                    <?php foreach ($pelanggan as $s) : ?>
                                        <option <?= set_select('id_pelanggan', $s['id_pelanggan']) ?> value="<?= $s['id_pelanggan'] ?>"><?= $s['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <a class="btn btn-primary" href="<?= base_url('pelanggan/add'); ?>"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <?= form_error('supplier_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class=" col-sm-2">
                            <div class="form-group">
                                <button disabled type="button" class="btn btn-danger btn-block" id="reset"><i class="fa fa-times"></i></button>
                            </div>

                        </div>
                        <input type="hidden" name="nama_supplier" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-bottom-primary">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama Pengirim Barang</label>
                        <div class="col-sm-8">
                            <input type="text" name="pengirim" value=""  class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Barang</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <select name="nama_barang" id="barang_id" class="custom-select">
                                    <option value="" selected disabled>Pilih Barang</option>
                                    <?php foreach ($barang as $b) : ?>
                                        <option <?= $this->uri->segment(3) == $b['id_barang'] ? 'selected' : '';  ?> <?= set_select('barang_id', $b['id_barang']) ?> value="<?= $b['id_barang'] ?>"><?= $b['id_barang'] . ' | ' . $b['nama_barang'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="kode_barang" value="" readonly class="form-control">
                                <div class="input-group-append">
                                    <a class="btn btn-primary" href="<?= base_url('barang/add'); ?>"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <?= form_error('barang_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Jumlah Beli </label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="hidden" name="kode_barang_edit">
                                <input value="<?= set_value('jumlah_masuk'); ?>" name="jumlah_masuk" id="jumlah_beli" type="number" class="form-control" placeholder="Jumlah Masuk...">

                                <div class="input-group-append">
                                    <span class="input-group-text" id="satuan">Satuan</span>
                                </div>
                                <input type="hidden" name="satuan" value="">
                                <input type="hidden" name="harga_satuan" value="" id="harga_satuan">
                            </div>
                            <?= form_error('jumlah_masuk', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Stok</label>
                        <div class="col-sm-3">
                            <input readonly="readonly" id="stok" type="number" class="form-control">
                        </div>
                        <label class="col-sm-2 col-form-label">Total </label>
                        <div class="col-sm-3">
                            <input readonly="readonly" id="total_stok" type="number" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label"></label>
                        <div class="col-sm-8">
                            <input type="hidden" name="max_hidden" value="">
                            <button type="button" class="btn btn-primary btn-sm" id="tambah">Masukkan Ke keranjang</button>
                            <button type="reset" class="btn btn-secondary btn-sm">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card shadow-sm border-bottom-primary">
                <dev class="card-header">
                    <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                        Barang Masuk
                    </h4>
                </dev>
                <div class="card-body table-reponsive">
                    <table class="table table-bordered table-striped dataTable" id="keranjang">
                        <thead>
                            <tr>
                                <th width="10%">Kode Barang</th>
                                <th>Nama Barang</th>
                                <th width="20%">Harga Satuan</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart_table">
                            
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-bottom-primary">
                <div class="card-body table-reponsive">
                    <table width="100%">
                        <!--  <tr>
                        <td style="vertical-align: top; width: 30%;">
                            <label>Sub Total</label>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" name="sub_total" id="sub_total" value="" class="form-control sub_total" readonly>

                            </div>
                        </td>
                    </tr> -->
                        <tr>
                            <!-- <td style="vertical-align: top;">
                                <label>PPN %</label>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="number" name="ppn" id="ppn" value="" class="form-control">
                                </div>
                            </td> -->
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">
                                <label>Total</label>
                            </td>
                            <td>
                                <div class="form-group">
                                    Rp. <span id="total">0</span>
                                    <input type="hidden" name="total_harga">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-bottom-primary">
                <div class="card-body table-reponsive">
                    <table width="100%">
                        <tr>
                            <td style="vertical-align: top; width: 30%;">
                                <label>Cash</label>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="number" name="cash" id="cash" value="0" min="0" class="form-control" >
                                </div>

                            </td>
                                    <div class="error"></div>

                        </tr>
                        <tr>
                            <td style="vertical-align: top;">
                                <label>Kembalian</label>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="number" name="change" id="change" class="form-control" readonly>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-bottom-primary">
                <div class="card-body table-reponsive">
                    <table width="100%">
                        <tr>
                            <td style="vertical-align: top; ">
                                <label>Note</label>
                            </td>
                            <td>
                                <div>
                                    <textarea name="note" id="note" rows="3" class="form-control"></textarea>
                                </div>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div>
                <a  class="btn btn-flat btn-warning" href="<?= base_url('penjualan') ?>">
                    <i class="fa fa-refresh"></i> Cancel
                </a><br><br>
                <button id="save_keranjang" type="button" class="btn btn-flat btn-success">
                    <i class="fa fa-paper-plane-o"></i> Simpan
                </button><br><br>

            </div>
        </div>
    </div>
    <?= form_close() ?>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= form_open('', [
                        'class' => 'form-horizontal',
                        'id' => 'form_edit',
                        'enctype' => 'multipart/form-data'
                    ]); ?>
                    <input type="hidden" name="id_transaksi_edit">
                    <input type="hidden" name="kode_barang_edit">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Stok</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input value="<?= set_value('stok'); ?>" name="stok" id="stok_edit" type="number" min="0" class="form-control prc" placeholder="Stok" readonly>
                                <input type="hidden" value="" name="stok_real" id="stok_real" type="number" min="0" class="form-control prc" placeholder="Stok" readonly>
                            </div>
                            <?= form_error('stok', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Harga Satuan</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input value="<?= set_value('harga_satuan'); ?>" name="harga_satuan_edit" id="harga_satuan_edit" type="number" min="0" class="form-control prc" placeholder="Harga Satuan" >
                                <input type="hidden" name="kode_barang" value="">
                                
                            </div>
                            <?= form_error('harga_satuan', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Qty</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input value="<?= set_value('jumlah_masuk'); ?>" name="jumlah_masuk_edit" type="number" class="form-control prc" placeholder="Jumlah Masuk...">

                                <div class="input-group-append">
                                    <span class="input-group-text" id="nama_satuan">Satuan</span>
                                </div>
                                <input type="hidden" name="qty" value="">
                            </div>
                            <?= form_error('jumlah_masuk', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Sub Total</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input value="<?= set_value('sub_total'); ?>" name="sub_total" type="number" class="form-control" placeholder="Sub Total..." readonly>
                            </div>
                            <?= form_error('jumlah_masuk', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <?= form_close(); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit">Edit</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {



        $('tfoot').hide();
            let hal = '<?= $this->uri->segment(1); ?>';
            let satuan = $('#satuan');
            let stok = $('#stok');
            let total = $('#total_stok');
            let jumlah = hal == 'penjualan' ? $('#jumlah_masuk') : $('#jumlah_keluar');
        hitung_total()
       var id = $('input[name="no_terima"]').val();
       var table = $('.dataTable').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('/penjualan/getKeranjang/')?>"+id,
                "type": "POST",
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                { 
                    "targets": [ -1, 0, 2 ], //last column
                    "orderable": false, //set not orderable
                    "searching": false,
                    "paging": false,
                    "sort" : false,
                }
            ],
        });
            function update() {
                 table.ajax.reload(null,false);
            }

            $('#barang_id').on('change', function() {

                if ($(this).val() == '') reset()
                else {
                    const url_get_all_barang = '<?= base_url('penjualan/get_all_barang'); ?>'
                    $.ajax({
                        url: url_get_all_barang,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            nama_barang: $(this).val()
                        },
                        success: function(data) {
                            console.log
                            $('input[name="kode_barang"]').val(data.id_barang)
                            $('#harga_satuan').val(data.harga_satuan)
                            $('input[name="jumlah_masuk"]').val(1)
                            $('input[name="satuan"]').val(data.satuan_id)
                            $('#stok').val(data.stok)
                            $('#satuan').text(data.nama_satuan)
                            $('input[name="jumlah_masuk"]').prop('readonly', false)
                            $('button#tambah').prop('disabled', false)

                            $('input[name="sub_total"]').val($('input[name="jumlah_masuk"]').val() * $('input[name="harga_satuan"]').val())

                            $('input[name="jumlah_masuk"]').on('keydown keyup change blur', function() {
                                $('input[name="sub_total"]').val($('input[name="jumlah_masuk"]').val() * $('input[name="harga_satuan"]').val())
                            })
                        }
                    })
                }
            })

            $(document).on('keyup', '#jumlah_beli', function() {
               
                if (parseInt(this.value) >= parseInt(stok.val())) {
                    alert('Jumlah beli tidak boleh melebihi stok yang tersedia')
                    reset();
                } else {
                 let totalStok = parseInt(stok.val()) - parseInt(this.value);
                 total.val(Number(totalStok));
                }
                

            });

            function reset() {
                $('#barang_id').val('')
                $('input[name="kode_barang"]').val('')
                $('input[name="jumlah_masuk"]').val('')
                $('#stok').val('')
                $('#total_stok').val('')
                $('input[name="jumlah_masuk"]').prop('readonly', true)
                $('button#tambah').prop('disabled', true)
            }

            function hitung_total() {
                var id_transaksi = '<?= $kode_transaksi; ?>';
                $.ajax({
                    url: '<?= base_url('penjualan/getTotal') ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {id_transaksi: id_transaksi},
                    success : function(res){
                        console.log(res.data.total_bayar)
                        if (res.data.total_bayar == null) {
                            $('#total').html('<strong>0</strong>')
                             $('input[name="total_harga"]').val(res.data.total_bayar)  
                        } else {
                             $('#total').html('<strong>'+(res.data.total_bayar)+'</strong>') 
                             $('input[name="total_harga"]').val(res.data.total_bayar)  

                        }
                    }
                })
                
                
                // let total = 0;
                // $('.total_harga').each(function() {
                //     total += parseInt($(this).text())
                // })

                // return total;
            }
              $(document).on('click', '#tambah', function(e) {
                const url_keranjang_barang = '<?= base_url('penjualan/keranjang_barang') ?>'
                let totalStok = $('#total_stok').val();
                const data_keranjang = {
                    nama_barang: $('select[name="nama_barang"]').val(),
                    kode_barang: $('input[name="kode_barang"]').val(),
                    jumlah_masuk: $('input[name="jumlah_masuk"]').val(),
                    harga_satuan: $('input[name="harga_satuan"]').val(),
                    id_transaksi: $('input[name="no_terima"]').val(),
                    total_stok: $('#total_stok').val(),
                    total_harga: parseInt($('input[name="jumlah_masuk"]').val()) * parseInt($('input[name="harga_satuan"]').val()),
                    satuan_id: $('input[name="satuan"]').val(),
                }
                if (totalStok == 1 || totalStok == 0) {
                    alert('stok barang habis, anda harus mengisi ulang stok trlebih dahulu')
                    return false;
                }
               
                $.ajax({
                    url: url_keranjang_barang,
                    type: 'POST',
                    data: data_keranjang,
                    success: function(res) {
                      if ($('select[name="nama_barang"]').val() == data_keranjang.nama_barang) $('option[value="' + data_keranjang.nama_barang + '"]').hide()
                        update();
                        hitung_total()
                        reset()
                        $('.no_item').addClass('hidden');

                        $('tfoot').show()

                        
                    }
                })
            })


           

            $(document).on('keyup change', 'input[name="jumlah_masuk_edit"]', function(event) {
                event.preventDefault();
                let stok_barang_db = $('#stok_real').val();
                let stok_barang_tampil = $('#stok_edit');
                let qty = $(this).val();


                let qtyOld = $('input[name="qty"]').val();

                if (parseInt(qty) >= parseInt(stok_barang_db)) {
                    alert('Stok Barang anda saat ini '+stok_barang_db+' dan tidak dapat lebih dari jumlah stok saat ini')
                    $(this).val(qtyOld)
                } else if(parseInt(qty) <= 1){
                    alert('Qty tidak boleh kurang dari satu')
                    $(this).val(qtyOld)
                    stok_barang_tampil.val(stok_barang_db)
                } else {
                    let totalStok = parseInt(stok_barang_db) - parseInt(qty);
                    stok_barang_tampil.val(totalStok);
                }


                

                // var stok = $('#stok_real').val()
                // var stokE = $('#stok_edit').val()
                // var qty = $(this).val();
                // let qtyOld = $('input[name="qty"]').val();
                // if (qty == null || qty == 0) {
                //     alert('Qty tidak boleh 0 atau kosong')
                //     $('input[name="jumlah_masuk_edit"]').val(qtyOld);
                //     return false;
                // }
                // if (parseInt(qty) >= parseInt(stokE)) {
                //     alert('Stok Barang anda saat ini '+stok+' dan tidak dapat lebih dari jumlah stok saat ini')
                //      $('input[name="jumlah_masuk_edit"]').val(qtyOld);
                // } else {
                //      let totalStok = parseInt(stok) - parseInt(qty);
                //      $('#stok_edit').val(Number(totalStok));
                // }

                

                /* Act on the event */
            });  

            


            $(document).on('click', '#tombol-edit', function(e) {
                $('#myModal').modal('show')
                var transaksi = $(this).attr('data-transaksi')
                var kode_barang_edit = $(this).attr('data-id')
                $.ajax({
                    url: '<?= base_url('penjualan/getEdit') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {transaksi: transaksi, kode_barang : kode_barang_edit},
                    success : function(res) {
                        if (res.status == true) {
                            $('input[name="id_transaksi_edit"]').val(res.data.id_transaksi)
                            $('input[name="stok"]').val(res.data.stok)
                            $('#stok_real').val(res.data.stok)
                            $('input[name="kode_barang_edit"]').val(res.data.kode_barang)
                            $('input[name="harga_satuan_edit"]').val(res.data.harga_satuan_transaksi)
                            $('input[name="jumlah_masuk_edit"]').val(res.data.qty)
                            $('input[name="qty"]').val(res.data.qty)
                            $('input[name="sub_total"]').val(res.data.total)
                            $('#nama_satuan').text(res.data.nama_satuan)
                        }
                    }
                })
                
                
            })

            $('#edit').on('click', function() {
                const url_keranjang_barang = '<?= base_url('penjualan/keranjang_edit') ?>'
                var data = $('#form_edit').serializeArray();
                console.log(data)
                $.ajax({
                    url: url_keranjang_barang,
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    success: function(res){
                        if (res.status == true) {
                            update();
                            hitung_total()

                             $('#modal').modal('hide')
                        } else {
                            alert('Error');
                           update();
                             $('#modal').modal('hide')     
                        }
                    }
                })
            })

            $('.form-group').on('input', function() {
                var totalSum = 0;
                var a = $('input[name="harga_satuan_edit"]').val();
                var b = $('input[name="jumlah_masuk_edit"]').val();
                totalSum = a * b;
                $('input[name="sub_total"]').val(totalSum)
            })

            $(document).on('click', '#delete', function(event) {
                event.preventDefault();
                /* Act on the event */
                var transaksi = $(this).attr('data-transaksi')
                var kode_barang_edit = $(this).attr('data-id')
                alert('Anda yakin ingin menghapusnya dari keranjang ? ');
                $.ajax({
                    url: '<?= base_url('penjualan/delete_keranjang') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {transaksi: transaksi, kode_barang : kode_barang_edit},
                    success : function(res) {
                        if (res.status == true) {
                            update();
                            hitung_total()

                            $('option[value="' + res.data.nama_barang + '"]').show()
                        } else {
                            hitung_total()
                            update();
                        }
                    }
                })

            });

            $(document).on('input ', '#cash', function(event) {
                event.preventDefault();
                /* Act on the event */
                var kembalian = 0;
                var total =  $('input[name="total_harga"]').val();
                var cash = $(this).val();
                // if (cash <= total ) {
                //     alert('Kurang Bayaa')
                // } else {    
                    kembalian = parseInt(cash) - parseInt(total);
                //}
                console.log(cash);
                $('#change').val(kembalian);
            });        

            $(document).on('click', '#save_keranjang', function(event) {
                event.preventDefault();
                var data = $('#form_tambah').serializeArray();

                console.log(data)
                $.ajax({
                    url: '<?= base_url('penjualan/save') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    
                })


                .done(function(res) {
                   if (res.status == true) {
                        alert(res.message)
                        location.reload();
                    } else {
                        alert(res.message)
                    }
                })
                .fail(function(res) {
                    alert(res.message)
                    //location.reload();
                })
                .always(function() {
                    console.log("complete");
                });
                
                
                
            }); 

            
    });
</script>