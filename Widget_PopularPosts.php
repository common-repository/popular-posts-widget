<?php
/*
Plugin Name: Popular posts widget (based on comment count)
Plugin URI: http://online-source.net/2010/10/27/popular-posts-widget/
Description: Show popular posts in a widget with several options.
Author: Laurens ten Ham (MrXHellboy)
Version: 1.5
Author URI: http://online-source.net
*/

class os_popular_articles_widget extends WP_Widget 
{

	function os_popular_articles_widget() 
	{
		$widget_ops = array('classname' => 'widget_popular_posts', 'description' => 'Most discussed articles');
		$this->WP_Widget('popular_posts', 'Most discussed articles', $widget_ops);
	}

	function widget($args, $instance) 
	{
		extract($args);

		echo $before_widget;
		$title = strip_tags($instance['title']);
		echo $before_title . $title . $after_title;
		echo $this->os_popular_articles_get($instance);
		echo $after_widget;
	}
    	
	function os_popular_articles_get($instance)
	{
		global $wpdb;
        if ($instance['post_type'] == 'all')
        {
      		$Popular_Posts = $wpdb->get_results("SELECT guid,post_title 
                                                 FROM {$wpdb->prefix}posts
                                                 WHERE post_status = 'publish'
                                                 AND comment_count > 0
                                                 ORDER BY comment_count 
                                                 DESC LIMIT 0 , ".$instance['amount']
                                                    );            
        }
        else
        {
      		$Popular_Posts = $wpdb->get_results("SELECT guid,post_title 
                                                 FROM {$wpdb->prefix}posts
                                                 WHERE post_status = 'publish'
                                                 AND comment_count > 0
                                                 AND post_type = '".$instance['post_type']."'
                                                 ORDER BY comment_count 
                                                 DESC LIMIT 0 , ".$instance['amount']
                                                    );
        }
		
		$li = (empty($instance['liclass'])) ? '<li>' : '<li class="'.$instance['liclass'].'">';
		
		$PP_list = (empty($instance['ulclass'])) ? '<ul>' : '<ul class="'.$instance['ulclass'].'">';
		foreach ($Popular_Posts as $list)
		{
			$PP_list .= $li.'<a href="'. $list->guid .'" title="'.$list->post_title.'">'.$list->post_title.'</a></li>';
		}
		$PP_list .= '</ul>';
		
		return $PP_list;
	}

    
    function os_popular_articles_posttypes($preselect)
    {
        $CustomPostTypes = get_post_types(array('public' => true));
        array_unshift($CustomPostTypes, 'all');
            foreach ($CustomPostTypes as $index => $type)
            {
                if ($type == 'all')
                {
                    $type_info = new stdClass();
                    $type_info->publish = 5;
                }
                else
                {
                    $type_info = wp_count_posts($type);
                }
                
                if ($type_info->publish > 0)
                {
                    if ($type == trim($preselect))
                    {
                        echo '<option value="'.$type.'" selected="selected">'.$type.'</option>';
                    }
                    else
                    {
                        echo '<option value="'.$type.'">'.$type.'</option>';
                    }
                }
            }
        return;
    }

	function update($new_instance, $old_instance) 
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['amount'] = trim(strip_tags($new_instance['amount']));
		$instance['ulclass'] = trim(strip_tags($new_instance['ulclass']));
		$instance['liclass'] = trim(strip_tags($new_instance['liclass']));
        $instance['post_type'] = strip_tags($new_instance['post_type']);

		return $instance;
	}

	function form($instance) 
	{
		$instance = wp_parse_args((array)$instance, array('title' => 'Popular posts', 'amount' => 5, 'ulclass' => '', 'liclass' => '', 'post_type' => ''));
		$title = strip_tags($instance['title']);
		$amount = strip_tags($instance['amount']);
		$ulclass = strip_tags($instance['ulclass']);
		$liclass = strip_tags($instance['liclass']);
        $PostType = strip_tags($instance['post_type']);
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('amount'); ?>">Amount: <input class="widefat" id="<?php echo $this->get_field_id('amount'); ?>" name="<?php echo $this->get_field_name('amount'); ?>" type="text" value="<?php echo attribute_escape($amount); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('ulclass'); ?>">ul class: <input class="widefat" id="<?php echo $this->get_field_id('ulclass'); ?>" name="<?php echo $this->get_field_name('ulclass'); ?>" type="text" value="<?php echo attribute_escape($ulclass); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('liclass'); ?>">li class: <input class="widefat" id="<?php echo $this->get_field_id('liclass'); ?>" name="<?php echo $this->get_field_name('liclass'); ?>" type="text" value="<?php echo attribute_escape($liclass); ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('post_type'); ?>">Post type: <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
                                                                                        <?php $this->os_popular_articles_posttypes($PostType); ?>
                                                                                        </select>
                                                                                         </label></p>
<?php
	}
}

add_action('widgets_init', 'register_os_popular_articles_widget');

function register_os_popular_articles_widget() {
	register_widget('os_popular_articles_widget');
}
?>