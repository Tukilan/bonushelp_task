<?
	
	class DB {
		

		private static $BD;

		private static $host = 'localhost';
		private static $user = 'root';
		private static $db_pass = '';
		private static $db_name = 'bonushelp';
		

		static function fetch($result){
			$res = [];
			if ($result){
				while ($row = mysqli_fetch_assoc($result)) {
					$res[] = $row; 	
				} 
			}
			
			return $res;
		}

		static function connect(){
			self::$BD = mysqli_connect(self::$host,self::$user,self::$db_pass,self::$db_name);
			mysqli_set_charset(self::$BD,"utf8");
		}

		static function disconnect(){
			mysqli_close(self::$BD);
		}

		static function exec($q,$options = []){
			global $site;
			$res = [];
			self::connect();
			if (isset($options['multi'])){
				$queryResult = mysqli_multi_query(self::$BD,$q);
				if ($queryResult){
					if ($result = mysqli_store_result(self::$BD)) {
				        while ($row = mysqli_fetch_row($result)) {
				            $res[] = $row;
				        }
				    }
			    } else {
			    	var_dump("Сообщение ошибки: %s\n", mysqli_error(self::$BD));
			    }
			} else {
				$queryResult = mysqli_query(self::$BD,$q);
				if ($queryResult){
					if (isset($options['fetch'])){
						$res = self::fetch($queryResult);
					} else {
						$res =  $queryResult;
					}	
				} else {
					var_dump("Сообщение ошибки: %s\n", mysqli_error(self::$BD));
				}
			}
			self::disconnect();
			return $res;
		}
	}
?>	