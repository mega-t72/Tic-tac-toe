<?php
	class View_base{
		//	базовый шаблон страницы
		public function html( $body ){
			?><!DOCTYPE html>
			<html>
				<head>
					<meta charset="utf-8" />
					<title><?php echo ln( 'title' ); ?></title>
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" />
					<link rel="stylesheet" href="/style.css" />
					<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
					<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
					<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
					<script src="script.js"></script>
				</head>
				<body><?php echo $body; ?></body>
			</html><?php
		}
		//	показать языковую панель
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
		//	показать панель выбора маркера игры
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
	}
?>