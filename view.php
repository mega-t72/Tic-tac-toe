<?php
	function ln( $ID ){
		global $messages;
		//
		$LID	= $_SESSION['ln'] ? $_SESSION['ln'] : 'en';
		return isset( $messages[$LID][$ID] ) ? $messages[$LID][$ID] : false;
	}
	class View{
		public function html( $body ){
			?><!DOCTYPE html>
			<html>
				<head>
					<meta charset="utf-8" />
					<title><?php echo ln( 'title' ); ?></title>
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
					<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
					<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
					<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
					<style>
						.matrix{
							width			: 301px;
							height			: 301px;
							margin			: 0 auto;
							border-right	: 1px solid gray;
							border-bottom	: 1px solid gray;
							margin-top		: 1em;
						}
						.square:hover{
							color				: white;
							background-color	: #cccccc;
							cursor				: pointer;
						}
						.square{
							width		: 100px;
							height		: 100px;
							border-left	: 1px solid gray;
							border-top	: 1px solid gray;
							float		: left;
						}
						.large, .square{
							font-size	: 60pt;
						}
						.al{
							width	: 80px;
							display	: inline-block;
						}
						.error{
							color	: red;
						}
					</style>
					<script>
						function Is_Winner( Steps ){
							var wins	= [0x007, 0x124, 0x1c0, 0x049, 0x038, 0x092, 0x111, 0x054 ];
							for( var i in wins ){
								if( ( Steps & wins[i] ) == wins[i] ){
									return true;
								}
							}
							return false;
						}
						$( document ).ready( function(){
							$( '.square' ).click( function(){
								var step		= 1 << $( this ).index();
								location.href	+= '&Step=' + step;
							} );
							setInterval( function(){
								$( '.blink' ).fadeOut( 500, function(){
									$( '.blink' ).fadeIn( 500 );
								} );
							}, 2000 );
        				} );
						setInterval( function(){
							var u	= '/?Ajax';
							var opp	= location.search.match( /OppID=(\d+)/ );
							var own	= location.search.match( /OwnerID=(\d+)/ );
							if( opp ){
								u	+= '&OppID=' + opp[1];
							}else if( own ){
								u	+= '&OwnerID=' + own[1];
							}
							$.ajax( {
								method		: 'POST',
								url			: u,
								dataType	: 'json',
								cache		: false,
								error		: function( jqXHR, textStatus, errorThrown ){
									console.log( 'Error in ajax:' + textStatus );
								},
								success		: function( data, textStatus, jqXHR ){
									if( data.game ){
										var owns	= parseInt( data.game.Data[1] );
										var opps	= parseInt( data.game.Data[2] );
										var marker	= parseInt( data.game.Data[3] );
										var active	= parseInt( data.game.Data[4] );
										var $matrix	= $( '.matrix .square' );
										var $status	= $( '.status' );
										var $large	= $( 'span.large' );
										var i, ic, m, owe, $cell;
										//	обновление игрового поля
										for( m = 1, i = 0, ic = 9 ; ic-- ; m <<= 1, ++i ){
											$cell	= $matrix.eq( i );
											if( owns & m ){
												$cell.text( marker ? 'o' : 'x' );
											}else if( opps & m ){
												$cell.text( marker ? 'x' : 'o' );
											}else{
												$cell.text( '' );
											}
										}
										//	обновление статуса
										owe	= ( parseInt( data.game.OwnerID ) == parseInt( data.ID ) ) ? 1 : 0;
										if( active ){
											if( Is_Winner( owe ? owns : opps ) ){
												$status.text( data.ln['you win'] );
											}else if( Is_Winner( owe ? opps : owns ) ){
												$status.text( data.ln['you lose'] );
											}else if( ( opps | owns ) == 0x1ff ){
												$status.text( data.ln['dead heat'] );
											}else if( owe == parseInt( data.game.Data[0] ) ){
												$status.text( data.ln['your turn'] );
											}else{
												$status.text( data.ln['wait for'] );
											}
										}else if( !owe && !$( '.approve' ).length ){
											//	если владелец предложил переиграть - перезагрузить страницу с предложением
											location.reload( true );
										}
										//	обновление индикатора игрока
										$large.text(
											( parseInt( data.game.Data[0] ) && active ) ? ( marker ? 'o' : 'x' ) : ( marker ? 'x' : 'o' )
										);
									}else if( /OppID=\d+/.test( location.search ) ){
										//	если игра не существует - редирект на главную страницу
										location.href	= '/';
									}
								}
							} );
						}, 2000 );
					</script>
				</head>
				<body><?php echo $body; ?></body>
			</html><?php
		}
		public function logon(){
			ob_start();
			?><form method="POST" action="/?LogonQuery"><div class="container-fluid">
				<div class="row"><?
					//	языковая панель
					$this->ShowLanguageBar();
					//
					?><div class="col-xs-12 text-center"><?php
							if( isset( $_REQUEST['error'] ) ){
								?><span class="error"><?php echo ln( 'login error' ); ?></span><?php
							}else if( isset( $_REQUEST['ok'] ) ){
								?><span class="error"><?php echo ln( 'signup ok' ); ?></span><?php
							}
						?><br />
						<span class="al"><?php echo ln( 'login' ); ?>:</span><input name="login" type="text" /><br />
						<span class="al"><?php echo ln( 'password' ); ?>:</span><input name="pass" type="password" /><br />
						<br />
						<input type="submit" class="btn btn-default" value="<?php echo ln( 'send' ); ?>"/>
						&nbsp; <a href="/?SignUp" type="button" class="btn btn-default"><?php echo ln( 'signup' ); ?></a>
					</div>
				</div>
			</div></form><?php
			$this->html( ob_get_clean() );
		}
		public function SignUp(){
			ob_start();
			?><form method="POST" action="/?SignUpQuery"><div class="container-fluid">
				<div class="row"><?php
					//	языковая панель
					$this->ShowLanguageBar();
					//
					?><div class="col-xs-12 text-center"><?php
							if( isset( $_REQUEST['error'] ) ){
								?><span class="error"><?php echo ln( 'signup error' ); ?></span><?php
							}
						?><br />
						<span class="al"><?php echo ln( 'login' ); ?>:</span><input name="login" type="text" /><br />
						<span class="al"><?php echo ln( 'password' ); ?>:</span><input name="pass" type="password" /><br />
						<br />
						<input type="submit" class="btn btn-default" value="<?php echo ln( 'send' ); ?>" />
					</div>
				</div>
			</div></form><?php
			$this->html( ob_get_clean() );
		}
		public function ShowLanguageBar(){
			$cl		= 'btn btn-default';
			$en_cl	= $cl;
			$ru_cl	= $cl;
			if( !isset( $_SESSION['ln'] ) || ( $_SESSION['ln'] == 'en' ) ){
				$en_cl	.= ' btn-success';
			}
			if( isset( $_SESSION['ln'] ) && ( $_SESSION['ln'] == 'ru' ) ){
				$ru_cl	.= ' btn-success';
			}
			?><div class="btn-group pull-right" role="group">
				<a href="/?ChLn=ru" type="button" class="<?php echo $ru_cl; ?>">Ru</a>
				<a href="/?ChLn=en" type="button" class="<?php echo $en_cl; ?>">En</a>
			</div><?
		}
		public function ShowMarkerBar(){
			$cl		= 'btn btn-default';
			$x_cl	= $cl;
			$o_cl	= $cl;
			if( !isset( $_SESSION['marker'] ) || ( intval( $_SESSION['marker'] ) == 0 ) ){
				$x_cl	.= ' btn-success';
			}
			if( isset( $_SESSION['marker'] ) && ( intval( $_SESSION['marker'] ) == 1 ) ){
				$o_cl	.= ' btn-success';
			}
			?><div class="btn-group pull-right" role="group">
				<a href="/?ChMarker=0" type="button" class="<?php echo $x_cl; ?>">x</a>
				<a href="/?ChMarker=1" type="button" class="<?php echo $o_cl; ?>">o</a>
			</div><?
		}
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
						$this->ShowMarkerBar()
						//
						?><span class="pull-right">&nbsp;</span><?php
						//
						$user	= $_SESSION['user'];
						if( $user ){
							?><a type="button" class="btn btn-default pull-right" href="/?Logout"><?php
								printf( '%s (%s)', $user->GetLogin(), ln( 'exit' ) );
							?></a><?php
						}else{
							?><a type="button" class="btn btn-default pull-right" href="/?Logon"><?php
								echo ln( 'logon' );
							?></a><?php
						}
					?></div>
					<div class="col-xs-10 text-center"><?php
						if( $game ){
							//	вывод рабочего поля
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
							//	вывод статуса и инструментов управления ходом игры
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
									?><a title="<?php echo ln( 'again title' ); ?>" href="?New&OppID=<?php echo $game->Opp()->GetID(); ?>" type="button" class="<?php echo $cl; ?>" ><?php
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
					?></div>
					<div class="col-xs-2">
						<div class="row panel"><?php
							//	выводим список пользователей
							if( $user ){
								?><div class="col-xs-12"><?php echo ln( 'users' ); ?></div><?php
								foreach( $users as $u ){
									?><div class="col-xs-12"><?php
										$self	= $user && $u->GetID() == $user->GetID();
										$owner	= $user && Game::HasGame( $user->GetID(), $u->GetID() );
										$opp	= $user && Game::HasGame( $u->GetID(), $user->GetID() );
										$cl		= 'btn btn-default pull-right';
										if( $self ){
											$cl	.= ' btn-success';
										}
										if( $self || $owner || $opp ){
											$cl	.= ' disabled';
										}
										?><a title="Отправить приглашение" href="?New&OppID=<?php echo $u->GetID(); ?>" type="button" class="<?php echo $cl; ?>" style="width: 100%;"><?php
											echo htmlspecialchars( $u->GetLogin() ), '&nbsp;';
											if( !$self ){
												?><span class="glyphicon glyphicon-plus-sign"></span><?php
											}
										?></a><?
									?></div><?php
								}
							}
							//	выводим список своих игр
							if( !empty( $own_games ) ){
								?><div class="col-xs-12"><?php echo ln( 'owned list' ); ?></div><?php
								foreach( $own_games as $g ){
									$u		= $g->Opp();
									$cur	= isset( $_REQUEST['OppID'] ) && ( intval( $_REQUEST['OppID'] ) == $u->GetID() );
									$cl		= 'btn btn-default pull-right';
									$title	= 'Перейти к игре';
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
							//	выводим список чужих игр
							if( !empty( $opp_games ) ){
								?><div class="col-xs-12"><?php echo ln( 'other list' ); ?></div><?php
								foreach( $opp_games as $g ){
									$u		= $g->Owner();
									$cur	= isset( $_REQUEST['OwnerID'] ) && ( intval( $_REQUEST['OwnerID'] ) == $u->GetID() );
									$cl		= 'btn btn-default pull-right';
									$title	= 'Перейти к игре';
									if( $cur ){
										$cl	.= ' btn-success disabled';
										$title	= '';
									}
									if( !$cur && !$g->Active() ){
										$cl	.= ' blink';
									}
									?><div class="col-xs-12 opp_game"><?php
										?><a title="<?php echo $title; ?>" href="?OwnerID=<?php echo $u->GetID(); ?>" type="button" class="<?php echo $cl; ?>" style="width: 100%;"><?php
											echo '<< ', htmlspecialchars( $u->GetLogin() );
										?></a><?php
									?></div><?php
								}
							}
						?></div>
					</div>
				</div>
			</div><?php
			$this->html( ob_get_clean() );
		}
	}
?>