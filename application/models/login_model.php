<?php if(!defined('BASEPATH'))
	exit('No direct script access allowed');
class login_model extends CI_Model{
	var $table="tb_user";
//untuk simpan data
	
function get_all_produk(){
    $query=$this->db->get($this->table);
    return $query->result();
}
function simpan($data)
{
    $query = $this->db->insert($this->table, $data);
    return $query;
}
function simpan_regis($data)
{
    $query = $this->db->insert($this->table, $data);
    return $query;
}
function ambil_id($id_menu)
{
    $this->db->where('id_menu',$id_menu);
    $query = $this->db->get($this->table);

    return $query->result();
}
function simpan_id($data, $nik)
{
    //$id = $this->input->post('id');
    $this->db->where('nik', $nik);
    $query = $this->db->update($this->table, $data);

    return $query;
}

function hapus_id($nik)
{
    $this->db->where('nik', $nik);
    $query = $this->db->delete($this->table);	

    return $query;
}

function hapus_menu_id($id_menu)
{
    $this->db->where('id_menu', $id_menu);
    $query = $this->db->delete($this->table);	

    return $query;
}

function forgot_pass($user){
    $this->db->get_where(['email' => $email])->row_array();
    
}


}
?>