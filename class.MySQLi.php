<?php
	class SQL {
		public static $I	= null;
		public $server		= '';
		public $username	= '';
		public $password	= '';
		public $database	= '';
		public $errorDB		= '';
		public $errorTable	= '';
		public $port		= '3306';
		public $Conn		= null;
		public $queries		= 0;
		public $whereT		= false;
		public $connected	= false;
		public $revert		= false;
		public $affected	= 0;
		public $info		= '';
		public $version		= '';
		public $ping		= '';
		
		public function __Construct(){
			$c							= mysqli_connect(
														$this->server,
														$this->username,
														$this->password,
														$this->database,
														$this->port);
			if(mysqli_connect_errno() > 0){
				echo mysqli_connect_error();
				die();
			}else{
				$this->Conn = $c;
				$this->connected		= true;
				$this->info				= mysqli_get_client_info($this->Conn);
				$this->version			= mysqli_get_client_version($this->Conn);
				return $this->Conn;
			}
		}
		public static function I(){
			if(Self::$I == null){
				$c						= __CLASS__;
				Self::$I				= new $c;
			}
			return Self::$I;
		}
		public function query($sql){
			$this->ping					= mysqli_ping($this->Conn);
			$r							= mysqli_query($this->Conn, $sql);
			if(mysqli_errno($this->Conn) > 0){
				echo mysqli_error($this->Conn);die();
			}
			$this->queries++;
			$rd							= $this->toArray($r);
			unset($sql, $r);
			if($this->revert){
				$this->switchDB($this->database);
			}
			$this->affected				= mysqli_affected_rows($this->Conn);
			return $rd;
		}
		private function toArray($res){
			$arr						= [];
			while($row = mysqli_fetch_assoc($res)){
				foreach($row as $key=>$val){
					$arr[]					= [
						$key				=> $val
					];
				}
			}
			unset($res, $key, $val, $row);
			return $arr;
		}
		public function error($text){
			mysqli_select_db($this->Conn, $this->errorDB);
			$ar								= [
				'TX_ERROR_DETAILS'			=> $this->convertText($text)
			];
			$this->insert($ar, $this->errorTable);
		}
		public function update($ar, $table, $where = null, $like = null, $extra = ''){
			$d								= '';
			$whereClause					= $this->genWhere($where);
			$likeClause						= $this->genLike($like);
			foreach($ar as $key=>$val){
				$val						= $this->convertText($val);
				$d .= "`$key`='$val',";
			}
			$sql							= "
			UPDATE `$table` SET $d $whereClause $likeClause $extra
			";
			return $this->query($sql);
		}
		public function getRecord($fields, $table, $where = null, $like = null, $extra){
			$whereClause					= $this->genWhere($where);
			$likeClause						= $this->genLike($like);
			$fieldClause					= '';
			foreach($fields as $key){
				$fieldClause .= "`$key`,";
			}
			$fieldClause					= rtrim($fieldClause, ',');
			$sql							= "
			SELECT $fieldClause FROM `$table` $whereClause $likeClause $extra
			";
			unset($whereClause, $likeClause, $fieldClause, $fields, $table, $where, $like, $extra);
			return $this->query($sql);
		}
		public function genWhere($where){
			$whereClause					= '';
			if($where != null){
				$this->whereT				= true;
				$whereClause				= 'WHERE ';
				foreach($where as $key=>$val){
					$whereClause .= "`$key`='$val' AND ";
				}
				$whereClause				= rtrim($whereClause, ' AND ');
			}
			return $whereClause;
		}
		public function genLike($like){
			$likeClause						= '';
			if($like != null){
				if(!$this->whereT){
					$likeClause				= 'WHERE ';
				}
				foreach($like as $key=>$val){
					$likeClause .= "`$key` LIKE '%$val%' AND ";
				}
				$likeClause					= rtrim($likeClause, ' AND ');
			}
			$this->whereT					= false;
			return $likeClause;
		}
		public function switchDB($database, $once = false){
			mysqli_select_db($this->Conn, $database);
			return true;
		}
		public function insert($ar, $table){
			$columns						= '';
			$values							= '';
			foreach($ar as $key=>$val){
				$val						= $this->convertText($val);
				$columns .= "`$key`,";
				$values .= "'$val',";
			}
			$columns						= rtrim($columns, ',');
			$values							= rtrim($values, ',');
			$sql							= "INSERT INTO `$table` ($columns) VALUES ($values)";
			return $this->query($sql);
		}
		public function convertText($t){
			$from				= ["'"];
			$to					= ["''"];
			return str_replace($from, $to, $t);
		}
		public function changeUser($user, $password){
			mysqli_change_user($this->Conn, $user, $password);
		}
		public function __destruct(){
			mysqli_close($this->Conn);
		}
		public function refresh(){
			$options						= 'MYSQLI_REFRESH_GRANT';
			return mysqli_refresh($this->Conn, $options);
		}
		public function ssl($key, $cert, $ca, $caPath, $cipher){
			mysqli_ssl_set($this->Conn, $key, $cert, $ca, $caPath, $cipher);
		}
	}
?>