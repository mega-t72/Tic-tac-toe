<?php
	class Controller_turn{
		public function Approve(){
			$user	= $_SESSION['user'];
			if( $user ){
				if( isset( $_REQUEST['OwnerID'] ) ){
					$game	= new Game( intval( $_REQUEST['OwnerID'] ), $user->GetID() );
					$game->Read();
					if( !$game->Active() ){
						$game->Approve();
						header( 'Location: /?OwnerID=' . $_REQUEST['OwnerID'] );
					}else{
						die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
					}
				}else{
					die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
				}
			}
		}
		public function Reject(){
			$user	= $_SESSION['user'];
			if( $user ){
				if( isset( $_REQUEST['OwnerID'] ) ){
					$game	= new Game( intval( $_REQUEST['OwnerID'] ), $user->GetID() );
					$game->Read();
					if( !$game->Active() ){
						$game->Delete();
						header( 'Location: /' );
					}else{
						die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
					}
				}else{
					die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
				}
			}
		}
		public function Step(){
			$user	= $_SESSION['user'];
			if( $user ){
				if( !empty( $_REQUEST['Step'] ) ){
					$owner	= false;
					$hdr	= 'Location: /';
					if( isset( $_REQUEST['OppID'] ) ){
						$game	= new Game( $user->GetID(), intval( $_REQUEST['OppID'] ) );
						$owner	= 1;
						$hdr	.= '?OppID=' . $_REQUEST['OppID'];
					}else if( isset( $_REQUEST['OwnerID'] ) ){
						$game	= new Game( intval( $_REQUEST['OwnerID'] ), $user->GetID() );
						$owner	= 0;
						$hdr	.= '?OwnerID=' . $_REQUEST['OwnerID'];
					}else{
						die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
					}
					$game->Read();
					$game->Step( $owner, intval( $_REQUEST['Step'] ) & 0x1ff );
					header( $hdr );
				}else{
					die( sprintf( '%s(%d): invalid parameter', __FILE__, __LINE__ ) );
				}
			}
		}
	}
?>