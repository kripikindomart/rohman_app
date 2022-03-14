<div class="row justify-content-center">
    <div class="col">
        <div class="card shadow-sm border-bottom-primary">
            <div class="card-header bg-white py-3">
                <div class="row">
                    <div class="col">
                        <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                            Form Input Barang Masuk
                        </h4>
                    </div>
                    <div class="col-auto">
                        <a href="<?= base_url('barangmasuk') ?>" class="btn btn-sm btn-secondary btn-icon-split">
                            <span class="icon">
                                <i class="fa fa-arrow-left"></i>
                            </span>
                            <span class="text">
                                Kembali
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= $this->session->flashdata('pesan'); ?>
                <?= form_open('', [], ['id_barang_masuk' => $id_barang_masuk, 'user_id' => $this->session->userdata('login_session')['user']]); ?>
                <h5>Data Petugas</h5>
                <hr>
                <div class="form-row">
                    <div class="form-group col-4">
                        <label>No. Terima</label>
                        <input type="text" name="no_terima" value="<?= $id_barang_masuk; ?>" readonly class="form-control" id="faktur">
                         <?= form_error('id_barang_masuk', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    <div class="form-group col-3">
                        <label>Nama Petugas</label>
                        <input type="hidden" name="id_petugas" value="<?= userdata('id_user') ?>" >
                        <input type="text" name="nama_petugas" value="<?= userdata('username') ?>" readonly class="form-control">
                    </div>
                    <div class="form-group col-3">
                        <label>Tanggal Terima</label>
                        <input value="<?= set_value('tanggal_masuk', date('Y-m-d')); ?>" name="tanggal_masuk" id="tanggal_masuk" type="text" class="form-control date" placeholder="Tanggal Masuk...">
                        <?= form_error('tanggal_masuk', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <h5>Data Supplier</h5>
                        <hr>
                        <div class="form-row">
                            <div class="form-group col-10">
                                <label for="nama_supplier">Nama Supplier</label>
                                <div class="input-group">
                                    <select name="supplier_id" id="supplier_id" class="custom-select">
                                        <option value="" selected disabled>Pilih Supplier</option>
                                        <?php foreach ($supplier as $s) : ?>
                                            <option <?= set_select('supplier_id', $s['id_supplier']) ?> value="<?= $s['id_supplier'] ?>"><?= $s['nama_supplier'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <a class="btn btn-primary" href="<?= base_url('supplier/add'); ?>"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                                <?= form_error('supplier_id', '<small class="text-danger">', '</small>'); ?>
                            </div>
                            <div class="form-group col-2">
                                <label for="">&nbsp;</label>
                                <button disabled type="button" class="btn btn-danger btn-block" id="reset"><i class="fa fa-times"></i></button>
                            </div>
                            <input type="hidden" name="nama_supplier" value="">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5>Data Barang</h5>
                        <hr>
                        <div class="form-row">
                            <div class="form-group col-5">
                                <label for="nama_barang">Nama Barang</label>
                                <div class="input-group">
                                    <select name="barang_id" id="barang_id" class="custom-select">
                                        <option value="" selected disabled>Pilih Barang</option>
                                        <?php foreach ($barang as $b) : ?>
                                            <option <?= $this->uri->segment(3) == $b['id_barang'] ? 'selected' : '';  ?> <?= set_select('barang_id', $b['id_barang']) ?> value="<?= $b['id_barang'] ?>"><?= $b['id_barang'] . ' | ' . $b['nama_barang'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <a class="btn btn-primary" href="<?= base_url('barang/add'); ?>"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                                <?= form_error('barang_id', '<small class="text-danger">', '</small>'); ?>
                            </div>
                            <div class="form-group col-1">
                                <label>Stok</label>
                                <input readonly="readonly" id="stok" type="number" class="form-control">
                            </div>
                            <div class="form-group col-4">
                                <label>Jumlah</label>
                                <div class="input-group">
                                    <input value="<?= set_value('jumlah_masuk'); ?>" name="jumlah_masuk" id="jumlah_masuk" type="number" class="form-control" placeholder="Jumlah Masuk..." min="1">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="satuan">Satuan</span>
                                    </div>
                                </div>
                                <?= form_error('jumlah_masuk', '<small class="text-danger">', '</small>'); ?>
                            </div>
                            <div class="form-group pull-right" style="float: left;">
                                <label for="">&nbsp;</label>
                                <button disabled type="button" class="btn btn-primary btn-block" id="tambah"><i class="fa fa-plus"></i></button>
                            </div>
                            <input type="hidden" name="satuan" value="">
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                   <div class="form-row col-6"></div>
                   <div class="form-row col-3"></div>
                    
                </div>
                <div class="row form-group">
                    
                </div>
                <div class="row form-group">
                    
                </div>
                <div class="row form-group">
                    
                </div>
                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="total_stok">Total Stok</label>
                    <div class="col-md-5">
                        <input readonly="readonly" id="total_stok" type="number" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col offset-md-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row" id="tampilDataTemp"></div>
<script type="text/javascript">
    function dataTemp() {
        let faktur = $('#faktur').val();
        let token = '<?= $this->security->get_csrf_hash(); ?>'
        $.ajax({
            url: '<?= base_url('barangmasuk/dataTemp') ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {faktur: faktur, 'csrf_test_name' : token},
            success : function(res) {
                console.log(res.status)
                if (res) {
                    
                    $('#tampilDataTemp').html(res.data)
                } 
            },
            error : function(xhr, ajaxOption, thrownError) {
                console.log(xhr.status + '\n' | thrownError);
            }
        })
        
        
    }

    $(document).ready(function() {
        dataTemp();

        $('#kdbarang').keydown(function(event) {
            /* Act on the event */
            event.preventDefault();
            let token = '<?= $this->security->get_csrf_hash(); ?>'
            let kodebarang = $('#kdbarang').val();

            $.ajax({
                url: '<?= base_url('barangmasuk/ambilDatabarang') ?>',
                type: 'POST',
                dataType: 'json',
                data: {kodebarang: 'kodebarang', 'csrf_test_name' : token},
                success : function (res) {
                    console.log(res)
                }
            })
           
            
        });
    });
</script>