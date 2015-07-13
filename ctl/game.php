<?php
	class Controller_game{
		public function NewGame(){
			$user	= $_SESSION['user'];
			if( $user && isset( $_REQUEST['OppID'] ) ){
				$opp	= new User( intval( $_REQUEST['OppID'] ), false );
				if( $opp ){
					if( !isset( $_SESSION['marker'] ) || ( $_SESSION['marker'] == 0 ) ){
						$marker	= 0;
					}else{
						$marker	= 1;
					}
					Game::Create( $user->GetID(), $opp->GetID(), $marker );
					header( sprintf( "Location: /?OppID=%s", $opp->GetID() ) );
				}else{
					die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
				}
			}else{
				die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
			}
		}
		public function Ajax(){
			$user	= $_SESSION['user'];
			if( $user ){
				$out				= array();
				$out['users']		= User::GetList();
				$out['own_games']	= Game::GetList( glob( sprintf( "games/.%d-*", $user->GetID() ) ) );
				$out['opp_games']	= Game::GetList( glob( sprintf( "games/.*-%d", $user->GetID() ) ) );
				$out['ID']			= $user->GetID();
				//	чтение базы игр
				foreach( $out['own_games'] as $g ){
					$g->Read();
				}
				foreach( $out['opp_games'] as $g ){
					$g->Read();
				}
				//
				if( isset( $_REQUEST['OppID'] ) ){
					$out['game']	= new Game( $user->GetID(), intval( $_REQUEST['OppID'] ) );
				}else if( isset( $_REQUEST['OwnerID'] ) ){
					$out['game']	= new Game( intval( $_REQUEST['OwnerID'] ), $user->GetID() );
				}
				if( $out['game'] ){
					if( !$out['game']->Read() ){
						unset( $out['game'] );
					}
				}
				//
				$out['ln']['you win']		= ln( 'you win' );
				$out['ln']['you lose']		= ln( 'you lose' );
				$out['ln']['your turn']		= ln( 'your turn' );
				$out['ln']['wait for']		= ln( 'wait for' );
				$out['ln']['dead heat']		= ln( 'dead heat' );
				$out['ln']['go to game']	= ln( 'go to game' );
				$out['ln']['invite']		= ln( 'invite' );
				//
				printf( json_encode( $out, JSON_FORCE_OBJECT ) );
			}else{
				printf( '{}' );
			}
		}
		public function Index(){
			$user		= $_SESSION['user'];
			if( !$user ){
				return header( 'Location: /?Logon' );
			}
			$users		= User::GetList();
			$own_games	= $user ? Game::GetList( glob( sprintf( "games/.%d-*", $user->GetID() ) ) ) : array();
			$opp_games	= $user ? Game::GetList( glob( sprintf( "games/.*-%d", $user->GetID() ) ) ) : array();
			$game		= null;
			$view		= new View_game();
			//	чтение базы игр
			foreach( $own_games as $g ){
				$g->Read();
			}
			foreach( $opp_games as $g ){
				$g->Read();
			}
			//
			if( $user ){
				if( isset( $_REQUEST['OppID'] ) ){
					$game	= new Game( $user->GetID(), intval( $_REQUEST['OppID'] ) );
				}else if( isset( $_REQUEST['OwnerID'] ) ){
					$game	= new Game( intval( $_REQUEST['OwnerID'] ), $user->GetID() );
				}
				if( $game ){
					$game->Read();
				}
			}
			$view->Index( $users, $own_games, $opp_games, $game );
		}
	}
?>