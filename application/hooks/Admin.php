<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		$this->load->model('M_admin');
		// $this->load->library('pagination');
		if (!$this->session->userdata('admin')) {

			redirect('login/admin');
		}
	}

	public function index()
	{
		$this->load->view('admin_layout/header');
		$this->load->view('admin_layout/sidebar');
		$this->load->view('admin/dashboard');
		$this->load->view('admin_layout/footer');
	}


	public function artikel()
	{
		$data = [
			'title' => 'Artikel',
			'artikel' => $this->M_admin->get_artikel()->result()

		];
		$this->load->view('admin_layout/header', $data);
		$this->load->view('admin_layout/sidebar', $data);
		$this->load->view('admin/artikel', $data);
		$this->load->view('admin_layout/footer');
	}
	public function form_tambah_artikel()
	{

		$data = [
			'title' => 'Form tambah Artikel'
		];

		$this->load->view('admin/form_tambah_artikel');
		$this->load->view('admin_layout/footer');
	}

	public function tambah_artikel()
	{
		$judul_artikel = $this->input->post('judul_artikel');
		$foto = $_FILES['foto']['name'];
		$artikel = $this->input->post('artikel');

		if ($foto = '') {
		} else {
			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['detect_mime']     = TRUE;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('foto')) {
				$error = array('error' => $this->upload->display_errors());
				$this->session->set_flashdata('form', $error);
			} else {
				$foto = $this->upload->data('file_name');
			}
		}
		$data = [
			'judul_artikel' => $judul_artikel,
			'foto' => $foto,
			'isi_artikel' => $artikel,
			'created' => date('Y-m-d H:i:s')
		];
		$this->M_admin->tambah_artikel($data, 'artikel');
		$this->session->set_flashdata('pesan', 'ditambahkan');
		redirect('admin/artikel', 'refresh');
	}

	public function hapus_artikel($id_artikel)
	{
		$data = $this->M_admin->get_artikel_byId($id_artikel);
		$foto = './assets/uploads/' . $data->foto;
		$id_artikel  = array('id_artikel' => $id_artikel);
		$this->M_admin->hapus_artikel($id_artikel);
		unlink($foto);
		$this->session->set_flashdata('pesan', 'dihapus');
		redirect('admin/artikel', 'refresh');
	}

	public function detail_artikel($id_artikel)
	{
		$id_artikel  = ['id_artikel' => $id_artikel];
		$data = [
			'title' => 'Detail Artikel',
			'artikel' => $this->M_admin->detail_artikel($id_artikel, 'artikel')->result()
		];
		$this->load->view('admin/detail_artikel', $data);
	}

	public function edit_artikel($id_artikel)
	{
		$id_artikel = ['id_artikel' => $id_artikel];
		$data['artikel'] = $this->M_admin->detail_artikel($id_artikel, 'artikel')->result();
		$this->load->view('admin/form_edit_artikel', $data);
		$this->load->view('admin_layout/footer');
	}

	public function ubah_artikel()
	{
		$judul_artikel = $this->input->post('judul_artikel');
		$foto = $_FILES['foto']['name'];
		$artikel = $this->input->post('artikel');
		$foto_lama = $this->input->post('foto_lama');
		$id_artikel = $this->input->post('id_artikel');

		if ($foto) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('foto')) {
				$new_foto = $this->upload->data('file_name');
				$data = $this->M_admin->get_artikel_byId($id_artikel);
				$foto = './assets/uploads/' . $data->foto;
				unlink($foto);
			}
		} else {

			$new_foto = $foto_lama;
		};

		$data = [
			'judul_artikel' => $judul_artikel,
			'foto' => $new_foto,
			'isi_artikel' => $artikel

		];

		$id_artikel = ['id_artikel' => $id_artikel];
		$this->M_admin->update_artikel($data, $id_artikel, 'artikel');
		$this->session->set_flashdata('pesan', 'diubah');
		redirect('admin/artikel', 'refresh');
	}

	public function profil_sekolah()
	{

		$data = [
			'visi' => $this->M_admin->edit_visi()->result(),
			'misi' => $this->M_admin->edit_misi()->result(),
			'ekskul' => $this->M_admin->get_ekskul()->result(),
			'prestasi' => $this->M_admin->get_prestasi()->result(),
			'about' => $this->M_admin->edit_about()->result(),
			'slider' => $this->M_admin->get_slider()->result(),
			'profile' => $this->M_admin->get_profile()->result()
		];

		$this->load->view('admin_layout/header', $data);
		$this->load->view('admin_layout/sidebar', $data);
		$this->load->view('admin/profil_sekolah', $data);
		$this->load->view('admin_layout/footer');
	}

	public function ubah_visi()
	{
		$id_visi = $this->input->post('id_visi');
		$isi_visi = $this->input->post('isi_visi');

		$data = [
			'isi_visi' => $isi_visi
		];

		$id_visi = ['id_visi' => $id_visi];
		$this->M_admin->update_visi($data, $id_visi, 'visi');
		$this->session->set_flashdata('pesan', 'disimpan');
		redirect('admin/profil_sekolah' . '#misi', 'refresh');
	}

	public function ubah_misi()
	{
		$id_misi = $this->input->post('id_misi');
		$isi_misi = $this->input->post('isi_misi');

		$data = [
			'isi_misi' => $isi_misi
		];

		$id_misi = ['id_misi' => $id_misi];
		$this->M_admin->update_misi($data, $id_misi, 'misi');
		$this->session->set_flashdata('pesan', 'disimpan');
		redirect('admin/profil_sekolah' . '#misi', 'refresh');
	}

	public function tambah_ekskul()
	{
		$nama_ekskul = $this->input->post('nama_ekskul');
		$gambar = $_FILES['gambar']['name'];
		$deskripsi = $this->input->post('deskripsi');


		if ($gambar = '') {
		} else {
			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['detect_mime']     = TRUE;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('gambar')) {

				echo 'gagal upload';
			} else {
				$gambar = $this->upload->data('file_name');
			}
		}

		$data = [
			'nama_ekskul' => $nama_ekskul,
			'deskripsi' => $deskripsi,
			'gambar' => $gambar,

		];

		$this->M_admin->tambah_ekskul($data);
		$this->session->set_flashdata('pesan', 'ditambah');
		redirect('admin/profil_sekolah' . '#ekskul', 'refresh');
	}

	public function hapus_ekskul($id_ekskul)
	{
		$data = $this->M_admin->get_ekskul_byId($id_ekskul);
		$gambar = './assets/uploads/' . $data->gambar;
		$id_ekskul  = array('id_ekskul' => $id_ekskul);
		$this->M_admin->hapus_ekskul($id_ekskul);
		unlink($gambar);
		$this->session->set_flashdata('pesan', 'dihapus');
		redirect('admin/profil_sekolah' . '#ekskul', 'refresh');
	}

	public function ubah_ekskul()
	{
		$nama_ekskul = $this->input->post('nama_ekskul');
		$gambar = $_FILES['gambar']['name'];
		$deskripsi = $this->input->post('deskripsi');
		$foto_lama = $this->input->post('foto_lama');
		$id_ekskul = $this->input->post('id_ekskul');

		if ($gambar) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('gambar')) {
				$new_gambar = $this->upload->data('file_name');
				$data = $this->M_admin->get_ekskul_byId($id_ekskul);
				$gambar = './assets/uploads/' . $data->gambar;
				unlink($gambar);
			}
		} else {

			$new_gambar = $foto_lama;
		};

		$data = [
			'nama_ekskul' => $nama_ekskul,
			'deskripsi' => $deskripsi,
			'gambar' => $new_gambar

		];

		$id_ekskul = ['id_ekskul' => $id_ekskul];
		$this->M_admin->update_ekskul($data, $id_ekskul, 'ekskul');
		$this->session->set_flashdata('pesan', 'diubah');
		redirect('admin/profil_sekolah' . '#ekskul', 'refresh');
	}

	public function tambah_prestasi()
	{
		$nama_prestasi = $this->input->post('nama_siswa');
		$gambar = $_FILES['gambar']['name'];
		$nama_kegiatan = $this->input->post('nama_prestasi');
		$tahun = $this->input->post('tahun');
		$keterangan = $this->input->post('juara');


		if ($gambar = '') {
		} else {
			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['detect_mime']     = TRUE;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('gambar')) {

				echo 'gagal upload';
			} else {
				$gambar = $this->upload->data('file_name');
			}
		}

		$data =
			[
				'nama_siswa' => $nama_prestasi,
				'tahun' => $tahun,
				'nama_prestasi' => $nama_kegiatan,
				'juara' => $keterangan,
				'gambar' => $gambar

			];

		$this->M_admin->tambah_prestasi($data);
		$this->session->set_flashdata('pesan', 'ditambah');
		redirect('admin/profil_sekolah' . '#prestasi', 'refresh');
	}

	public function ubah_prestasi()
	{
		$nama_prestasi = $this->input->post('nama_prestasi');
		$gambar = $_FILES['gambar']['name'];
		$nama_kegiatan = $this->input->post('nama_kegiatan');
		$tahun = $this->input->post('tahun');
		$keterangan = $this->input->post('juara');
		$foto_lama = $this->input->post('foto_lama');
		$id_prestasi = $this->input->post('id_prestasi');


		if ($gambar) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('gambar')) {
				$new_gambar = $this->upload->data('file_name');
				$data = $this->M_admin->get_prestasi_byId($id_prestasi);
				$gambar = './assets/uploads/' . $data->gambar;
				unlink($gambar);
			}
		} else {

			$new_gambar = $foto_lama;
		};

		$data = [
			'nama_siswa' => $nama_prestasi,
			'tahun' => $tahun,
			'nama_prestasi' => $nama_kegiatan,
			'juara' => $keterangan,
			'gambar' => $new_gambar
		];

		$id_prestasi = ['id_prestasi' => $id_prestasi];
		$this->M_admin->update_prestasi($data, $id_prestasi, 'prestasi');
		$this->session->set_flashdata('pesan', 'diubah');
		redirect('admin/profil_sekolah' . '#prestasi', 'refresh');
	}
	public function hapus_prestasi($id_prestasi)
	{
		$data = $this->M_admin->get_prestasi_byId($id_prestasi);
		$gambar = './assets/uploads/' . $data->gambar;
		$id_prestasi  = array('id_prestasi' => $id_prestasi);
		$this->M_admin->hapus_prestasi($id_prestasi);
		unlink($gambar);
		$this->session->set_flashdata('pesan', 'dihapus');
		redirect('admin/profil_sekolah' . '#prestasi', 'refresh');
	}

	public function tambah_slider()
	{
		$gambar_slider = $_FILES['gambar_slider']['name'];
		$judul_slider = $this->input->post('judul_slider');

		if ($gambar_slider = '') {
		} else {
			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['detect_mime']     = TRUE;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('gambar_slider')) {

				echo 'gagal upload';
			} else {
				$gambar_slider = $this->upload->data('file_name');
			}
		}

		$data = [
			'gambar_slider' => $gambar_slider,
			'judul_slider' => $judul_slider
		];

		$this->M_admin->tambah_slider($data);
		$this->session->set_flashdata('pesan', 'ditambahkan');
		redirect('admin/profil_sekolah' . '#home', 'refresh');
	}

	public function hapus_slider($id_slider)
	{
		$data = $this->M_admin->get_slider_byId($id_slider);
		$gambar = './assets/uploads/' . $data->gambar_slider;
		$id_slider  = array('id_slider' => $id_slider);
		$this->M_admin->hapus_slider($id_slider);
		unlink($gambar);
		$this->session->set_flashdata('pesan', 'dihapus');
		redirect('admin/profil_sekolah' . '#home', 'refresh');
	}
	public function ubah_slider()
	{
		$gambar_slider = $_FILES['gambar_slider']['name'];
		$judul_slider = $this->input->post('judul_slider');
		$gambar_lama = $this->input->post('foto_lama');
		$id_slider = $this->input->post('id_slider');

		if ($gambar_slider) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('gambar_slider')) {
				$new_gambar = $this->upload->data('file_name');
				$data = $this->M_admin->get_slider_byId($id_slider);
				$gambar = './assets/uploads/' . $data->gambar_slider;
				unlink($gambar);
			}
		} else {

			$new_gambar = $gambar_lama;
		};
		$id_slider = ['id_slider' => $id_slider];
		$data = [
			'judul_slider' => $judul_slider,
			'gambar_slider' => $new_gambar
		];

		$this->M_admin->update_slider($id_slider, $data);
		$this->session->set_flashdata('pesan', 'diubah');
		redirect('admin/profil_sekolah' . '#home', 'refresh');
	}

	public function dokumentasi()
	{

		$data = [
			'title' => 'Dokumentasi',
			'dokumentasi' => $this->M_admin->get_dokumentasi()


		];
		$this->load->view('admin_layout/header', $data);
		$this->load->view('admin_layout/sidebar', $data);
		$this->load->view('admin/dokumentasi');
		$this->load->view('admin_layout/footer');
	}

	public function tambah_dokumentasi()
	{
		$foto = $_FILES['foto']['name'];
		$keterangan = $this->input->post('keterangan');
		if ($foto = '') {
		} else {
			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['detect_mime']     = TRUE;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('foto')) {

				echo 'gagal upload';
			} else {
				$foto = $this->upload->data('file_name');
			}
		}

		$data = [

			'nama_foto' => $keterangan,
			'foto' => $foto

		];

		$this->M_admin->tambah_dokumentasi($data);
		$this->session->set_flashdata('pesan', 'ditambahkan');
		redirect('admin/dokumentasi', 'refresh');
	}

	public function hapus_dokumentasi($id_dokumentasi)
	{
		$data = $this->M_admin->get_dokumentasi_byId($id_dokumentasi);
		$foto = './assets/uploads/' . $data->foto;
		$id_dokumentasi  = array('id_dokumentasi' => $id_dokumentasi);
		$this->M_admin->hapus_dokumentasi($id_dokumentasi);
		unlink($foto);
		$this->session->set_flashdata('pesan', 'dihapus');
		redirect('admin/dokumentasi', 'refresh');
	}

	public function ubah_dokumentasi()
	{
		$id_dokumentasi = $this->input->post('id_dokumentasi');
		$foto_lama = $this->input->post('foto_lama');
		$foto = $_FILES['foto']['name'];
		$judul_dokumentasi = $this->input->post('judul_dokumentasi');
		if ($foto) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('foto')) {
				$new_gambar = $this->upload->data('file_name');
				$data = $this->M_admin->get_dokumentasi_byId($id_dokumentasi);
				$gambar = './assets/uploads/' . $data->foto;
				unlink($gambar);
			}
		} else {

			$new_gambar = $foto_lama;
		};

		$id_dokumentasi = ['id_dokumentasi' => $id_dokumentasi];

		$data = [

			'nama_foto' => $judul_dokumentasi,
			'foto' => $new_gambar
		];

		$this->M_admin->update_dokumentasi($id_dokumentasi, $data);
		$this->session->set_flashdata('pesan', 'diubah');
		redirect('admin/dokumentasi', 'refresh');
	}

	public function jadwal()
	{
		$data = [
			'title' => 'Jadwal'
		];
		$this->load->view('admin_layout/header', $data);
		$this->load->view('admin_layout/sidebar', $data);
		$this->load->view('admin/jadwal', $data);
		$this->load->view('admin_layout/footer');
	}
	public function guru()
	{
		$data = [
			'title' => 'Guru',
			'guru' => $this->M_admin->get_guru()

		];
		$this->load->view('admin_layout/header', $data);
		$this->load->view('admin_layout/sidebar', $data);
		$this->load->view('admin/guru', $data);
		$this->load->view('admin_layout/footer');
	}

	public function tambah_guru()
	{
		$nuptk = $this->input->post('nuptk');
		$nama = $this->input->post('nama');
		$tempat_lahir = $this->input->post('tempat_lahir');
		$tgl_lahir = $this->input->post('tgl_lahir');
		$jenis_kelamin = $this->input->post('jenis_kelamin');
		$jabatan = $this->input->post('jabatan');
		$username = $this->input->post('username_guru');
		$password = $this->input->post('password_guru');
		$foto = $_FILES['foto']['name'];

		if ($foto = '') {
		} else {
			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['detect_mime']     = TRUE;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('foto')) {

				$foto = 'user.png';
			} else {
				$foto = $this->upload->data('file_name');
			}
		}

		$data = [

			'nuptk' => $nuptk,
			'nama_guru' => $nama,
			'tempat_lahir' => $tempat_lahir,
			'tgl_lahir' => $tgl_lahir,
			'jenis_kelamin' => $jenis_kelamin,
			'jabatan' => $jabatan,
			'foto' => $foto,
			'username' => $username,
			'password' => $password

		];

		$this->M_admin->tambah_guru($data);
		$this->session->set_flashdata('pesan', 'ditambahkan');
		redirect('admin/guru', 'refresh');
	}
	public function ubah_guru()
	{
		$nuptk = $this->input->post('nuptk');
		$nama = $this->input->post('nama');
		$tempat_lahir = $this->input->post('tempat_lahir');
		$tgl_lahir = $this->input->post('tgl_lahir');
		$jenis_kelamin = $this->input->post('jenis_kelamin');
		$jabatan = $this->input->post('jabatan');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$foto = $_FILES['foto']['name'];
		$foto_lama = $this->input->post('foto_lama');
		$id_guru = $this->input->post('id_guru');

		if ($foto) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('foto')) {
				$data = $this->M_admin->get_guru_byId($id_guru);
				$foto = $data->foto;
				if ($foto != 'user.png') {
					$foto_guru = './assets/uploads/' . $data->foto;
					unlink($foto_guru);
				}

				$new_foto = $this->upload->data('file_name');
			}
		} else {

			$new_foto = $foto_lama;
		};

		$data = [

			'nuptk' => $nuptk,
			'nama_guru' => $nama,
			'tempat_lahir' => $tempat_lahir,
			'tgl_lahir' => $tgl_lahir,
			'jenis_kelamin' => $jenis_kelamin,
			'jabatan' => $jabatan,
			'foto' => $new_foto,
			'username' => $username,
			'password' => $password

		];

		$id_guru = ['id_guru' => $id_guru];

		$this->M_admin->update_guru($data, $id_guru);
		$this->session->set_flashdata('pesan', 'diubah');
		redirect('admin/guru', 'refresh');
	}

	public function hapus_guru($id_guru)
	{
		$data = $this->M_admin->get_guru_byId($id_guru);
		$foto = $data->foto;
		$id_guru  = array('id_guru' => $id_guru);
		if ($foto != 'user.png') {
			$foto_guru = './assets/uploads/' . $data->foto;
			unlink($foto_guru);
		}

		$this->M_admin->hapus_guru($id_guru);

		$this->session->set_flashdata('pesan', 'dihapus');
		redirect('admin/guru', 'refresh');
	}

	public function guru_banyak()
	{
		$data = [

			'input' => $this->input->post('tambah_banyak')
		];

		$this->load->view('admin_layout/header', $data);
		$this->load->view('admin_layout/sidebar', $data);
		$this->load->view('admin/banyak_guru', $data);
		$this->load->view('admin_layout/footer');
	}

	public function tambah_guru_banyak()
	{
		$jumlah = $this->input->post('jumlah');
		for ($i = 1; $i <= $jumlah; $i++) {

			$data = [

				'nuptk' => $this->input->post('nuptk' . $i),
				'nama_guru' => $this->input->post('nama' . $i),
				'username' => $this->input->post('username' . $i),
				'password' => $this->input->post('password' . $i),
				'foto' => 'user.png'

			];

			$this->M_admin->tambah_guru($data);
		}


		$this->session->set_flashdata('pesan', 'ditambahkan');
		redirect('admin/guru', 'refresh');
	}

	public function about()
	{
		$id_about = $this->input->post('id_about');
		$isi_about = $this->input->post('isi_about');

		$data = [
			'isi_about' => $isi_about
		];

		$id_about = ['id_about' => $id_about];
		$this->M_admin->update_about($data, $id_about, 'about');
		$this->session->set_flashdata('pesan', 'disimpan');
		redirect('admin/profil_sekolah' . '#about', 'refresh');
	}

	public function profil()
	{
		$isi_profile = $this->input->post('isi_profile');
		$id_profile = $this->input->post('id_profile');

		$id_profile = ['id_profil' => $id_profile];

		$data = [
			'isi_profil' => $isi_profile
		];

		$this->M_admin->update_profile($id_profile, $data);
		$this->session->set_flashdata('pesan', 'disimpan');
		redirect('admin/profil_sekolah' . '#profile', 'refresh');
	}
	public function sambutan()
	{
		$data = [
			'title' => 'sambutan',
			'sambutan' => $this->M_admin->get_sambutan()->result()

		];
		$this->load->view('admin_layout/header', $data);
		$this->load->view('admin_layout/sidebar');
		$this->load->view('admin/sambutan', $data);
		$this->load->view('admin_layout/footer');
	}

	public function update_sambutan()
	{
		$foto = $_FILES['foto']['name'];
		$sambutan = $this->input->post('sambutan');
		$id_sambutan = $this->input->post('id_sambutan');
		$foto_lama = $this->input->post('foto_lama');


		if ($foto) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('foto')) {
				$new_gambar = $this->upload->data('file_name');
				$data = $this->M_admin->get_sambutan_byId($id_sambutan);
				$gambar = './assets/uploads/' . $data->foto_sambutan;
				unlink($gambar);
			}
		} else {

			$new_gambar = $foto_lama;
		};
		$id_sambutan = ['id_sambutan' => $id_sambutan];
		$data = [
			'isi_sambutan' => $sambutan,
			'foto_sambutan' => $new_gambar
		];

		$this->M_admin->update_sambutan($data, $id_sambutan);
		$this->session->set_flashdata('pesan', 'disimpan');
		redirect('admin/sambutan', 'refresh');
	}

	public function edit_profile()
	{
		$this->load->view('admin_layout/header');
		$this->load->view('admin_layout/sidebar');
		$this->load->view('admin/edit_profil');
		$this->load->view('admin_layout/footer');
	}

	public function ubah_profile()
	{
		$nama_admin = $this->input->post('nama_admin');
		$foto_admin = $_FILES['foto_admin']['name'];
		$id_admin = $this->input->post('id_admin');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$foto_lama = $this->input->post('foto_lama');
		if ($foto_admin) {

			$config['upload_path'] = './assets/uploads';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('foto_admin')) {
				$data = $this->M_admin->get_guru_byId($id_admin);
				$foto = $data->foto_admin;
				if ($foto != 'user.png') {
					$foto_admin = './assets/uploads/' . $data->foto_admin;
					unlink($foto_admin);
				}

				$new_foto = $this->upload->data('file_name');
			}
		} else {

			$new_foto = $foto_lama;
		};

		$id_admin = ['id_admin' => $id_admin];

		$data = [
			'nama_admin' => $nama_admin,
			'foto_admin' => $new_foto,
			'username_admin' => $username,
			'password_admin' => $password
		];

		$this->session->set_userdata($data);
		$this->M_admin->ubah_profile($id_admin, $data);
		$this->session->set_flashdata('pesan', 'disimpan');
		redirect('admin/edit_profile', 'refresh');
	}
}
