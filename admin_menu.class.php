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
		add_submenu_page(
				'edit.php?post_type=' . CircleList::POST_TYPE, __( 'Import/Export', CircleList::TEXTDOMAIN ),
													   __( 'Import/Export', CircleList::TEXTDOMAIN ), 'manage_options', 'circlelist_import_export',
					array( $this, 'admin_import_export_html' )
		);
	}

	function admin_init() {
		if ( isset( $_POST['circlelist-setting'] ) && $_POST['circlelist-setting'] ) {
			if ( check_admin_referer( 'circlelist-setting', 'circlelist-setting' ) ) {
				$this->update_post_setteing( $_POST );
			}
		}

		if ( isset( $_POST['circlelist-export'] ) && $_POST['circlelist-export'] ) {
			if ( check_admin_referer( 'circlelist-export', 'circlelist-export' ) ) {
				$this->update_post_export( $_POST );
			}
		}
		if ( isset( $_POST['circlelist-import'] ) && $_POST['circlelist-import'] ) {
			if ( check_admin_referer( 'circlelist-import', 'circlelist-import' ) ) {
				$this->update_post_import( $_POST, $_FILES );
			}
		}
	}

	function update_post_import( $Post, $Files ) {
		print_r($Post);
		print_r($Files);
		if (is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
			
		}
	}

	function update_post_export( $Post ) {

		//print_r($Post);
		$args = array(
			'posts_per_page' => '-1',
			'post_type'		 => 'circlelist',
		);

		$query	 = new WP_Query( $args );
		$posts	 = $query->get_posts();

		$export_title = 'ID' . "\t" .
				__( 'Space name', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'Circle name', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'Circle (kana)', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'Author name', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'Author (kana)', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'Circle Image url', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'url1', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'url2', CircleList::TEXTDOMAIN ) . "\t" .
				__( 'Taxonomies', CircleList::TEXTDOMAIN ) . "\r\n";

		$export_body = "";
		foreach ( $posts as $post ) {
			$customFileds = get_post_custom( $post->ID );

			$taxonomies	 = get_the_terms( $post->ID, CircleList::TAXONOMY );
			$tagText	 = "";
			if ( isset( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$tagText .= esc_attr( $taxonomy->name ) . ", ";
				}
				$tagText = preg_replace( '/, $/', '', $tagText );
			}

			$export_body .= $post->ID . "\t" .
					$post->post_title . "\t" .
					$customFileds['CircleName'][0] . "\t" .
					$customFileds['CircleKana'][0] . "\t" .
					$customFileds['AuthorName'][0] . "\t" .
					$customFileds['AuthorKana'][0] . "\t" .
					$customFileds['CircleImageUrl'][0] . "\t" .
					$customFileds['url1'][0] . "\t" .
					$customFileds['url2'][0] . "\t" .
					$tagText . "\r\n";
		}

		$filename = CircleList::POST_TYPE . "_" . date( 'YmdHis' ) . ".txt";
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );

		echo $export_title;
		echo $export_body;
		die;
	}

	function update_post_setteing( $Post ) {

		if ( "" == $Post['URL1_Title'] ) $Post['URL1_Title']	 = 'URL1_Title';
		if ( "" == $Post['URL2_Title'] ) $Post['URL2_Title']	 = 'URL2_Title';

		$option						 = array();
		$option['CirclrImageHeight'] = sanitize_text_field( $Post['CirclrImageHeight'] );
		$option['CircleImageWidth']	 = sanitize_text_field( $Post['CircleImageWidth'] );
		$option['URL1_Title']		 = sanitize_text_field( $Post['URL1_Title'] );
		$option['URL2_Title']		 = sanitize_text_field( $Post['URL2_Title'] );

		update_option( 'circlelist_setting', $option );
	}

	function admin_setting_html() {
		$option = get_option( 'circlelist_setting' );

		if ( !isset( $option['URL1_Title'] ) || "" == $option['URL1_Title'] ) $option['URL1_Title']		 = 'URL1_Title';
		if ( !isset( $option['URL2_Title'] ) || "" == $option['URL2_Title'] ) $option['URL2_Title']		 = 'URL2_Title';
		if ( !isset( $option['CirclrImageHeight'] ) || "" == $option['CirclrImageHeight'] )
				$option['CirclrImageHeight'] = '100';
		if ( !isset( $option['CircleImageWidth'] ) || "" == $option['CircleImageWidth'] ) $option['CircleImageWidth']	 = '100';
		?>
		<div class="wrap">
			<div id="icon-tools" class="icon32"></div>
			<h2><?php echo esc_attr( __( 'Setting', CircleList::TEXTDOMAIN ) ); ?></h2>
			<form action="" method = "post" id="circlelist-setting">
				<?php wp_nonce_field( 'circlelist-setting', 'circlelist-setting' ); ?>

				<table class="form-table">
					<tr>
						<th scope="row"><?php echo esc_attr( __( 'Circle Image Height', CircleList::TEXTDOMAIN ) ); ?></th>
						<td><input type="text" name="CirclrImageHeight" value="<?php echo esc_attr( $option['CirclrImageHeight'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_attr( __( 'Circle Image Width', CircleList::TEXTDOMAIN ) ); ?></th>
						<td><input type="text" name="CircleImageWidth" value="<?php echo esc_attr( $option['CircleImageWidth'] ); ?>"></td>
					</tr>
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

	function admin_import_export_html() {
		?>
		<div class="wrap"></div>
		<div id="icon-tools" class="icon32"></div>
		<h2><?php echo esc_attr( __( 'Import', CircleList::TEXTDOMAIN ) ); ?></h2>
		<form action="" method = "post" id="circlelist-import" enctype="multipart/form-data">
			<input type="file" name="example1" accept="text/" required>
			<?php
			wp_nonce_field( 'circlelist-import', 'circlelist-import' );
			printf( '<input type="submit" value="%s" class="button button-primary button-large">',
		   esc_attr( __( 'Import', CircleList::TEXTDOMAIN ) ) );
			?>
		</form>
		<hr />
		<div class="wrap"></div>
		<div id="icon-tools" class="icon32"></div>
		<h2><?php echo esc_attr( __( 'Export', CircleList::TEXTDOMAIN ) ); ?></h2>
		<form action="" method = "post" id="circlelist-export">
			<?php
			wp_nonce_field( 'circlelist-export', 'circlelist-export' );
			printf( '<input type="submit" value="%s" class="button button-primary button-large">',
		   esc_attr( __( 'Export', CircleList::TEXTDOMAIN ) ) );
			?>
		</form>
		<?php
	}

}
