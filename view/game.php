<?php
	class View_game extends View_base{
		//	показать панель пользователя
		public function ShowUserBar( $user ){
			if( $user ){
				?><a type="button" class="btn btn-default pull-right" href="/?Logout"><?php
					printf( '%s (%s)', $user->GetLogin(), ln( 'exit' ) );
				?></a><?php
			}else{
				?><a type="button" class="btn btn-default pull-right" href="/?Logon"><?php
					echo ln( 'logon' );
				?></a><?php
			}
		}
		//	показать игровое поле
		public function ShowMap( $game ){
			?><div class="matrix"><?php
				for( $m = 1, $i = 0 ; $i < 9 ; ++$i, $m <<= 1 ){
					?><div class="square"><?
						if( $game->Steps( 1 ) & $m ){
							echo $game->Marker() ? 'o' : 'x';
						}else if( $game->Steps( 0 ) & $m ){
							echo $game->Marker() ? 'x' : 'o';
						}
					?></div><?php
				}
			?></div><br /><?php	
		}
		//	показать статус
		public function ShowStatus( $user, $game ){
			if( $game->Active() ){
				$owe	= ( $game->Owner()->GetID() == $user->GetID() ) ? 1 : 0;
				if( Game::Winner( $game->Steps( $owe ) ) ){
					printf( '<div class="status">%s</div>', ln( 'you win' ) );
				}else if( Game::Winner( $game->Steps( $owe ? 0 : 1 ) ) ){
					printf( '<div class="status">%s</div>', ln( 'you lose' ) );
				}else if( $game->Finished() ){
					printf( '<div class="status">%s</div>', ln( 'dead heat' ) );
				}else if( $owe == $game->OWE() ){
					printf( '<div class="status">%s</div>', ln( 'your turn' ) );
				}else{
					printf( '<div class="status">%s</div>', ln( 'wait for' ) );
				}
				if( $game->Owner()->GetID() == $user->GetID() ){
					$cl	= 'btn btn-default btn-success';
					if( !$game->Finished() ){
						$cl	.= ' hidden';
					}
					?><a title="<?php echo ln( 'again title' ); ?>" href="?NewGame&OppID=<?php
						echo $game->Opp()->GetID();
					?>" type="button" class="<?php
						echo $cl;
					?>" ><?php
						printf( ln( 'again' ) );
					?></a><?
				}
			}else{
				if( $game->Owner()->GetID() != $user->GetID() ){
					?><div class="approve"><?php
						?><a title="<?php echo ln( 'approve title' ); ?>" href="?Approve&OwnerID=<?php echo $game->Owner()->GetID(); ?>" type="button" class="btn btn-default btn-success" ><?php
							printf( ln( 'approve' ) );
						?></a><?
						?>&nbsp;<?php
						?><a title="<?php echo ln( 'reject title' ); ?>" href="?Reject&OwnerID=<?php echo $game->Owner()->GetID(); ?>" type="button" class="btn btn-default" ><?php
							printf( ln( 'reject' ) );
						?></a><?
					?></div><?
				}else{
					printf( '<div class="status">%s</div>', ln( 'wait for' ) );
				}
			}
			printf(
				'<br /><span class="large">%s</span>',
				( $game->OWE() && $game->Active() ) ? ( $game->Marker() ? 'o' : 'x' ) : ( $game->Marker() ? 'x' : 'o' )
			);
		}
		//	показать пользователей
		public function ShowUsers( $user, $users ){
			foreach( $users as $u ){
				?><div class="col-xs-12"><?php
					$self	= $user && ( $u->GetID() == $user->GetID() );
					$owner	= $user && Game::HasGame( $user->GetID(), $u->GetID() );
					$opp	= $user && Game::HasGame( $u->GetID(), $user->GetID() );
					$cl		= 'btn btn-default pull-right';
					if( $self ){
						$cl	.= ' btn-success';
					}
					if( $self || $owner || $opp ){
						$cl	.= ' disabled';
					}
					?><a title="<?php echo ln( 'invite' ); ?>" href="?NewGame&OppID=<?php echo $u->GetID(); ?>" type="button" class="<?php echo $cl; ?>" style="width: 100%;"><?php
						echo htmlspecialchars( $u->GetLogin() ), '&nbsp;';
						if( !$self ){
							?><span class="glyphicon glyphicon-plus-sign"></span><?php
						}
					?></a><?
				?></div><?php
			}
		}
		//	показать список своих игр
		public function ShowOwnGames( $own_games ){
			foreach( $own_games as $g ){
				$u		= $g->Opp();
				$cur	= isset( $_REQUEST['OppID'] ) && ( intval( $_REQUEST['OppID'] ) == $u->GetID() );
				$cl		= 'btn btn-default pull-right';
				$title	= ln( 'go to game' );
				if( $cur ){
					$cl	.= ' btn-success disabled';
					$title	= '';
				}
				?><div class="col-xs-12"><?php
					?><a title="<?php echo $title; ?>" href="?OppID=<?php echo $u->GetID(); ?>" type="button" class="<?php echo $cl; ?>" style="width: 100%;"><?php
						echo '>> ', htmlspecialchars( $u->GetLogin() ), '&nbsp;';
					?></a><?php
				?></div><?php
			}
		}
		//	показать список чужих игр
		public function ShowOppGames( $opp_games ){
			foreach( $opp_games as $g ){
				$u		= $g->Owner();
				$cur	= isset( $_REQUEST['OwnerID'] ) && ( intval( $_REQUEST['OwnerID'] ) == $u->GetID() );
				$cl		= 'btn btn-default pull-right';
				$title	= ln( 'go to game' );
				if( $cur ){
					$cl	.= ' btn-success disabled';
					$title	= '';
				}
				if( !$cur && !$g->Active() ){
					$cl	.= ' blink';
				}
				?><div class="col-xs-12"><?php
					?><a title="<?php echo $title; ?>" href="?OwnerID=<?php echo $u->GetID(); ?>" type="button" class="<?php echo $cl; ?>" style="width: 100%;"><?php
						echo '<< ', htmlspecialchars( $u->GetLogin() );
					?></a><?php
				?></div><?php
			}
		}
		//
		public function index( $users, $own_games, $opp_games, $game ){

			ob_start();

			?><div class="container-fluid">
				<div class="row">
					<div class="col-xs-12"><?php
						//	языковая панель
						$this->ShowLanguageBar();
						//
						?><span class="pull-right">&nbsp;</span><?php
						//	панель маркера
						$this->ShowMarkerBar();
						//
						?><span class="pull-right">&nbsp;</span><?php
						//	панель пользователя
						$user	= $_SESSION['user'];
						$this->ShowUserBar( $user );
					?></div>
					<div class="col-xs-10 text-center"><?php
						if( $game ){
							//	вывод рабочего поля
							$this->ShowMap( $game );
							//	вывод статуса и инструментов управления ходом игры
							$this->ShowStatus( $user, $game );
						}
					?></div>
					<div class="col-xs-2">
						<div class="row panel"><?php
							//	выводим список пользователей
							if( $user ){
								?><div class="col-xs-12"><?php echo ln( 'users' ); ?></div><?php
								?><div class="users"><?php
									$this->ShowUsers( $user, $users );
								?></div><?php
							}
							//	выводим список своих игр
							if( !empty( $own_games ) ){
								?><div class="col-xs-12"><?php echo ln( 'owned list' ); ?></div><?php
								$this->ShowOwnGames( $own_games );
							}
							//	выводим список чужих игр
							$cl	= 'other_header col-xs-12';
							if( empty( $opp_games ) ){
								$cl	.=' hidden';
							}
							?><div class="<?php echo $cl; ?>"><?php echo ln( 'other list' ); ?></div><?php
							?><div class="opp_games"><?php
								$this->ShowOppGames( $opp_games );
							?></div><?php
						?></div>
					</div>
				</div>
			</div><?php

			$this->html( ob_get_clean() );
		}
	}
?>