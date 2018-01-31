<?php

/*
 * @author      Luis Eduardo da Silva Santos <https://github.com/luisdev1>
 * @copyright   Copyright (c) 2018, Luis Eduardo da Silva Santos. (https://github.com/luisdev1)
 * @license     http://opensource.org/licenses/MIT  MIT License
 */

class Datatable {
	
    /*
     * Tabela do banco de dados
     * @var string
     */

    protected $table_name = '';

    /*
     * Colunas a serem ordenadas
     * @var array
     */
    protected $order_columns = array();
    
    /*
     * Colunas a serem selecionadas 
     * @var array
     */    
    protected $select_columns = array();
    
    /*
     * Coluna a ser utilizada na pesquisa
     * @var mixed
     */
    protected $search_columns = '';
    
    /*
     * Condição para a consulta
     * @var mixed
     */
    protected $where = array();    
    
    public function __construct($config = array()){
    	if(count($config) > 0){
    		$this->initialize($config);
    	}
    }
    
    /**
     * Retorna a instancia do CI
     *
     * @return mixed
     */
    public function __get($var) {
    	return get_instance()->$var;
    }    
    
    /**
     * Inicializa a biblioteca carregando os arquivos de configuração ou
     * um array() passada no carregamento da classe.
     *
     * @param $config array()
     * @return void
     */
    public function initialize($config = array()) {
    	foreach ($config as $key => $val) {
    		if (isset($this->$key)) {
    			$method = 'set_' . $key;
    			if (method_exists($this, $method)) {
    				$this->$method($val);
    			} else {
    				$this->$key = $val;
    			}
    		}
    	}
    	return $this;
    }    
    
    // MONTANDO QUERY PARA A DATATABLE
    public function make_query() {
    	$search = $this->input->post('search', TRUE);
    	$order = $this->input->post('order', TRUE);
    	$order_column = array($this->order_columns);
    	$this->db->select($this->select_columns);
    	$this->db->from($this->table_name);
    	if(is_array($this->where) && count($this->where) > 0){
    		$this->db->where($this->where);
    	}
    	if(isset($search['value'])){
    		if(is_array($this->search_columns) && count($this->search_columns) > 0){
    			$this->db->like($this->search_columns[0], $search['value']);
    			if(is_array($this->where) && count($this->where) > 0){
    				$this->db->where($this->where);
    			}                   
    			for($i = 1; $i < count($this->search_columns); $i++){
    				$this->db->or_like($this->search_columns[$i], $search['value']);
    				if(is_array($this->where) && count($this->where) > 0){
    					$this->db->where($this->where);
    				}                       
    			}
    		} else {
    			$this->db->like($this->search_columns, $search['value']);
    			if(is_array($this->where) && count($this->where) > 0){
    				$this->db->where($this->where);
    			}                   
    		}         
    	}
    	if(isset($order[0]['column'])){
    		$this->db->order_by($order_column[$order[0]['column']], $order[0]['dir']);
    	} else {
    		$this->db->order_by($this->table_name.'.id', 'DESC');
    	}
    }
    
    // CONSTRUINDO A TABELA A PARTIR DO LIMITE
    public function make_table() {
    	$this->make_query();
    	if($this->input->post('length') > -1){
    		$this->db->limit($this->input->post('length'), $this->input->post('start'));
    	}
    	$query = $this->db->get();
    	return $query->result_array();
    }
    
    public function get_filtered_data() {
    	$this->make_query();        
    	$query = $this->db->get();
    	return $query->num_rows();
    }
    
    public function get_all_data() {
    	$this->db->select('*');
    	$this->db->from($this->table_name);
    	if(is_array($this->where) && count($this->where) > 0){
    		$this->db->where($this->where);
    	}
    	return $this->db->count_all_results();
    }  

    /*
     * Retorna a tabela construída a partir da $data passada
     */
    public function get_table($data = array()) {
    	if(count($data) == 0){
    		$data = array();
    		$result = array_values($this->make_table());
    		foreach ($result as $row) {
    			$data[] = array_values($row);
    		}
    	}
    	
    	$output = array(
    		'draw' => intval($this->input->post('draw')),
    		'recordsTotal' => $this->datatable->get_all_data(),
    		'recordsFiltered' => $this->datatable->get_filtered_data(),
    		'data' => $data
    	);
    	
    	return json_encode($output, JSON_UNESCAPED_UNICODE);
    }   

  }
