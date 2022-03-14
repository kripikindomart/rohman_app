<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow-sm border-bottom-primary">
            <div class="card-header bg-white py-3">
                <div class="row">
                    <div class="col">
                        <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                            Form Edit Harga Barang
                        </h4>
                    </div>
                    <div class="col-auto">
                        <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-secondary btn-icon-split">
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
                <div class="message"></div>
                <?= $this->session->flashdata('pesan'); ?>
                <?= form_open('', ['id' => 'form'], ['stok' => 0]); ?>
                <div class="row form-group">
                    <label class="col-md-3 text-md-right" for="id_barang">ID Barang</label>
                    <div class="col-md-9">
                        <select name="barang" id="barang" class="custom-select">
                            <option value="" selected disabled>Pilih Barang</option>
                            <?php foreach ($barang as $s) : ?>
                                <option <?= set_select('id_barang', $s['id_barang']) ?> value="<?= $s['id_barang'] ?>"><?= $s['nama_barang'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="supplier_add">
                <div class="row form-group supplier-1">
                    <label class="col-md-3 text-md-right" for="satuan_id">Supplier</label>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                <select name="supplier[]" id="supplier" class="custom-select supplier" disabled>
                                    <option value="" selected disabled>Pilih Supplier</option>
                                    <?php foreach ($supplier as $s) : ?>
                                        <option <?= set_select('id_supplier', $s['id_supplier']) ?> value="<?= $s['id_supplier'] ?>"><?= $s['nama_supplier'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                
                            </div>
                            <?= form_error('supplier', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input value="<?= set_value('harga_beli'); ?>" name="harga_beli[]" id="harga_beli" type="number" class="form-control" placeholder="Harga Beli" disabled>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input value="<?= set_value('harga_jual'); ?>" name="harga_jual" id="harga_jual" type="number" class="form-control" placeholder="Harga Jual" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="add_supplier" disabled><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                            
                        </div>
                    </div>
                </div>
                </div>
       
                <div class="row form-group">
                    <div class="col-md-9 offset-md-3">
                        <button type="button" class="btn btn-primary" id="simpan">Simpan</button>
                        <button type="button" class="btn btn-secondary" onclick="reset_btn()">Reset</bu>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function reset_btn(){
            reset();
        $('.form-add-append').remove()
    }

    function reset() {
                 $('#nama_supplier').val('')
                $('#nama_supplier').prop('disabled', false)
                $(this).prop('disabled', true)
                $('input[name="nama_supplier"]').val('')
                $('#satuan').text('Satuan')
            }
    $(document).ready(function() {
         /*print message*/
         

        var i = 1;
        $('#add_supplier').click(function(event) {
            /* Act on the event */
            i++;
            $('.supplier_add').append('<div class="row form-group form-add-append" id="form'+i+'">'+
                '<label class="col-md-3 text-md-right" for="satuan_id">Supplier</label>'+
                    '<div class="col-md-9">'+
                        '<div class="row">'+
                           ' <div class="col-md-4">'+
                                '<div class="input-group">'+
                                '<select name="supplier[]" id="supplier" class="custom-select supllier-'+i+'">'+
                                    '<option value="" selected disabled>Pilih Supplier</option>'+
                                    <?php foreach ($supplier as $s) : ?>
                                        '<option <?= set_select('id_supplier', $s['id_supplier']) ?> value="<?= $s['id_supplier'] ?>"><?= $s['nama_supplier'] ?></option>'+
                                    <?php endforeach; ?>
                                '</select>'+
                                
                            '</div>'+
                            <?= form_error('supplier', '<small class="text-danger">', '</small>'); ?>
                        '</div>'+
                        '<div class="col-md-4">'+
                            '<div class="input-group">'+
                                '<input value="<?= set_value('harga_beli'); ?>" name="harga_beli[]" id="harga_beli" type="text" class="form-control harga_beli-'+i+'" placeholder="Harga Beli">'+
                              '<div class="input-group-append">'+
                                    '<button type="button" class="btn btn-danger btn_remove" id="'+i+'"><i class="fa fa-trash"></i></a>'+
                                '</div>'+  
                            '</div>'+
                        '</div>'+
                        
                            
                        '</div>'+
                    '</div></div>');

            $('.harga_beli-'+i).attr('id-supplier', $('supllier-'+i).val())
        
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
             $('#form'+button_id+'').remove();
         });

        $(document).on('click', '#simpan', function(event) {
            event.preventDefault();
            var data = $('#form').serializeArray();
            $.ajax({
                url: '<?= base_url('harga_barang/simpan'); ?>',
                type: 'post',
                dataType: 'json',
                data: data,
                success : function(res){
                    if (res.status == true) {
                        $('.message').printMessage({message : res.message, type : 'success'});
                        reset_btn();
                    } else {
                        $('.message').printMessage({message : res.message, type : 'warning'});
                    }
                }
            })
        });

        $(document).on('change', '.supplier', function(event) {
            event.preventDefault();
            /* Act on the event */
            var id_barang = $('#barang').val();
            var id = $(this).val();
            $('row ').attr('id-supplier', id)
            if (id_barang != null) {
                $.ajax({
                    url: '<?= base_url('harga_barang/getHargaOld') ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {id: id, id_barang : id_barang},
                    success : function(res){
                         console.log()
                    }
                })
            }
            
        }); 

        $(document).on('change', '#barang', function(event) {
            event.preventDefault();
            /* Act on the event */
            $('#harga_beli').prop('disabled', false)
            $('#harga_jual').prop('disabled', false)
            $('#supplier').prop('disabled', false)
            $('#add_supplier').prop('disabled', false)
        });                    


        
    });
</script>