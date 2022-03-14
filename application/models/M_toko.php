<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Toko extends CI_Model {

	var $table = 'temporary_transaction';
    var $column_order = array('kode_barang','nama_barang','harga_satuan','qty','total',null); //set column field database for datatable orderable
    var $column_search = array('kode_barang','nama_barang','harga_satuan'); //set column field database for datatable searchable just firstname , lastname , address are searchable
    var $order = array('kode_barang' => 'desc'); // default order 

	public $id = 'kode_barang';
	var $select = array('temporary_transaction.id_transaksi', 'temporary_transaction.kode_barang', 'temporary_transaction.harga_satuan as harga_satuan_transaksi', 'temporary_transaction.qty', 'temporary_transaction.total', 'barang.nama_barang', 'barang.stok','barang.harga_satuan as harga_satuan_barang', 'satuan.nama_satuan', 'jenis.nama_jenis');
	var $join = array(
		'barang' => 'barang.id_barang = temporary_transaction.kode_barang',
		'satuan' => 'satuan.id_satuan = barang.satuan_id',
		'jenis' => 'jenis.id_jenis = barang.jenis_id',
	);
	var $where = null;

	public function __construct()
	{
		parent::__construct();
		
	}

	private function _get_datatables_query()
    {
         
        //SELECT * FROM ar_users JOIN ar_role ON ar_role.id = ar_users.role
		$select_dat = implode(', ', $this->select);
		$this->db->select($select_dat);
        $this->db->from($this->table);
        //$this->db->join('role', 'ar_users.role = role.id', 'left');
        $this->getJoin();
        $this->getWhere();
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
    }

    public function getJoin()
	{
		$i = 1;
		if ($this->join != null) {
			 foreach ($this->join as $key => $value) {
			 	$a[$i] = $this->db->join($key, $value, 'left');
			 	$i++;
			 }
		} else {
			$a = '';
		}

		 return $this;
	}

	public function wheres($columns, $condition)
    {
        $this->db->where($columns, $condition);
        return $this;
    }

	public function getWhere()
	{
		if ($this->where != null) {
			if (is_array($this->where)) {
				return $this->db->where($this->where);
			}
		} else {
			return false;
		}
	}
 
    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

}

/* End of file M_toko.php */
/* Location: ./application/models/M_toko.php */