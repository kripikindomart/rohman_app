<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
			<div class="card-header"><strong>Isi Form Dibawah Ini!</strong></div>
			<div class="card-body">
				<form action="<?= base_url('instansi/proses_ubah') ?>" id="form-tambah" method="POST">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<div class="form-group">
						<label for="nama_aplikasi"><strong>Nama Aplikasi : </strong></label>
						<input type="text" name="nama_aplikasi" id="nama_aplikasi" value="<?= $toko['nama_aplikasi'] ?>" placeholder="Masukan Nama Aplikasi" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label for="nama_toko"><strong>Nama Instansi : </strong></label>
						<input type="text" name="nama_toko" id="nama_toko" value="<?= $toko['nama'] ?>" placeholder="Masukan Nama Toko" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label for="nama_toko"><strong>Nama Pemilik : </strong></label>
						<input type="text" name="nama_pemilik" id="nama_pemilik" value="<?= $toko['nama_pemilik'] ?>" placeholder="Masukan Nama Pemilik" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label for="nama_toko"><strong>No Telepon : </strong></label>
						<input type="number" name="no_telepon" id="no_telepon" value="<?= $toko['no_telepon'] ?>" placeholder="Masukan No Telepon" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label for="alamat"><strong>Alamat</strong></label>
						<textarea readonly name="alamat" id="alamat" class="form-control" placeholder="Masukan Alamat" style="resize: none;"><?= $toko['alamat'] ?></textarea>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>
						<button type="button" class="btn btn-success" id="ubah"><i class="fa fa-pen"></i>&nbsp;&nbsp;Ubah</button>
						<button type="reset" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp;&nbsp;Batal</button>
					</div>
				</form>
			</div>				
		</div>
    </div>
</div>