
<div id="content" data-url="<?= base_url('barangmasuk') ?>">

    <div class="row mb-3">

        <div class="col-md-4">
            <div class="card shadow-sm border-bottom-primary">
                <div class="card-body">
                     <?= form_open('', ['id' => 'form', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']); ?>   
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Supplier</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <select name="supplier" id="supplier" class="custom-select">
                                    <option value="" selected disabled>Pilih Supplier</option>
                                    <?php foreach ($supplier as $b) : ?>
                                        <option <?= $this->uri->segment(3) == $b['id_supplier'] ? 'selected' : '';  ?> <?= set_select('id_supplier', $b['id_supplier']) ?> value="<?= $b['id_supplier'] ?>"><?=  $b['nama_supplier'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <a class="btn btn-primary" href="<?= base_url('barang/add'); ?>"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <?= form_error('barang_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Barang</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <select name="id_barang" id="barang" class="custom-select">
                                    <option value="" selected disabled>Pilih Barang</option>
                                    <?php foreach ($barang as $b) : ?>
                                        <option <?= $this->uri->segment(3) == $b['id_barang'] ? 'selected' : '';  ?> <?= set_select('id_barang', $b['id_barang']) ?> value="<?= $b['id_barang'] ?>"><?= $b['id_barang'] . ' | ' . $b['nama_barang'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <a class="btn btn-primary" href="<?= base_url('barang/add'); ?>"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <?= form_error('barang_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Harga beli</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" name="harga_beli" class="form-control uang" min="0">
                            </div>
                            <?= form_error('barang_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Harga Jual</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" name="harga_jual" class="form-control uang" min="0" readonly>
                            </div>
                            <?= form_error('barang_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                   


                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label"></label>
                        <div class="col-sm-8">
                            <button type="button" class="btn btn-primary btn-sm" id="tambah">Tambah</button>
                            <button type="reset" class="btn btn-secondary btn-sm">Reset</button>
                        </div>
                    </div>
                        <?= form_close() ?>
                </div>
            </div>
            <div class="message mt-2"></div>
        </div>
    

        <div class="col-md-8">
            <div class="card shadow-sm border-bottom-primary">
                <dev class="card-header">
                    <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                        Barang yang di stok
                    </h4>
                    <button class="btn btn-primary btn-sm text-right float-right reload">Reload</button>
                </dev>
                <div class="card-body table-reponsive">
                    <table class="table table-bordered table-striped dataTable" id="keranjang">
                        <thead>
                            <tr>
                                <th width="10%">Kode Barang</th>
                                <th>Nama Barang</th>
                                <th width="20%">Harga Beli (Satuan)</th>
                                <th>Harga Jual</th>
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
    <div class="row mb-3">
        
    </div>
  

 
</div>

<script type="text/javascript">
    $(document).ready(function() {

       var id = $('input[name="nama_barang"]').val();
       var table = $('.dataTable').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('/supplier/getBarangSupplier/')?>",
                "type": "POST",
                "data": {id : function() { return $('#supplier').val()}},
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                { 
                    "targets": [ -1, 0 ], //last column
                    "orderable": false, //set not orderable
                    "searching": false,
                    "paging": false,
                    "sort" : false,
                }
            ],
        });

       table.on('load', function(){
         $( '.uang' ).mask('000.000.000.000', {reverse: true});
       })
        $(".reload").click(function(event) {
            /* Act on the event */
            table.ajax.reload(null,false);
        });
            function update() {
                 table.ajax.reload(null,false);
            }

            function reset() {
                $('#nama_barang').val('')
                $('input[name="kode_barang"]').val('')
                $('input[name="jumlah_masuk"]').val('')
                $('#stok').val('')
                $('#total_stok').val('')
                $('input[name="jumlah_masuk"]').prop('readonly', true)
                $('button#tambah').prop('disabled', true)
            }

              $(document).on('click', '#tambah', function(e) {
                const url_keranjang_barang = '<?= base_url('supplier/barang_to_supplier') ?>'
                var data = $('#form').serializeArray();
                console.log(data)
                $.ajax({
                    url: url_keranjang_barang,
                    type: 'POST',
                    data: data,
                    success: function(res) {

                        if (res.status == true) {
                           update();
                            $('.no_item').addClass('hidden');
                            $('tfoot').show() 
                            $('.message').printMessage({message : res.message, type : 'success'})

                        } else {
                            $('.message').printMessage({message : res.message, type : 'warning'})
                            update();
                        }
                    }
                })
            })

        $(document).on('change', '#supplier', function(event) {
            event.preventDefault();
            /* Act on the event */
            table.ajax.reload();
        });      


           



   
            $(document).on('click', '#delete', function(event) {
                event.preventDefault();
                /* Act on the event */
                var supplier = $(this).attr('data-supplier')
                var kode_barang_edit = $(this).attr('data-id')
                alert('Anda yakin ingin menghapusnya dari barang yg di stok supplier ? ');
                $.ajax({
                    url: '<?= base_url('supplier/delete_barang_stok') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {supplier: supplier, kode_barang : kode_barang_edit},
                    success : function(res) {
                        if (res.status == true) {
                            $('.message').printMessage({message : res.message, type : 'success'})
                            update();

                        } else {
                            $('.message').printMessage({message : res.message, type : 'warning'})

                        }
                    }
                })

            });

            $(document).on('change', '.harga_supplier', function(event) {
                event.preventDefault();
                /* Act on the event */
                var harga_supplier = $(this).val();
                var id_barang = $(this).attr('id-barang')
                var supplier = $(this).attr('id-supplier')
                $.ajax({
                    url: '<?= base_url('supplier/update_harga_beli') ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {harga_supplier: harga_supplier, id_barang:id_barang, supplier:supplier},
                    success : function(res){
                        if (res.status == true) {
                            $('.message').printMessage({message : res.message, type : 'success'})
                        } else {
                            $('.message').printMessage({message : res.message, type : 'warning'})

                        }
                    }
                })
            });

            $(document).on('change', '#barang', function(event) {
                event.preventDefault();
                /* Act on the event */
                var id_barang = $(this).val()

                $.ajax({
                    url: '<?= base_url('supplier/getDataBarang') ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {id_barang:id_barang},
                    success : function(res){
                        if (res.status == true) {
                            
                            $('input[name="harga_jual"]').val(res.data.harga_satuan)
                        } else {
                            $('.message').printMessage({message : res.data, type : 'warning'})

                        }
                    }
                })
            });

            $(document).on('change', '.harga_jual', function(event) {
                event.preventDefault();
                /* Act on the event */
                var harga_jual = $(this).val();
                var id_barang = $(this).attr('id-barang')
                var supplier = $(this).attr('id-supplier')

                $.ajax({
                    url: '<?= base_url('supplier/update_harga_jual') ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {harga_jual: harga_jual, id_barang:id_barang, supplier:supplier},
                    success : function(res){
                        console.log(res)
                        if (res.status == true) {
                            $('.message').printMessage({message : res.message, type : 'success'})
                        } else {
                            $('.message').printMessage({message : res.message, type : 'warning'})
                            
                        }
                    }
                })
               
                

            });




            
    });
</script>