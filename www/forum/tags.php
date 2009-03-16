<?php
require_once('./bb-load.php');

bb_repermalink();

// Temporary, refactor this!

if ( !$tag && $tag_name )
	bb_die(__('Tag not found'));

if ( $tag_name && $tag ) :

	$topics = get_tagged_topics($tag->tag_id, $page);
	do_action( 'bb_tag-single.php', $tag->tag_id );

	bb_load_template( 'tag-single.php', array('tag', 'tag_name', 'topics') );
else :

	do_action( 'bb_tags.php', '' );

	bb_load_template( 'tags.php' );
endif;
?>
