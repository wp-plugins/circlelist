<?php

class CircleList_AdminMenu {

	function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}

	function add_menus() {

		//return;
		add_submenu_page(
		'edit.php?post_type=' . CircleList::POST_TYPE, __( 'Setting', CircleList::TEXTDOMAIN ),
													 __( 'Setting', CircleList::TEXTDOMAIN ), 'manage_options', 'circlelist_setting',
				  array( $this, 'admin_setting_html' )
		);
	}

	function admin_init() {
		if ( isset( $_POST['circlelist-setting'] ) && $_POST['circlelist-setting'] ) {
			if ( check_admin_referer( 'circlelist-setting', 'circlelist-setting' ) ) {

				if ( "" == $_POST['URL1_Title'] )
					$_POST['URL1_Title'] = 'URL1_Title';
				if ( "" == $_POST['URL2_Title'] )
					$_POST['URL2_Title'] = 'URL2_Title';

				$option['URL1_Title']	 = sanitize_text_field( $_POST['URL1_Title'] );
				$option['URL2_Title']	 = sanitize_text_field( $_POST['URL2_Title'] );

				update_option( 'circlelist_setting', $option );
				//wp_safe_redirect( menu_page_url( 'circlelist_setting', false ) );
			}
		}
	}

	function admin_setting_html() {
		$option = get_option( 'circlelist_setting' );

		if ( "" == $option['URL1_Title'] )
			$option['URL1_Title']	 = 'URL1_Title';
		if ( "" == $option['URL2_Title'] )
			$option['URL2_Title']	 = 'URL2_Title';
		?>
		<div class="wrap">
			<div id="icon-tools" class="icon32"></div>
			<h2><?php echo esc_attr( __( 'Setting', CircleList::TEXTDOMAIN ) ); ?></h2>
			<form action="" method = "post" id="circlelist-setting">
		<?php wp_nonce_field( 'circlelist-setting', 'circlelist-setting' ); ?>

				<table class="form-table">
					<tr>
						<th scope="row"><?php echo esc_attr( __( 'URL1 Title', CircleList::TEXTDOMAIN ) ); ?></th>
						<td><input type="text" name="URL1_Title" value="<?php echo esc_attr( $option['URL1_Title'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_attr( __( 'URL2 Title', CircleList::TEXTDOMAIN ) ); ?></th>
						<td><input type="text" name="URL2_Title" value="<?php echo esc_attr( $option['URL2_Title'] ); ?>"></td>
					</tr>
				</table>
				<input type="submit" value="<?php echo esc_attr( __( 'Save', CircleList::TEXTDOMAIN ) ); ?>" class="button button-primary button-large">
			</form>

		</div>
		<?php
	}

}
