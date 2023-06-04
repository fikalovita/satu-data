<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_index extends CI_Controller
{
    public function get_data()
    {
        $tgl_awal = '2023-03-01';
        $tgl_akhir = '2023-04-30';
        $this->db->select('pasien.no_ktp, nm_pasien, tgl_daftar, alamat');
        $this->db->where("tgl_daftar BETWEEN '$tgl_awal'  AND '$tgl_akhir' ");
        $this->db->from('pasien');
        return $this->db->get();
    }
}
