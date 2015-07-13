<?php
	class View_auth extends View_base{
		//	вывод формы "входа"
		public function logon(){
			ob_start();

			?><form method="POST" action="/?LogonQuery">
				<div class="container-fluid">
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
				</div>
			</form><?php

			$this->html( ob_get_clean() );
		}
		//	вывод формы "регистрации"
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
	}
?>