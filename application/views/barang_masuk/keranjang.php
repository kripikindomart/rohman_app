<?php foreach ($keranjang as $data_keranjang): ?>
	

<tr class="row-keranjang">
	<td class="kode_barang">
		<?= $data_keranjang->kode_barang ?>
		<input type="hidden" name="kode_barang_hidden[]" value="<?= $this->input->post('kode_barang') ?>">

	</td>
	<td class="nama_barang">
		<?= $data_keranjang->nama_barang ?>
		<input type="hidden" name="nama_barang_hidden[]" value="<?= $data_keranjang->nama_barang ?>">
	</td>
	<td class="harga_satuan">
		<?= $data_keranjang->harga_satuan ?>
		<input type="hidden" name="harga_satuan[]" value="<?= $data_keranjang->harga_satuan ?>">
	</td>
	<td class="jumlah_masuk">
		<span class="qty_text"><?= $data_keranjang->qty ?></span>
		<input type="hidden" name="jumlah_masuk_hidden[]" value="<?= $data_keranjang->qty ?>">
		<input type="hidden" name="satuan_id_hidden[]" value="<?= $data_keranjang->satuan_id ?>">
	</td>
	<td class="total_harga">
		<span class="total_text"><?= $data_keranjang->total ?></span>

		<input type="hidden" name="total_harga[]" value="<?= $data_keranjang->total ?>">
	</td>
	<td class="aksi">
		<button type="button" data-toggle="modal" data-target="#modal" class="btn btn-info btn-sm" id="tombol-edit" data-kode-barang="<?= $data_keranjang->kode_barang ?>" data-id="<?= $data_keranjang->kode_barang ?>" data-harga-satuan="<?= $data_keranjang->harga_satuan ?>" data-jumlah-masuk="<?= $data_keranjang->qty ?>"><i class="fa fa-cog"></i></button>
		<button type="button" class="btn btn-danger btn-sm" id="tombol-hapus" data-nama-barang="<?= $data_keranjang->kode_barang ?>"><i class="fa fa-trash"></i></button>

	</td>
</tr>

<?php endforeach ?>