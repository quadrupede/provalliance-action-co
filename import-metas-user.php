<?php
/* Connexion à une base ODBC avec l'invocation de pilote */
$dsn = 'mysql:dbname=actionco;host=127.0.0.1';
$user = 'actionco';
$password = 'GaeNg6ge';

//print_r($_POST);

if(isset($_GET['user_id']) && !empty($_POST) )
{
	try 
	{
		$dbh = new PDO($dsn, $user, $password);
	} 
	catch (PDOException $e) 
	{
		die( 'Connexion échouée : ' . $e->getMessage());
	}

	$key = "wc_multiple_shipping_addresses" ; // "_wcmca_additional_addresses";
	$user_id = intval($_GET['user_id']);
	$more_addresses = addslashes(serialize($_POST));
	
	echo "\n key : $key " ;
	echo "\n user_id : $user_id " ;
	echo "\n more_addresses : $more_addresses " ;
	
	$table = " `action_co_usermeta` ";
	$where = " `meta_key` = :key and `user_id` = :u_id " ;

	
	$set = " `meta_value` = :more_addresses ";
	$set_insert = " , `meta_key` = :key , `user_id` = :u_id " ;

	$sql_getMetas = "select * from $table WHERE $where limit 1 ";

	$sth_select = $dbh->prepare($sql_getMetas);
	$sth_select->bindParam(':u_id', $user_id, PDO::PARAM_INT);
	$sth_select->bindParam(':key', $key, PDO::PARAM_STR);

	try {

		$sth_select->execute();
		$red = $sth_select->fetch(PDO::FETCH_OBJ);
		
	} catch (PDOException $e) {
		die( 'select échouée : ' . $e->getMessage());
	}

	if(isset($red->umeta_id) && !is_null($red->umeta_id) && !empty($red->umeta_id) )
	{
		echo "\n update";
		
		$sql_recordMetas = " update $table set $set where $where limit 1 ";
	}
	else
	{
		echo "\n insert";
		$sql_recordMetas = " insert into $table set $set $set_insert limit 1";
	}
	
	echo "\n $sql_recordMetas " ;

	$sth_insert = $dbh->prepare($sql_recordMetas);
	$sth_insert->bindParam(':u_id', $user_id, PDO::PARAM_INT);
	$sth_insert->bindParam(':key', $key, PDO::PARAM_STR);
	$sth_insert->bindParam(':more_addresses', $more_addresses , PDO::PARAM_STR);

	try {

		echo "\n record " ;
		$sth_insert->execute();
		
		/* Récupération de la première ligne uniquement depuis le résultat */
		$sth_insert->fetch();

		/* L'appel suivant à closeCursor() peut être requis par quelques drivers */
		$sth_insert->closeCursor();

	} 
	catch (PDOException $e) 
	{
		die( 'record échouée : ' . $e->getMessage());
	}
}

?>