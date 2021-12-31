<?php 
	
	namespace Hcode\Model;

	use \Hcode\DB\Sql;
	use \Hcode\Model;
	
	class Categories extends Model{
	    
	    public static function listAll(){

	    	$sql = new Sql();

	    	$result = $sql->select("SELECT *FROM tb_categories ORDER BY
	    	descategory;");

	    	return $result; 
	    }

	    public function save(){

	    	$sql = new Sql();
	    	
			$results = $sql->select("CALL 
			sp_categories_save(:idcategory,:descategory)",[
				":idcategory"=>$this->getidcategory(),
				":descategory"=>$this->getdescategory()
			]);

			$this->setData($results[0]);

			Categories::updateFile();
	    }

	    public function get($idcategory){

	    	$sql = new Sql();

	    	$results = $sql->select("SELECT *FROM tb_categories 
	    	WHERE idcategory = :idcategory",[
	    		":idcategory"=>$idcategory
	    	]);

	    	$this->setData($results[0]);
	    }

	    public function delete(){

	    	$sql = new Sql();

	    	$sql->query("DELETE FROM tb_categories 
	    	WHERE idcategory = :idcategory",[
	    		":idcategory"=>$this->getidcategory()
	    	]);

	    	Categories::updateFile();
	    }

	    public static function updateFile(){

	    	$categories = Categories::listAll();

	    	$html = [];

	    	foreach($categories as $row){
	    		array_push($html,'<li><a href="/categories/'
	    		.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
	    	}

	    	file_put_contents($_SERVER['DOCUMENT_ROOT'].
	    		DIRECTORY_SEPARATOR
	    	."views".DIRECTORY_SEPARATOR."categories-menu.html",
	    	implode('',$html));
	    }

	    public function getProducts($related = true){

	    	$sql = new Sql();
	    	
	    	if($related === true){
	    		
	    		$results = $sql->select("SELECT *FROM tb_products 
	    		WHERE idproduct IN(
				SELECT a.idproduct FROM tb_products a
				INNER JOIN tb_productscategories b USING(idproduct)
				WHERE b.idcategory = :idcategory);",[
					":idcategory"=>$this->getidcategory()
				]);
	    	}else{

	    		$results = $sql->select("SELECT *FROM tb_products 
	    		WHERE idproduct NOT IN(
				SELECT a.idproduct FROM tb_products a
				INNER JOIN tb_productscategories b USING(idproduct)
				WHERE b.idcategory = :idcategory);",[
					":idcategory"=>$this->getidcategory()
				]);
	    	}

	    	return $results;

	    }

	    public function getProductsPage($page=1,$itemsPerPage=3){

	    	$start = ($page - 1) * $itemsPerPage;

	    	$sql = new Sql();

	    	$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS 
	    	*FROM tb_products a INNER JOIN	tb_productscategories b 
	    	ON b.idproduct = a.idproduct INNER JOIN tb_categories c 
	    	ON c.idcategory = b.idcategory 
	    	WHERE c.idcategory = :idcategory LIMIT $start,
	    	$itemsPerPage;",[
				":idcategory"=>$this->getidcategory()
			]);

			$resultTotal = $sql->select("SELECT FOUND_ROWS() 
			AS nrtotal");

			return [
				"data"=>Product::checklist($results),
				'total'=>(int)$resultTotal[0]['nrtotal'],
				'pages'=>ceil($resultTotal[0]['nrtotal']/$itemsPerPage)
			];

	    }

	    public function Pagination($totalpage){

	    	$pages = [];

	    	for($i=1;$i<=$totalpage;$i++){
	    		array_push($pages,[
	    			"link"=>'/categories/'.$this->getidcategory().
	    			"?page=".$i,
	    			"page"=>$i
	    		]);
	    	}

	    	return $pages;
	    }

	    public function addProduct(Product $product){

	    	$sql = new Sql();

	    	$sql->query("INSERT INTO tb_productscategories(idcategory,
	    	idproduct)VALUES(:idcategory,:idproduct);",[
	    		":idcategory"=>$this->getidcategory(),
	    		":idproduct"=>$product->getidproduct()
	    	]);
	    }

	    public function removeProduct(Product $product){

	    	$sql = new Sql();

	    	$sql->query("DELETE FROM tb_productscategories WHERE
	    	idcategory = :idcategory AND idproduct = :idproduct;",[
	    		":idcategory"=>$this->getidcategory(),
	    		":idproduct"=>$product->getidproduct()
	    	]);
	    }

	}
	

?>