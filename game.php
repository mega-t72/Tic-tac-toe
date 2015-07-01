<?php

	define( G_OWE,			0 );
	define( G_OWNER_STEPS,	1 );
	define( G_OPP_STEPS,	2 );
	define( G_MARKER,		3 );
	define( G_ACTIVE,		4 );
	define( G_COUNT,		5 );

	class Game{

		public $OwnerID	= 0;
		public $OppID	= 0;
		public $Data	= array();

		public function Game( $OwnerID, $OppID ){
			$this->OwnerID	= $OwnerID;
			$this->OppID	= $OppID;
		}

		public static function GetList( $Paths ){
			$result	= array();
			if( $Paths ){
				foreach( $Paths as $path ){
					if( preg_match( "/games\/\.(\d)-(\d)/", $path, $match ) ){
						$result[]	= new Game( intval( $match[1] ), intval( $match[2] ) );
					}
				}
			}
			return $result;
		}

		public function Valid(){
			return count( $this->Data ) == G_COUNT;
		}

		public function Read(){
			$filename	= "games/.{$this->OwnerID}-{$this->OppID}";
			if( file_exists( $filename ) ){
				$cfg	= file_get_contents( $filename );
				if( $cfg ){
					$this->Data	= array_map(
						function( $a ){
							return intval( $a );
						}, explode( "::", $cfg )
					);
					return true;
				}
			}
			return false;
		}

		public function Write(){
			return file_put_contents( "games/.{$this->OwnerID}-{$this->OppID}", implode( '::', $this->Data ) );
		}

		public function Delete(){
			return unlink( "games/.{$this->OwnerID}-{$this->OppID}" );
		}

		public function Finished(){
			if( self::Winner( $this->Data[G_OWNER_STEPS] ) ){
				return true;
			}
			if( self::Winner( $this->Data[G_OPP_STEPS] ) ){
				return true;
			}
			return ( $this->Data[G_OWNER_STEPS] | $this->Data[G_OPP_STEPS] ) == 0x1ff;
		}

		public function Approve(){
			$this->Data[G_ACTIVE]	= 1;
			return $this->Write();
		}

		public function Restart( $Marker ){
			$this->Data					= array();
			$this->Data[G_OWE]			= 1;
			$this->Data[G_OWNER_STEPS]	= 0;
			$this->Data[G_OPP_STEPS]	= 0;
			$this->Data[G_MARKER]		= $Marker;
			$this->Data[G_ACTIVE]		= 0;
		}

		public function Step( $Owner, $Step ){
			if( $this->Data[G_ACTIVE] && ( $this->Data[G_OWE] == intval( $Owner ) ) && !$this->Finished() ){
				if( 0 == ( ( $this->Data[G_OWNER_STEPS] | $this->Data[G_OPP_STEPS] ) & $Step ) ){
					$this->Data[$Owner ? G_OWNER_STEPS : G_OPP_STEPS]	|= $Step;
					$this->Data[G_OWE]	= intval( $Owner ) ? 0 : 1;
					return $this->Write();
				}
			}
			return false;
		}

		public function OWE(){
			return $this->Data[G_OWE];
		}

		public function Active(){
			return $this->Data[G_ACTIVE];
		}

		public function Steps( $Owner ){
			return $this->Data[$Owner ? G_OWNER_STEPS : G_OPP_STEPS];
		}

		public function Marker(){
			return $this->Data[G_MARKER];
		}

		public function Owner(){
			return new User( $this->OwnerID, false );
		}

		public function Opp(){
			return new User( $this->OppID, false );
		}

		public static function Winner( $Steps ){
			foreach( array( 0x007, 0x124, 0x1c0, 0x049, 0x038, 0x092, 0x111, 0x054 ) as $s ){
				if( ( $Steps & $s ) == $s ){
					return true;
				}
			}
			return false;
		}

		public static function HasGame( $OwnerID, $OppID ){
			$g	= new Game( $OwnerID, $OppID );
			return $g->Read() && !$g->Finished();
		}

		public static function Create( $OwnerID, $OppID, $Marker ){
			$Game	= new Game( $OwnerID, $OppID );
			$Revert	= new Game( $OppID, $OwnerID );
			if( $Game->Read() && $Game->Valid() && !$Game->Finished() ){
				unset( $Game );
				return false;
			}
			if( $Revert->Read() && $Revert->Valid() && !$Revert->Finished() ){
				unset( $Revert );
				return false;
			}else if( $Revert->Read() ){
				$Revert->Delete();
			}
			$Game->Restart( $Marker );
			$Game->Write();
			return $Game;
		}

	}
?>