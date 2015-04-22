<?php

class CircleList_Shortcode {

	function __construct() {
		add_shortcode( 'CircleList', array( $this, 'shortcode_circleList' ) );
		add_shortcode( 'CircleInfo', array( $this, 'shortcode_circleInfo' ) );
	}

	function shortcode_circleList( $attr ) {

		//var_dump( $attr );

		$option = get_option( 'circlelist_setting' );

		$args		 = array(
			'post_type'				 => 'circlelist',
			'posts_per_page'		 => -1,
			CircleList::TAXONOMY	 => $attr['category'],
			'meta_key'				 => $attr['sort'],
			'orderby'				 => 'meta_value',
			'order'					 => 'ASC',
		);
		$customPosts = new WP_Query( $args );

		//print_r($customPosts);

		echo '<table>';
		echo '<tr>';
		printf( '<th>%s</th>', esc_html( __( 'Space name', CircleList::TEXTDOMAIN ) ) );
		printf( '<th>%s</th>', esc_html( __( 'Circle', CircleList::TEXTDOMAIN ) ) );
		printf( '<th>%s</th>', esc_html( __( 'Author', CircleList::TEXTDOMAIN ) ) );
		printf( '<th>%s</th>', esc_html( __( 'LINK', CircleList::TEXTDOMAIN ) ) );
		echo '</tr>';

		foreach ( $customPosts->posts as $customPost ) {
			setup_postdata( $customPost );

			$circleData = get_post_custom( $customPost->ID );
			//var_dump($circleData);

			echo '<tr>';

			if ( is_user_logged_in() ) {
				printf( '<td><a href="%s">%s</a></td>', $customPost->guid, $customPost->post_title );
			} else {
				printf( '<td>%s</td>', $customPost->post_title );
			}
			printf( '<td>%s</td>', $circleData['CircleName'][0] );
			printf( '<td>%s</td>', $circleData['AuthorName'][0] );

			echo '<td>';
			if ( isset( $circleData['url1'][0] ) && "" != $circleData['url1'][0] ) {
				printf( '<a href="%s">%s</a> ', esc_html( $circleData['url1'][0] ), esc_html( $option['URL1_Title'] ) );
			}
			if ( isset( $circleData['url2'][0] ) && "" != $circleData['url2'][0] ) {
				printf( '<a href="%s">%s</a> ', esc_html( $circleData['url2'][0] ), esc_html( $option['URL2_Title'] ) );
			}

			echo '</td>';
			echo '</tr>';
		}
		wp_reset_postdata();

		echo '</table>';
	}

	function shortcode_circleInfo( $attr ) {
		global $post;

		$option		 = get_option( 'circlelist_setting' );
		$circleData	 = get_post_custom();

		if ( has_post_thumbnail() ) {
			the_post_thumbnail();
		}

		echo '<dl>';
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( __( 'Circle Name', CircleList::TEXTDOMAIN ) ),
												  $circleData['CircleName'][0] );
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( __( 'Circle Kana', CircleList::TEXTDOMAIN ) ),
												  $circleData['CircleKana'][0] );
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( __( 'Author Name', CircleList::TEXTDOMAIN ) ),
												  $circleData['AuthorName'][0] );
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( __( 'Author Kana', CircleList::TEXTDOMAIN ) ),
												  $circleData['AuthorKana'][0] );
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( __( 'circle image Url', CircleList::TEXTDOMAIN ) ),
												  $circleData['CircleImageUrl'][0] );
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( $option['URL1_Title'] ), $circleData['url1'][0] );
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( $option['URL2_Title'] ), $circleData['url2'][0] );

		$taxonomy = get_the_terms( $post->ID, CircleList::TAXONOMY );
		printf( '<dt>%s</dt>', esc_html( __( 'Taxonomy', CircleList::TEXTDOMAIN ) ) );
		if(is_array($taxonomy)){
			foreach ( $taxonomy as $tag ) {
				printf( '<dd>%s</dd>', $tag->name );
			}
			echo '</dl>';
		}
	}

}
