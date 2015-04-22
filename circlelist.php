<?php

/*
  Plugin Name: Circle List
  Version: 0.2
  Description: Manage Circle(Booth) List tool.
  Author: YANO Yasuhiro
  Author URI: https://plus.google.com/u/0/+YANOYasuhiro/
  Plugin URI: https://github.com/yyano/circlelist
  Text Domain: circlelist
  Domain Path: /languages
 */

if ( class_exists( 'CircleList' ) ) {
	$CircleList = new CircleList();
}

class CircleList {

	const TEXTDOMAIN	 = 'circlelist';
	const POST_TYPE	 = 'circlelist';
	const TAXONOMY	 = 'circlelist_taxonomy';

	function __construct() {

		register_activation_hook( __FILE__, array( $this, 'register_activation' ) );

		add_action( 'init', array( $this, 'custom_post_type' ) );
		add_action( 'init', array( $this, 'custom_taxonomy' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_circle_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_circle_meta_box' ) );

		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'edit_custom_columns' ), 10, 2 );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'add_custom_columns' ), 10, 2 );
		add_filter( 'wp_insert_post_data', array( $this, 'save_circle_content' ), 10, 2 );

		require_once 'admin_menu.class.php';
		$AdminMenu = new CircleList_AdminMenu();

		require_once 'shortcode.class.php';
		$Shortcode = new CircleList_Shortcode( );
	}

	function register_activation() {
		$option = get_option( 'circlelist_setting' );

		if ( !isset( $option['URL1_Title'] ) ) $option['URL1_Title']	 = 'URL1_Title';
		if ( !isset( $option['URL2_Title'] ) ) $option['URL2_Title']	 = 'URL2_Title';

		update_option( 'circlelist_setting', $option );
	}

	function edit_custom_columns( $columns ) {
		$newColumns['cb']			 = $columns['cb'];
		$newColumns['title']		 = __( 'Space name', self::TEXTDOMAIN );
		$newColumns['circlename']	 = __( 'Circle name', self::TEXTDOMAIN );
		$newColumns['authorname']	 = __( 'Author name', self::TEXTDOMAIN );
		$newColumns['taxonomy']		 = __( 'Taxonomy', self::TEXTDOMAIN );
		$newColumns['date']			 = $columns['date'];
		return $newColumns;
	}

	function add_custom_columns( $column, $post_id ) {
		global $post;
		switch ( $column ) {
			case 'circlename':
				$circlename = get_post_meta( $post_id, 'CircleName', true );
				if ( isset( $circlename ) && $circlename ) {
					echo esc_attr( $circlename );
				} else {
					echo __( 'None', self::TEXTDOMAIN );
				}
				break;

			case 'authorname':
				$authorname = get_post_meta( $post_id, 'AuthorName', true );
				if ( isset( $authorname ) && $authorname ) {
					echo esc_attr( $authorname );
				} else {
					echo __( 'None', self::TEXTDOMAIN );
				}
				break;

			case 'taxonomy':
				$taxonomies = get_the_terms( $post_id, self::TAXONOMY );
				if ( isset( $taxonomies ) ) {
					foreach ( $taxonomies as $taxonomy ) {
						echo esc_attr( $taxonomy->name ) . ", ";
					}
				} else {
					echo __( 'None', self::TEXTDOMAIN );
				}
				break;

			default:
				break;
		}
	}

	function add_circle_meta_box() {
		add_meta_box( 'circleInfo', "Circle infomation", array( $this, 'set_circle_meta_box' ), self::POST_TYPE, 'normal',
				'high' );
	}

	function set_circle_meta_box() {
		global $post;
		wp_nonce_field( wp_create_nonce( __FILE__ ), 'my_nonce' );

		echo '<div id="circleInfoBox">';

		printf( '<p><label>%s<br />', __( 'Space name', self::TEXTDOMAIN ) );
		printf( '%s', esc_html( __( 'Please, set Space number to Title.', self::TEXTDOMAIN ) ) );
		echo '</label></p>';

		printf( '<p><label>%s<br />', __( 'Circle name', self::TEXTDOMAIN ) );
		printf( '<input type="text" name="CircleName" value="%s" style="width:80%%">',
		  esc_html( get_post_meta( $post->ID, 'CircleName', true ) ) );
		echo '</label></p>';

		printf( '<p><label>%s<br />', __( 'Circle (kana)', self::TEXTDOMAIN ) );
		printf( '<input type="text" name="CircleKana" value="%s" style="width:80%%">',
		  esc_html( get_post_meta( $post->ID, 'CircleKana', true ) ) );
		echo '</label></p>';

		printf( '<p><label>%s<br />', __( 'Author name', self::TEXTDOMAIN ) );
		printf( '<input type="text" name="AuthorName" value="%s" style="width:80%%">',
		  esc_html( get_post_meta( $post->ID, 'AuthorName', true ) ) );
		echo '</label></p>';

		printf( '<p><label>%s<br />', __( 'Author (kana)', self::TEXTDOMAIN ) );
		printf( '<input type="text" name="AuthorKana" value="%s" style="width:80%%">',
		  esc_html( get_post_meta( $post->ID, 'AuthorKana', true ) ) );
		echo '</label></p>';

		printf( '<p><label>%s<br />', __( 'Circle image Url', self::TEXTDOMAIN ) );
		printf( '<input type="text" name="CircleImageUrl" value="%s" style="width:80%%">',
		  esc_html( get_post_meta( $post->ID, 'CircleImageUrl', true ) ) );
		echo '</label></p>';

		$option = get_option( 'circlelist_setting' );

		printf( '<p><label>URL : %s<br />', esc_html( $option['URL1_Title'] ) );
		printf( '<input type="text" name="url1" value="%s" style="width:80%%">',
		  esc_html( get_post_meta( $post->ID, 'url1', true ) ) );
		echo '</label></p>';

		printf( '<p><label>URL : %s<br />', esc_html( $option['URL2_Title'] ) );
		printf( '<input type="text" name="url2" value="%s" style="width:80%%">',
		  esc_html( get_post_meta( $post->ID, 'url2', true ) ) );
		echo '</label></p>';

		echo '</div>';
	}

	//http://www.webdesignleaves.com/wp/wordpress/180/
	function save_circle_meta_box( $post_id ) {
		global $post;

		$my_nonce = isset( $_POST['my_nonce'] ) ? $_POST['my_nonce'] : null;

		if ( !wp_verify_nonce( $my_nonce, wp_create_nonce( __FILE__ ) ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( !current_user_can( 'edit_post', $post->ID ) ) {
			return $post_id;
		}

		if ( self::POST_TYPE == $_POST['post_type'] ) {
			update_post_meta( $post->ID, 'CircleName', $_POST['CircleName'] );
			update_post_meta( $post->ID, 'CircleKana', $_POST['CircleKana'] );
			update_post_meta( $post->ID, 'AuthorName', $_POST['AuthorName'] );
			update_post_meta( $post->ID, 'AuthorKana', $_POST['AuthorKana'] );
			update_post_meta( $post->ID, 'CircleImageUrl', $_POST['CircleImageUrl'] );
			update_post_meta( $post->ID, 'url1', $_POST['url1'] );
			update_post_meta( $post->ID, 'url2', $_POST['url2'] );
		}
	}

	function save_circle_content( $data, $postarr ) {
		if ( isset( $_POST['post_type'] ) && self::POST_TYPE == $_POST['post_type'] ) {
			$data['post_content'] = '[CircleInfo]';
		}
		return $data;
	}

	function custom_post_type() {

		$labels	 = array(
			'name'				 => _x( 'Circles', 'Post Type General Name', self::TEXTDOMAIN ),
			'singular_name'		 => _x( 'Circle', 'Post Type Singular Name', self::TEXTDOMAIN ),
			'menu_name'			 => __( 'Circle List', self::TEXTDOMAIN ),
			'name_admin_bar'	 => __( 'Circle List', self::TEXTDOMAIN ),
			'parent_item_colon'	 => __( 'Parent Item:', self::TEXTDOMAIN ),
			'all_items'			 => __( 'All Circles', self::TEXTDOMAIN ),
			'add_new_item'		 => __( 'Add New Circle', self::TEXTDOMAIN ),
			'add_new'			 => __( 'Add Circle', self::TEXTDOMAIN ),
			'new_item'			 => __( 'New Circle', self::TEXTDOMAIN ),
			'edit_item'			 => __( 'Edit Circle', self::TEXTDOMAIN ),
			'update_item'		 => __( 'Update Circle', self::TEXTDOMAIN ),
			'view_item'			 => __( 'View Circle', self::TEXTDOMAIN ),
			'search_items'		 => __( 'Search Circle', self::TEXTDOMAIN ),
			'not_found'			 => __( 'Not found', self::TEXTDOMAIN ),
			'not_found_in_trash' => __( 'Not found in Trash', self::TEXTDOMAIN ),
		);
		$args	 = array(
			'label'					 => __( 'circlelist', self::TEXTDOMAIN ),
			'description'			 => __( 'Circle List for Event', self::TEXTDOMAIN ),
			'labels'				 => $labels,
			'supports'				 => array( 'title', ), //thumbnail
			'taxonomies'			 => array( self::POST_TYPE ),
			'hierarchical'			 => false,
			'public'				 => true,
			'show_ui'				 => true,
			'show_in_menu'			 => true,
			'menu_position'			 => 10,
			'show_in_admin_bar'		 => true,
			'show_in_nav_menus'		 => true,
			'can_export'			 => true,
			'has_archive'			 => false,
			'exclude_from_search'	 => false,
			'publicly_queryable'	 => true,
			'capability_type'		 => 'page',
		);
		register_post_type( self::POST_TYPE, $args );
	}

	function custom_taxonomy() {

		$labels	 = array(
			'name'						 => _x( 'Taxonomies', 'Taxonomy General Name', self::TEXTDOMAIN ),
			'singular_name'				 => _x( 'Taxonomy', 'Taxonomy Singular Name', self::TEXTDOMAIN ),
			'menu_name'					 => __( 'Taxonomy', self::TEXTDOMAIN ),
			'all_items'					 => __( 'All Items', self::TEXTDOMAIN ),
			'parent_item'				 => __( 'Parent Item', self::TEXTDOMAIN ),
			'parent_item_colon'			 => __( 'Parent Item:', self::TEXTDOMAIN ),
			'new_item_name'				 => __( 'New Item Name', self::TEXTDOMAIN ),
			'add_new_item'				 => __( 'Add New Item', self::TEXTDOMAIN ),
			'edit_item'					 => __( 'Edit Item', self::TEXTDOMAIN ),
			'update_item'				 => __( 'Update Item', self::TEXTDOMAIN ),
			'view_item'					 => __( 'View Item', self::TEXTDOMAIN ),
			'separate_items_with_commas' => __( 'Separate items with commas', self::TEXTDOMAIN ),
			'add_or_remove_items'		 => __( 'Add or remove items', self::TEXTDOMAIN ),
			'choose_from_most_used'		 => __( 'Choose from the most used', self::TEXTDOMAIN ),
			'popular_items'				 => __( 'Popular Items', self::TEXTDOMAIN ),
			'search_items'				 => __( 'Search Items', self::TEXTDOMAIN ),
			'not_found'					 => __( 'Not Found', self::TEXTDOMAIN ),
		);
		$args	 = array(
			'labels'			 => $labels,
			'hierarchical'		 => true,
			'public'			 => true,
			'show_ui'			 => true,
			'show_admin_column'	 => true,
			'show_in_nav_menus'	 => true,
			'show_tagcloud'		 => false,
		);
		register_taxonomy( self::TAXONOMY, array( self::POST_TYPE ), $args );
	}

}
