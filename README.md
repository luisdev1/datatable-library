Esta é uma biblioteca voltada para o framework Codeigniter com objetivo de facilitar o uso da datatables em projetos, de forma com que seu preenchimento seja feita com AJAX, por isso o retorno final da biblioteca são os dados em formato JSON.


## Exemplo de Uso:

```php
$config = array(
	'table_name' => 'users',
	'order_columns' => array('name', 'email'),
	'select_columns' => array('id', 'name', 'email', 'status'),
	'search_columns' => array('name', 'email'),
	'where' => array('status' => 1)
);

$this->load->library('datatable', $config);

$result = $this->datatable->make_table();
```
## Exemplo de uso customizado dos valores retornados:
```php
$data = array();
foreach ($result as $row) {
	$array = array();
	$array[] = word_wrap($row['name'], 30);
	$array[] = word_wrap($row['email'], 30);
	$array[] = ($row['status'] == 0) ? 'Ativado' : 'Desativado';
	$data[] = $array;
}

echo $this->datatable->get_table($data);
```
## Uso sem modificações dos valores retornados:
```php
echo $this->datatable->get_table();
```
