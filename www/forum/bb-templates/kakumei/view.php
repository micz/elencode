<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; <?php view_name(); ?></h3>

<?php if ( $topics || $stickies ) : ?>

<table id="latest">
<tr>
	<th><?php _e('Topic'); ?></th>
	<th><?php _e('Posts'); ?></th>
	<th><?php _e('Last Poster'); ?></th>
	<th><?php _e('Freshness'); ?></th>
</tr>

<?php if ( $stickies ) : foreach ( $stickies as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <big><a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></big></td>
	<td class="num"><?php topic_posts(); ?></td>
	<td class="num"><?php topic_last_poster(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
</tr>
<?php endforeach; endif; ?>

<?php if ( $topics ) : foreach ( $topics as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></td>
	<td class="num"><?php topic_posts(); ?></td>
	<td class="num"><?php topic_last_poster(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
</tr>
<?php endforeach; endif; ?>
</table>
<div class="nav">
</div>
<?php endif; ?>
<?php view_pages(); ?>

<?php bb_get_footer(); ?>
