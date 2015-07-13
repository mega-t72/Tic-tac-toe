<?php
	class Controller_auth{
		public function LogonQuery(){
			if( isset( $_REQUEST['login'] ) && isset( $_REQUEST['pass'] )){
				$user	= User::Logon( $_REQUEST['login'], md5( $_REQUEST['pass'] ) );
				if( $user ){
					$_SESSION['user']	= $user;
					header( "Location: /" );
				}else{
					header( "Location: /?Logon&error" );
				}
			}else{
				header( "Location: /?Logon" );
			}
		}
		public function SignUpQuery(){
			if( isset( $_REQUEST['login'] ) && isset( $_REQUEST['pass'] )){
				if( User::Create( $_REQUEST['login'], md5( $_REQUEST['pass'] ) ) ){
					header( "Location: /?Logon&ok" );
				}else{
					header( "Location: /?SignUp&error" );
				}
			}else{
				header( "Location: /?SignUp&error" );
			}
		}
		public function Logon(){
			$view	= new View_auth();
			$view->logon();
		}
		public function SignUp(){
			$view	= new View_auth();
			$view->SignUp();
		}
		public function Logout(){
			session_destroy();
			header( "Location: /" );
		}
	}
?>