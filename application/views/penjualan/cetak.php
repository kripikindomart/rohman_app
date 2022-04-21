<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Cetak</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style type="text/css">
		body {
			font-family: sans-serif;
		}
	</style>
</head>
<body>
	<div style="width:750px; margin: auto;">
		<br>
		<center>
			
			<table width="100%">
				<tr>
					<td width="50%">
						<div class="row" >
							<div class="col-md-4">
								<img src="<?= $data_pt->logo == null ? base_url('assets/img/logo.png') : base_url('assets/img/').$data_pt->logo ?>" class="img img-responsive" style="width: 100%;">
							</div>
							<div class="col-md-8">
								<strong><?= $data_pt->nama ?></strong><br>
								<?= $data_pt->alamat ?> <br>
								Telp. <?= $data_pt->no_telepon ?>
							</div>
							
						</div>
						
					</td>
					<td align="right" width="50%"><span style="font-size: 26px;">SURAT JALAN</span></td>
				</tr>
			</table>
			<hr>
			

			<table width="100%">
				<tr>
					<td colspan="3"><strong>Kepada Yth</strong></td>
				</tr>
				<tr>
					<td width="10%"><strong>Nama</strong></td>
					<td width="2%"> : </td>
					<td width="18%">  <?= $data_penjualan->nama_pelanggan ?></td>
					<td width="20%"></td>
					<td width="20%"><strong>No invoice</strong></td>
					<td width="5"> : </td>
					<td width="25%"><strong> <?= $data_penjualan->kode_transaksi ?> </strong> </td>
				</tr>
				<tr>
					<td><strong>No.Hp</strong></td>
					<td width="2%"> : </td>
					<td width="25%">  <?= $data_penjualan->no_hp ?></td>
					<td width="20%"></td>
					<td width="20%"><strong>Tanggal</strong></td>
					<td width="5"> : </td>
					<td width="25%"> <?= tgl_indo($data_penjualan->tanggal_masuk) ?>  </td>
				</tr>
				<tr>
					<td><strong>Alamat</strong></td>
					<td width="2%"> : </td>
					<td width="18%">  <?= $data_penjualan->alamat ?></td>
					<td width="20%"></td>
					<td width="20%"><strong>Expedisi</strong></td>
					<td width="5"> : </td>
					<td width="25%"> <?= $data_penjualan->nama ?>  </td>
				</tr>
			</table>
			<hr>
			<div class="mt-10"></div>
			<table width="100%"  class="table table-bordered">
				<tr>
					<td>Nama Barang</td>
					<td>Qty</td>
					<td>Satuan</td>
					<td>Keterangan</td>
					
				</tr>
				<?php foreach ($data_item as $row): ?>
					<tr>
						<td>
							<?= $row['id_barang'] ?> - <?= $row['nama_barang'] ?>
						</td>
						<td>
							<?= $row['qty'] ?>
						</td>
						<td>
							<?= $row['nama_satuan'] ?>
						</td>
						
						<td height="100px">
						</td>
						
					</tr>

				<?php endforeach ?>
				<tr>
						<td colspan="3">
							<strong>Catatan : </strong><br>
							<?= $data_penjualan->note ?>
							<?php if ($data_penjualan->note == null): ?>
								<br>
								<br>
								<br>
							<?php endif ?>
						</td>
						
					</tr>
			</table>
			<div class="tex-left" align="left" style="margin-bottom: 10px;">
				<i>BARANG SUDAH DITERIMA DALAM KEADAAN BAIK DAN CUKUP oleh :</i><br>
			<i>(tanda tangan dan cap (stempel) perusahaan)</i>
			</div>
			<div class="mb-10"></div>
			<table width="100%" class="mt-10">
				<tr>
					<td width="40%"><strong>Bukti Keaslian Pembelian</strong></td>
					<td width="30%"><strong>Penerima / Pembeli</strong></td>
					<td width="30%"><strong>Bagian Pengiriman</strong></td>
				</tr>
				<tr>
					<td>
						<img src="<?= $qrcode ?>" class="img img-responsive" style="width: 37%;">
					</td>
					<td>
						<div class="margin" style="margin-bottom: 50px;"></div>

					</td>
					
					<td>
						a.n <?= $data_penjualan->pengirim ?>
						<div class="margin" style="margin-bottom: 50px;"></div>
						
					</td>
				</tr>
				<tr >
					<td colspan=2>
						
						<small>Qr Berikut merupakan bukti keaslian pembelian, barang yang dibeli jumlah barang yang di kirimkan, dan total pembayaran maupun kekurang bayar yang tercatat di sistem dan tidak bisa di manipulasi oleh pihak tertentu, selain pemilik</small>
					</td>
				</tr>
			</table>

			
			<hr>
			
		</center>
	</div>
	<script>
		window.print()
	</script>
</body>
</html>