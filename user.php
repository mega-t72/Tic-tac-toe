<?php
	class User{

		public $ID		= 0;
		public $Login	= false;

		public static function Create( $login, $md5 ){
			$db	= explode( "\r\n", file_get_contents( ".users" ) );
			//
			foreach( $db as $line ){
				$u	= explode( "::", $line );
				if( ( count( $u ) == 2 ) && ( $login == urldecode( $u[0] ) ) ){
					return false;
				}
			}
			file_put_contents( ".users", urlencode( $login ) . '::' . $md5 . "\r\n", FILE_APPEND );
			//
			return true;
		}

		public static function GetList(){
			$result	= array();
			$db		= explode( "\r\n", file_get_contents( ".users" ) );
			foreach( $db as $ID => $line ){
				$u	= explode( "::", $line );
				if( count( $u ) == 2 ){
					$result[]	= new User( $ID, urldecode( $u[0] ) );
				}
			}
			return $result;
		}

		public static function Logon( $login, $md5 ){
			$db	= explode( "\r\n", file_get_contents( ".users" ) );
			//
			foreach( $db as $ID => $line ){
				$u	= explode( "::", $line );
				if( ( count( $u ) == 2 ) && ( $login == urldecode( $u[0] ) ) ){
					if( $u[1] != $md5 ){
						//die( $md5 );
						//die( $u[1] );
						return false;
					}
					return new User( $ID, $login );
				}
			}
			//
			return false;
		}

		public function User( $ID, $Login ){
			$this->ID		= $ID;
			$this->Login	= $Login;
			if( $Login === false ){
				$db	= explode( "\r\n", file_get_contents( ".users" ) );
				if( isset( $db[$ID] ) ){
					$u	= explode( "::", $db[$ID] );
					if( count( $u ) == 2 ){
						$this->Login	= $u[0];
					}
				}
			}
		}

		public function GetID(){
			return $this->ID;
		}

		public function GetLogin(){
			return $this->Login;
		}
	}
?>