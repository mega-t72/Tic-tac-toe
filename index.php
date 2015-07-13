<?php
	function ln( $ID ){
		global $messages;
		//
		$LID	= $_SESSION['ln'] ? $_SESSION['ln'] : 'en';
		return isset( $messages[$LID][$ID] ) ? $messages[$LID][$ID] : false;
	}

	include( "user.php" );
	include( "game.php" );
	include( "view/base.php" );
	include( "view/auth.php" );
	include( "view/game.php" );
	include( "ctl/auth.php" );
	include( "ctl/game.php" );
	include( "ctl/toggle.php" );
	include( "ctl/turn.php" );
	include( "ln.php" );
	//
	session_start();
	//
	$keys	= array_keys( $_REQUEST );
	$auth	= array_intersect( $keys, array( 'Logon', 'SignUp', 'SignUpQuery', 'LogonQuery', 'Logout' ) );
	if( !empty( $auth ) ){
		$c	= new Controller_auth;
		$m	= array_shift( $auth );
		$c->$m();
	}else{
		$toggle	= array_intersect( $keys, array( 'ChLn', 'ChMarker' ) );
		if( !empty( $toggle ) ){
			$c	= new Controller_toggle;
			$m	= array_shift( $toggle );
			$c->$m();
		}else{
			$turn	= array_intersect( $keys, array( 'Step', 'Approve', 'Reject', ) );
			if( !empty( $turn ) ){
				//print_r( $turn );print_r( $m );die;
				$c	= new Controller_turn;
				$m	= array_shift( $turn );
				$c->$m();
			}else{
				$game	= array_intersect( $keys, array( 'NewGame', 'Ajax', ) );
				$c		= new Controller_game;
				if( !empty( $game ) ){
					$m	= array_shift( $game );
					$c->$m();
				}else{
					$c->index();
				}
			}
		}
	}
?>