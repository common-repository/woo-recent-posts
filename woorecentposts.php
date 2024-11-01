<?php
/*
Plugin Name: Woo Recent Posts
Plugin URI: http://www.jdstudios.us/plugins/woo-recent-posts
Description: Displays a list of recent posts using thumbnails in your woo theme. Filter by category, set the number to display, and set the woo thumbnail size.
Author: JD Studios (James Dudley)
Version: 1.0
Author URI: http://www.jdstudios.us

 * *************************************************************************
	Copyright 2011 JD Studios  (email: info@jdstudios.us)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * *************************************************************************

 */

// insert the stylesheet into the head
function woorecentposts_stylesheet() {
	echo '<link href="' . plugins_url() . '/woo-recent-posts/styles.css" rel="stylesheet" type="text/css" />';
}
add_action('wp_head', 'woorecentposts_stylesheet');

// the Widget class
class WooRecentPosts extends WP_Widget {
	/** constructor */
	function WooRecentPosts() {
		parent::WP_Widget( 'woorecentpostswidget', $name = 'Woo - Recent Posts' );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$category = apply_filters( 'widget_category', $instance['category'] );
		$number = apply_filters( 'widget_number', $instance['number'] );
		$thumbsize = apply_filters( 'widget_thumbsize', $instance['thumbsize'] );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		?>
		<ul>
			<?php
				$args = array(
					'numberposts' => $number,
					'orderby'         	=> 'post_date',
					'order'           	=> 'DESC',
					'post-type' 		=> 'post',
					'post-status' 	=> 'publish'
				);
				// filter by category?
				if ($category != "") {
					$args2 = array('category' => $category);
					$args = array_merge($args, $args2);
				}
				global $post;
				$latestnews = get_posts($args);
				foreach( $latestnews as $post ) :	setup_postdata($post);
					global $woo_options;
			?>
				<li>
					<? woo_image('link=img&width='. $thumbsize .'&height='. $thumbsize .'&class=thumbnail '.$woo_options['woo_magazine_b_align']); ?>
					<a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
					<h5><?php echo get_the_date(); ?></h5>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['number'] = strip_tags($new_instance['number']);
		$instance['thumbsize'] = strip_tags($new_instance['thumbsize']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$category = esc_attr( $instance[ 'category' ] );
			$number = esc_attr( $instance[ 'number' ] );
			$thumbsize = esc_attr( $instance[ 'thumbsize' ] );
		}
		else {
			$title = __( 'New title', 'text_domain' );
			$category = __( '', 'text_domain' );
			$number = __( '5', 'text_domain' );
			$thumbsize = __( '40', 'text_domain' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Posts Category:'); ?></label> 
			<select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
				<?php
				if ($category == "") {
					echo '<option value="">All</option>';
				} else { 
					// show the selected category as the first option
					$chosen_category = get_category($category);
					$option = '<option value="' . $chosen_category->cat_ID . '">';
					$option .= $chosen_category->cat_name;
					$option .= ' ('.$chosen_category->category_count.')';
					$option .= '</option>';
					echo $option;
					echo '<option value="">All</option>';
				}
				// show all the categories
				$categories=  get_categories(); 
				foreach ($categories as $category) {
					$option = '<option value="' . $category->cat_ID . '">';
					$option .= $category->cat_name;
					$option .= ' ('.$category->category_count.')';
					$option .= '</option>';
					echo $option;
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number to Show:'); ?></label> 
			<input style="width:50px;" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('thumbsize'); ?>"><?php _e('Thumbnail Size:'); ?></label> 
			<input style="width:50px;" id="<?php echo $this->get_field_id('thumbsize'); ?>" name="<?php echo $this->get_field_name('thumbsize'); ?>" type="text" value="<?php echo $thumbsize; ?>" />px
		</p>

		<?php 
	}

} // class WooRecentPosts


// register WooRecentPosts widget
add_action( 'widgets_init', create_function( '', 'return register_widget("WooRecentPosts");' ) );

?>