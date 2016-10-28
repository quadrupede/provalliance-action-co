<?php
	include "import-config-umetas.php";
//print_r($_POST);

if(isset($_GET['user_id']) && !empty($_POST) )
{
	try 
	{
            $dbh = new PDO($dsn, $user, $password ,  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
                
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); 
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
            //$dbh->setAttribute(PDO::ATTR_ORACLE_NULLS , PDO::NULL_EMPTY_STRING  ); 
            $dbh->setAttribute(PDO::ATTR_CASE , PDO::CASE_LOWER ); 
        } 
	catch (PDOException $e) 
	{
            die( 'Connexion échouée : ' . $e->getMessage());
	}
	
	echo "\n key : $key " ;
	echo "\n user_id : $user_id " ;
	echo "\n more_addresses : $more_addresses " ;
	
	$where = " `meta_key` = :key and `user_id` = :u_id " ;

	
	$set = " `meta_value` = :more_addresses ";
	$set_insert = " , `meta_key` = :key , `user_id` = :u_id " ;

	$sql_getMetas = "select * from $table WHERE $where limit 1 ";

	$sth_select = $dbh->prepare($sql_getMetas);
	$sth_select->bindParam(':u_id', $user_id, PDO::PARAM_INT);
	$sth_select->bindParam(':key', $key, PDO::PARAM_STR);

	try 
        {
            $sth_select->execute();
            $red = $sth_select->fetch();
	} 
        catch (PDOException $e) 
        {
            die( 'select échouée : ' . $e->getMessage());
	}

	if(isset($red->umeta_id) && !is_null($red->umeta_id) && !empty($red->umeta_id) )
	{
		echo "\n update";
		
		$sql_recordMetas = " update $table set $set where $where ";
	}
	else
	{
		echo "\n insert";
                
		$sql_recordMetas = " insert into $table set $set $set_insert ";
	}
	
	echo "\n $sql_recordMetas " ;

	$sth_insert = $dbh->prepare($sql_recordMetas);
        
	$sth_insert->bindParam(':u_id', $user_id, PDO::PARAM_INT);
	$sth_insert->bindParam(':key', $key, PDO::PARAM_STR);
	$sth_insert->bindParam(':more_addresses', $more_addresses , PDO::PARAM_STR);

	try 
        {
            echo "\n record " ;
            
            $sth_insert->execute();
	} 
	catch (PDOException $e) 
	{
            die( 'record échouée : ' . $e->getMessage());
	}
}

?>