<?= $this->session->flashdata('pesan'); ?>
<div class="card shadow-sm border-bottom-primary">
    <div class="card-header bg-white py-3">
        <div class="row">
            <div class="col">
                <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                    Riwayat Data Penjualan
                </h4>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('penjualan') ?>" class="btn btn-sm btn-secondary btn-icon-split">
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
    <div class="table-responsive">
        <table class="table table-striped w-100 dt-responsive nowrap" id="dataTable">
            <thead>
                <tr>
                    <th>No. </th>
                    <th>No Transaksi</th>
                    <th>Tanggal Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Item - qty</th>
                    <th>Sub - Total</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($barangmasuk) :
                    foreach ($barangmasuk as $bm) :
                ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $bm['kode_transaksi']; ?></td>
                            <td><?= $bm['tanggal_masuk']; ?></td>
                            <td><?= $bm['nama_pelanggan']; ?></td>
                            <td><?= $bm['nama_barang'].' - '.$bm['jumlah']; ?></td>
                            <td><?= rupiah($bm['sub_total']); ?> <br>
                                <small class="text-danger"><i>
                                    <?= rupiah($bm['harga_jual']) .' x '. $bm['jumlah'] ?></i>
                                </small>
                            </td>
                            <td><?= $bm['nama']; ?></td>
                            <!-- <td>

                                 <a onclick="return confirm('Yakin ingin hapus?')" href="<?= base_url('penjualan/delete/') . $bm['kode_transaksi'] ?>" class="btn btn-danger btn-circle btn-sm"><i class="fa fa-trash"></i></a>
                            </td> -->
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            Data Kosong
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function printDiv() 
    {

      var divToPrint=document.getElementById('DivIdToPrint');

      var newWin=window.open('','Print-Window');

      newWin.document.open();

      newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

      newWin.document.close();

      setTimeout(function(){newWin.close();},10);

    }
</script>