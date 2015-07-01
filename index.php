<?php
	include( "user.php" );
	include( "game.php" );
	include( "view.php" );
	include( "controller.php" );
	include( "ln.php" );
	//
	session_start();
	//
	$c	= new Controller;
	if( isset( $_REQUEST['New'] ) ){
		$c->NewGame();
	}else if( isset( $_REQUEST['Logon'] ) ){
		$c->Logon();
	}else if( isset( $_REQUEST['SignUp'] ) ){
		$c->SignUp();
	}else if( isset( $_REQUEST['SignUpQuery'] ) ){
		$c->SignUpQuery();
	}else if( isset( $_REQUEST['LogonQuery'] ) ){
		$c->LogonQuery();
	}else if( isset( $_REQUEST['Logout'] ) ){
		$c->Logout();
	}else if( isset( $_REQUEST['ChLn'] ) ){
		$c->ChLn();
	}else if( isset( $_REQUEST['ChMarker'] ) ){
		$c->ChMarker();
	}else if( isset( $_REQUEST['Step'] ) ){
		$c->Step();
	}else if( isset( $_REQUEST['Approve'] ) ){
		$c->Approve();
	}else if( isset( $_REQUEST['Reject'] ) ){
		$c->Reject();
	}else if( isset( $_REQUEST['Ajax'] ) ){
		$c->Ajax();
	}else{
		$c->Index();
	}
?>