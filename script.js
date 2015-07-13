function htmlspecialchars( text ){
	return text
		.replace( /&/g, '"&amp;' )
		.replace( /</g, '&lt;' )
		.replace( />/g, '&gt;' )
		.replace( /"/g, '&quot;' )
		.replace( /'/g, '&#039;' );
}

function Is_Winner( Steps ){
	var wins	= [0x007, 0x124, 0x1c0, 0x049, 0x038, 0x092, 0x111, 0x054 ];
	for( var i in wins ){
		if( ( Steps & wins[i] ) == wins[i] ){
			return true;
		}
	}
	return false;
}

function Is_Finished( Data ){
	return ( ( Data[1] | Data[2] ) == 0x1ff ) || Is_Winner( Data[1] ) || Is_Winner( Data[2] );
}

var Blink_hide	= true;

$( document ).ready( function(){
	$( '.square' ).click( function(){
		var step		= 1 << $( this ).index();
		location.href	+= '&Step=' + step;
	} );
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
			var i, j;
			//	обновление списка пользователей
			$( '.users' ).empty();
			for( i in data.users ){
				var html	= '';
				var HasGame	= false;
				var Self	= parseInt( data.ID ) == parseInt( data.users[i].ID );
				if( !Self ){
					for( j in data.opp_games ){
						if( parseInt( data.opp_games[j].OwnerID ) == parseInt( data.users[i].ID ) ){
						!Is_Winner( data.opp_games[j].Data[1] ) && !Is_Winner( data.opp_games[j].Data[2] )
							HasGame	= false == Is_Finished( data.opp_games[j].Data );
							break;
						}
					}
					if( !HasGame ){
						for( j in data.own_games ){
							if( parseInt( data.own_games[j].OppID ) == parseInt( data.users[i].ID ) ){
								HasGame	= false == Is_Finished( data.own_games[j].Data );
								break;
							}
						}
					}
				}
				var cl	= 'btn btn-default pull-right';
				if( Self ){
					cl	+= ' btn-success';
				}
				if( Self || HasGame ){
					cl	+= ' disabled';
				}
				html	+= '<div class="col-xs-12">';
				html	+=   '<a title="' + data.ln['invite'] + '" href="?NewGame&OppID=' + data.users[i].ID + '" type="button" class="' + cl + '" style="width: 100%;">';
				html	+=     htmlspecialchars( data.users[i].Login ) + '&nbsp;';
				if( !Self ){
					html	+= '<span class="glyphicon glyphicon-plus-sign"></span>';
				}
				html	+=   '</a>';
				html	+= '</div>';
				//
				$( html ).appendTo( $( '.users' ) );
			}
			//	обновление списка чужих игр
			$( '.opp_games' ).empty();
			for( i in data.opp_games ){
				var OwnerID	= data.opp_games[i].OwnerID;
				var Login	= '';
				for( j in data.users ){
					if( data.users[j].ID == OwnerID ){
						Login	= data.users[j].Login;
						break;
					}
				}
				if( Login ){
					var cur		= own && ( parseInt( own[1] ) == parseInt( OwnerID ) );
					var cl		= 'btn btn-default pull-right';
					var title	= data.ln['go to game'];
					var active	= data.opp_games[i].Data[4];
					var html	= '';
					if( cur ){
						cl		+= ' btn-success disabled';
						title	= '';
					}
					if( !cur && !active ){
						cl	+= ' blink';
					}
					html	+= '<div class="col-xs-12">';
					html	+=   '<a title="' + title + '" href="?OwnerID=' + OwnerID + '" type="button" class="' + cl + '" style="width: 100%;">';
					html	+=     '<< ' + htmlspecialchars( Login );
					html	+=   '</a>';
					html	+= '</div>';
					//
					var $ctl	= $( html );
					$ctl.appendTo( $( '.opp_games' ) );
				}
			}
			//	показать/скрыть заголовок
			if( $( '.opp_games' ).children().length ){
				$( '.other_header' ).removeClass( 'hidden' );
			}else{
				$( '.other_header' ).addClass( 'hidden' );
			}
			//	Blink
			if( Blink_hide ){
				$( '.blink' ).animate( { opacity: 0 }, 600 );
			}else{
				$( '.blink' ).animate( { opacity: 1 }, 600 );
			}
			Blink_hide	= Blink_hide ? false : true;
			//
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
}, 900 );
