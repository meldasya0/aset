<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Software extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');

        $level = $this->session->userdata("level");
        if ($level != '1') {
            redirect("Login");
        }
    }
    
    public function index()
    {
        $data['title'] = "Software";
        $data['Data'] = $this->db->query("SELECT 
        s.id_software, tp.nama_type_hardware AS nama_hardware, nama_software, lisensi, s.tanggal_pembelian, s.harga, hd.nama_pic AS pengguna
        FROM software s
        LEFT JOIN hardware hd ON s.id_hardware=hd.id_hardware
        LEFT JOIN type_hardware tp ON tp.id_type=hd.id_type;
        ")->result();
        $data['Hardware'] = $this->db->query("SELECT 
        tp.nama_type_hardware AS nama_hardware, hd.id_hardware AS id_hardware, hd.harga AS harga, hd.tanggal_pembelian AS tanggal_pembelian, hd.nama_pic AS pengguna, dp.nama_departemen AS nama_departemen, hd.spesifikasi AS spesifikasi, hd.sts_asset AS status_hardware
        FROM hardware hd
        LEFT JOIN departemen dp ON dp.id_departemen=hd.id_departemen
        LEFT JOIN type_hardware tp ON tp.id_type=hd.id_type;
        ")->result();
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar-it');
        $this->load->view('templates/topbar');
        $this->load->view('IT/master/Software/DataSoftware', $data);
        $this->load->view('templates/footer');
    }
    public function tambahDataAksi()
    {
        //set rules validasi 
        $this->form_validation->set_rules('nama_software', 'Nama Software', 'required');
        $this->form_validation->set_rules('id_hardware', 'ID Hardware', 'required|numeric');
        $this->form_validation->set_rules('lisensi', 'Lisensi', 'required');
        $this->form_validation->set_rules('tanggal_pembelian', 'Tanggal Pembelian', 'required');
        $this->form_validation->set_rules('harga', 'Harga', 'required|numeric');
       
        // Jalankan validasi
        if ($this->form_validation->run() == FALSE) {
        // Validasi gagal, atur pesan kesalahan dan kembali ke halaman sebelumnya
         $this->session->set_flashdata('pesan_tambah_gagal', validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', 
         '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span></button></div>'));
        redirect('IT/Software');

        } else {
        // Mengambil data dari formulir
        $nama_software = $this->input->post('nama_software');
        $id_hardware = $this->input->post('id_hardware');
        $lisensi = $this->input->post('lisensi');
        $tanggal_pembelian = $this->input->post('tanggal_pembelian');
        $hargaPost = $this->input->post('harga');
        $harga = preg_replace("/[^0-9]/", "",$hargaPost);


        // Menyimpan data ke dalam database
        $data = array(
            'nama_software' => $nama_software,
            'id_hardware' => $id_hardware,
            'lisensi' => $lisensi,
            'tanggal_pembelian' => $tanggal_pembelian,
            'harga' => $harga,

        );
        $this->db->insert('software', $data);

        // Mengatur pesan flashdata
        $this->session->set_flashdata('pesan_tambah_berhasil', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Data Berhasil Ditambahkan!</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        // Mengalihkan pengguna kembali ke halaman User
        redirect('IT/Software');
    }
}
    public function updateDataAksi()
    {
        // Mengambil data dari formulir
        $id = $this->input->post('id_software');
        $nama_software = $this->input->post('nama_software');
        $id_hardware = $this->input->post('id_hardware');
        $lisensi = $this->input->post('lisensi');
        $tanggal_pembelian = $this->input->post('tanggal_pembelian');
        $harga = $this->input->post('harga');
        
 //set rules validasi 
        $this->form_validation->set_rules('nama_software', 'Nama Software', 'required');
        $this->form_validation->set_rules('id_hardware', 'ID Hardware', 'required|numeric');
        $this->form_validation->set_rules('lisensi', 'Lisensi', 'required');
        $this->form_validation->set_rules('tanggal_pembelian', 'Tanggal Pembelian', 'required');
        $this->form_validation->set_rules('harga', 'Harga', 'required|numeric');
       
        // Jalankan validasi
        if ($this->form_validation->run() == FALSE) {
        // Validasi gagal, atur pesan kesalahan dan kembali ke halaman sebelumnya
         $this->session->set_flashdata('pesan_update_gagal', validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', 
         '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span></button></div>'));
        redirect('IT/Software');
        }else{

        // Menyimpan data ke dalam database
        $data = array(
            'nama_software' => $nama_software,
            'id_hardware' => $id_hardware,
            'lisensi' => $lisensi,
            'tanggal_pembelian' => $tanggal_pembelian,
            'harga' => $harga,

        );
        $where = array(
            'id_software' => $id
        );

        // Melakukan update pada tabel 'divisi' dengan kondisi WHERE yang telah ditentukan
        $this->db->update('software', $data, $where);

        // Mengatur pesan flashdata
        $this->session->set_flashdata('pesan_update_berhasil', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Data Berhasil Diupdate!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>');

        // Mengalihkan pengguna kembali ke halaman Divisi
        redirect('IT/Software');
    }
}
    public function deleteData($id)
    {
        $where = array(
            'id_software' => $id
        );
        $this->db->where($where);
        $this->db->delete('software');

        $this->session->set_flashdata('pesan_hapus_berhasil', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Data Berhasil Dihapus!</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');
        redirect('IT/Software');
    }
}
