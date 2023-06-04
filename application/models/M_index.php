<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_index extends CI_Controller
{
    public function get_data()
    {
        $this->db->select('aplicare_ketersediaan_kamar.kd_bangsal, kapasitas');
        $this->db->from('aplicare_ketersediaan_kamar');
        return $this->db->get();
    }
}
