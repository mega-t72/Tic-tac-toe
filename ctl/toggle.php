<?php
	class Controller_toggle{
		public function ChLn(){
			if( !empty( $_REQUEST['ChLn'] ) ){
				$_SESSION['ln']	= $_REQUEST['ChLn'];
			}
			header( "Location: /" );
		}
		public function ChMarker(){
			if( isset( $_REQUEST['ChMarker'] ) ){
				$_SESSION['marker']	= intval( $_REQUEST['ChMarker'] );
			}
			header( "Location: /" );
		}
	}
?>