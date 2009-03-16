<?php

function bb_get_admin_header() {
	do_action('bb_admin-header.php');
	include('admin-header.php');
	do_action('bb_get_admin_header');
}

function bb_get_admin_footer() {
	do_action('bb_admin-footer.php', '');
	include('admin-footer.php');
}

function bb_admin_notice( $message, $class = false ) {
	if ( is_string($message) ) {
		$message = "<p>$message</p>";
		$class = $class ? $class : 'updated';
	} elseif ( is_wp_error($message) ) {
		$errors = $message->get_error_messages();
		switch ( count($errors) ) :
		case 0 :
			return false;
			break;
		case 1 :
			$message = "<p>{$errors[0]}</p>";
			break;
		default :
			$message = "<ul>\n\t<li>" . join( "</li>\n\t<li>", $errors ) . "</li>\n</ul>";
			break;
		endswitch;
		$class = $class ? $class : 'error';
	} else {
		return false;
	}

	$message = "<div class='$class'>$message</div>";
	$message = str_replace("'", "\'", $message);
	$lambda = create_function( '', "echo '$message';" );
	add_action( 'bb_admin_notices', $lambda );
	return $lambda;
}

/* Menu */

function bb_admin_menu_generator() {
	global $bb_menu, $bb_submenu;
	$bb_menu = array();
	$bb_menu[0]  = array(__('Dashboard'), 'moderate',       'index.php');
	$bb_menu[5]  = array(__('Users'),     'moderate',       'users.php');
	$bb_menu[10] = array(__('Manage'),    'moderate',       'content.php');
	$bb_menu[15] = array(__('Design'),    'manage_themes',  'themes.php');
	$bb_menu[20] = array(__('Settings'),  'manage_options', 'options-general.php');
	$bb_menu[25] = array(__('Plugins'),   'use_keys',       'plugins.php');

	$bb_submenu = array();
	$bb_submenu['users.php'][5]  = array(__('Find'),       'moderate',   'users.php');
	$bb_submenu['users.php'][10] = array(__('Moderators'), 'moderate',   'users-moderators.php');
	$bb_submenu['users.php'][15] = array(__('Blocked'),    'edit_users', 'users-blocked.php');

	$bb_submenu['content.php'][5]  = array(__('Topics'),  'moderate',      'content.php');
	$bb_submenu['content.php'][10] = array(__('Posts'),   'moderate',      'content-posts.php');
	$bb_submenu['content.php'][15] = array(__('Forums'),  'manage_forums', 'content-forums.php');
	$bb_submenu['content.php'][20] = array(__('Recount'), 'recount',       'site.php');

	$bb_submenu['themes.php'][5]   = array(__('Themes'), 'manage_themes', 'themes.php');

	$bb_submenu['plugins.php'][5]  = array(__('Plugins'), 'manage_plugins', 'plugins.php');
	
	$bb_submenu['options-general.php'][5]  = array(__('General'),               'manage_options', 'options-general.php');
	$bb_submenu['options-general.php'][10] = array(__('WordPress Integration'), 'manage_options', 'options-wordpress.php');

	do_action('bb_admin_menu_generator','');
	ksort($bb_menu);
}

function bb_admin_add_menu($display_name, $capability, $file_name)
{
	global $bb_menu;
	if ($display_name && $capability && $file_name) {
		$bb_menu[] = array($display_name, $capability, $file_name);
	}
}

function bb_admin_add_submenu($display_name, $capability, $file_name, $parent = 'plugins.php')
{
	global $bb_submenu;
	if ($display_name && $capability && $file_name) {
		$bb_submenu[$parent][] = array($display_name, $capability, $file_name);
	}
}

function bb_get_current_admin_menu() {
	global $bb_menu, $bb_submenu, $bb_admin_page, $bb_current_menu, $bb_current_submenu;
	foreach ( $bb_submenu as $m => $b ) {
		foreach ( $b as $s ) {
			if ( $s[2] == $bb_admin_page ) {
				$bb_current_submenu = $s;
				$bb_current_menu = $m;
				break;
			}
		}
	}
	if ( !isset($bb_current_menu) ) :
		$bb_current_menu = $bb_menu[0];	// Dashboard is currently the only supported top with no subs
		$bb_current_submenu = false;
	else :
		foreach ( $bb_menu as $m ) {
			if ( $m[2] == $bb_current_menu ) {
				$bb_current_menu = $m;
				break;
			}
		}
	endif;
	if ( $bb_current_submenu && !bb_current_user_can( $bb_current_submenu[1] ) || !bb_current_user_can( $bb_current_menu[1] ) ) {
		wp_redirect( bb_get_option( 'uri' ) );
		exit();
	}
}

function bb_admin_title() {
	global $bb_current_menu, $bb_current_submenu;
	$title = bb_get_option('name') . ' &#8250; ' . $bb_current_menu[0] . ( $bb_current_submenu ? ' &raquo; ' . $bb_current_submenu[0] : '' ) . ' &#8212; bbPress';
	echo $title;
}

function bb_admin_menu() {
	global $bb_menu, $bb_submenu, $bb_current_menu, $bb_current_submenu;
	$r = "<ul id='bb-admin-menu'>\n";
	foreach ( $bb_menu as $m ) :
		if ( bb_current_user_can($m[1]) ) :
			$class = ( $m[2] == $bb_current_menu[2] ) ? " class='current'" : '';
			$r .= "\t<li$class><a href='" . bb_get_option('path') . 'bb-admin/' . bb_get_admin_tab_link($m) . "'>{$m[0]}</a></li>\n";
		endif;
	endforeach;
	$r .= '</ul>';
	if ( $bb_current_submenu ) :
		$r .= "\n\t<ul id='bb-admin-submenu'>\n";
		foreach ( $bb_submenu[$bb_current_menu[2]] as $m ) :
			if ( bb_current_user_can($m[1]) ) :
				$class = ( $m[2] == $bb_current_submenu[2] ) ? " class='current'" : '';
				$r .= "\t\t<li$class><a href='" . bb_get_option('path') . 'bb-admin/' . bb_get_admin_tab_link($m) . "'>{$m[0]}</a></li>\n";
			endif;
		endforeach;
		$r .= "\t</ul>\n";
	endif;
	echo $r;
}

function bb_get_admin_tab_link( $tab ) {
	if ( is_array($tab) )
		$tab = $tab[2];
	if ( strpos($tab, '.php') !== false )
		return $tab;
	else
		return 'admin-base.php?plugin=' . $tab;
}

/* Stats */

function bb_get_recently_moderated_objects( $num = 5 ) {
	$post_query  = new BB_Query( 'post', array( 'per_page' => $num, 'post_status' => '-normal', 'topic_status' => 0 ) ); // post_time != moderation_time;
	$topic_query = new BB_Query( 'topic', array( 'per_page' => $num, 'topic_status' => '-normal' ) ); // topic_time == topic_start_time != moderation_time;

	$objects = array();
	if ( $post_query->results )
		foreach ( array_keys($post_query->results) as $key )
			$objects[bb_gmtstrtotime($post_query->results[$key]->post_time)] = array('type' => 'post', 'data' => $post_query->results[$key]);
	if ( $topic_query->results )
		foreach ( array_keys($topic_query->results) as $key )
			$objects[bb_gmtstrtotime($topic_query->results[$key]->topic_time)] = array('type' => 'topic', 'data' => $topic_query->results[$key]);
	krsort($objects);
	return array_slice($objects, 0, $num);
}

/* Users */

// Not bbdb::prepared
function bb_get_ids_by_role( $role = 'moderator', $sort = 0, $page = 1, $limit = 50 ) {
	global $bbdb, $bb_last_countable_query;
	$sort = $sort ? 'DESC' : 'ASC';
	$key = $bbdb->escape( $bbdb->prefix . 'capabilities' );

	if ( !$page = abs( (int) $page ) )
		$page = 1;
	$limit = abs( (int) $limit );

	$limit = ($limit * ($page - 1)) . ", $limit";

	$role = $bbdb->escape_deep($role);

	if ( is_array($role) )
		$and_where = "( meta_value LIKE '%" . join("%' OR meta_value LIKE '%", $role) . "%' )";
	else
		$and_where = "meta_value LIKE '%$role%'";
	$bb_last_countable_query = "SELECT user_id FROM $bbdb->usermeta WHERE meta_key = '$key' AND $and_where ORDER BY user_id $sort LIMIT $limit";

	if ( $ids = (array) $bbdb->get_col( $bb_last_countable_query ) )
		bb_cache_users( $ids );
	return $ids;
}

function bb_user_row( $user_id, $role = '', $email = false ) {
	$user = bb_get_user( $user_id );
	$r  = "\t<tr id='user-$user->ID'" . get_alt_class("user-$role") . ">\n";
	$r .= "\t\t<td>$user->ID</td>\n";
	$r .= "\t\t<td><a href='" . get_user_profile_link( $user->ID ) . "'>" . get_user_name( $user->ID ) . "</a></td>\n";
	if ( $email ) {
		$email = bb_get_user_email( $user->ID );
		$r .= "\t\t<td><a href='mailto:$email'>$email</a></td>\n";
	}
	$r .= "\t\t<td>" . date( 'Y-m-d H:i:s', bb_offset_time( bb_gmtstrtotime( $user->user_registered ) ) ) . "</td>\n";
	$actions = '';
	if ( bb_current_user_can( 'edit_user', $user_id ) )
		$actions .= "<a href='" . attribute_escape( get_profile_tab_link( $user->ID, 'edit' ) ) . "'>" . __('Edit') . "</a>";
	$r .= "\t\t<td>$actions</td>\n\t</tr>";
	return $r;
}

// BB_User_Search class
// by Mark Jaquith

class BB_User_Search {
	var $results;
	var $search_term;
	var $page;
	var $raw_page;
	var $users_per_page = 50;
	var $first_user;
	var $last_user;
	var $query_limit;
	var $total_users_for_query = 0;
	var $search_errors;

	function BB_User_Search ($search_term = false, $page = 1 ) { // constructor
		$this->search_term = $search_term ? stripslashes($search_term) : false;
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$page = (int) $page;
		$this->page = $page < 2 ? 1 : $page;

		$this->prepare_query();
		$this->query();
		$this->prepare_vars_for_template_usage();
		$this->do_paging();
	}

	function prepare_query() {
		$this->first_user = ($this->page - 1) * $this->users_per_page;
	}

	function query() {
		$users = bb_user_search( array(
				'query' => $this->search_term,
				'user_email' => true,
				'users_per_page' => $this->users_per_page,
				'page' => $this->page
		) );

		if ( is_wp_error($users) )
			$this->search_errors = $users;
		else if ( $users )
			foreach ( (array) $users as $user )
				$this->results[] = $user->ID;

		if ( $this->results )
			$this->total_users_for_query = bb_count_last_query();
		elseif ( !is_wp_error($this->search_errors) )
			$this->search_errors = new WP_Error('no_matching_users_found', __('No matching users were found!'));

		if ( is_wp_error( $this->search_errors ) )
			bb_admin_notice( $this->search_errors );
	}

	function prepare_vars_for_template_usage() {
		$this->search_term = stripslashes($this->search_term); // done with DB, from now on we want slashes gone
	}

	function do_paging() {
		global $bb_current_submenu;
		if ( $this->total_users_for_query > $this->users_per_page ) { // have to page the results
		$pagenow = bb_get_admin_tab_link($bb_current_submenu);
			$this->paging_text = paginate_links( array(
				'total' => ceil($this->total_users_for_query / $this->users_per_page),
				'current' => $this->page,
				'prev_text' => '&laquo; Previous Page',
				'next_text' => 'Next Page &raquo;',
				'base' => $pagenow . ( false === strpos($pagenow, '?') ? '?%_%' : '&amp;%_%' ),
				'format' => 'userspage=%#%',
				'add_args' => array( 'usersearch' => urlencode($this->search_term) )
			) );
		}
	}

	function get_results() {
		return (array) $this->results;
	}

	function page_links() {
		echo $this->paging_text;
	}

	function results_are_paged() {
		if ( $this->paging_text )
			return true;
		return false;
	}

	function is_search() {
		if ( $this->search_term )
			return true;
		return false;
	}

	function display( $show_search = true, $show_email = false ) {
		global $bb_roles;
		$r = '';
		// Make the user objects
		foreach ( $this->get_results() as $user_id ) {
			$tmp_user = new BB_User($user_id);
			$roles = $tmp_user->roles;
			$role = array_shift($roles);
			$roleclasses[$role][$tmp_user->data->user_login] = $tmp_user;
		}

		if ( isset($this->title) )
			$title = $this->title;
		elseif ( $this->is_search() )
			$title = sprintf(__('Users Matching "%s" by Role'), wp_specialchars( $this->search_term ));
		else
			$title = __('User List by Role');
		$r .= "<h2>$title</h2>\n";

		if ( $show_search ) {
			$r .= "<form action='' method='get' id='search'>\n\t<p>";
			$r .= "<label class='hidden' for='usersearch'>" . _e('Search:') . "</label>";
			$r .= "\t\t<input type='text' name='usersearch' id='usersearch' value='" . wp_specialchars( $this->search_term, 1) . "' />\n";
			$r .= "\t\t<input type='submit' value='" . __('Search for users &raquo;') . "' />\n\t</p>\n";
			$r .= "</form>\n\n";
		}

		if ( $this->get_results() ) {
			if ( $this->is_search() )
				$r .= "<p>\n\t<a href='users.php'>" . __('&laquo; Back to All Users') . "</a>\n</p>\n\n";

			$r .= '<h3>' . sprintf(__('%1$s &#8211; %2$s of %3$s shown below'), $this->first_user + 1, min($this->first_user + $this->users_per_page, $this->total_users_for_query), $this->total_users_for_query) . "</h3>\n";

			if ( $this->results_are_paged() )
				$r .= "<div class='user-paging-text'>\n" . $this->paging_text . "</div>\n\n";

			foreach($roleclasses as $role => $roleclass) {
				ksort($roleclass);
				if ( !empty($role) )
					$r .= "<h3>{$bb_roles->role_names[$role]}</h3>\n";
				else
					$r .= "<h3><em>" . __('Users with no role in these forums') . "</h3>\n";
				$r .= "<table class='widefat'>\n";
				$r .= "<thead>\n";
				$r .= "\t<tr>\n";
				$r .= "\t\t<th style='width:10%;'>" . __('ID') . "</th>\n";
				if ( $show_email ) {
					$r .= "\t\t<th style='width:30%;'>" . __('Username') . "</th>\n";
					$r .= "\t\t<th style='width:30%;'>" . __('Email') . "</th>\n";
				} else {
					$r .= "\t\t<th style='width:60%;'>" . __('Username') . "</th>\n";
				}
				$r .= "\t\t<th style='width:20%;'>" . __('Registered Since') . "</th>\n";
				$r .= "\t\t<th style='width:10%;'>" . __('Actions') . "</th>\n";
				$r .= "\t</tr>\n";
				$r .= "</thead>\n\n";

				$r .= "<tbody id='role-$role'>\n";
				foreach ( (array) $roleclass as $user_object )
				$r .= bb_user_row($user_object->ID, $role, $show_email);
				$r .= "</tbody>\n";
				$r .= "</table>\n\n";
			}

		 	if ( $this->results_are_paged() )
				$r .= "<div class='user-paging-text'>\n" . $this->paging_text . "</div>\n\n";
		}
		echo $r;
	}

}

class BB_Users_By_Role extends BB_User_Search {
	var $role = '';
	var $title = '';

	function BB_Users_By_Role($role = '', $page = '') { // constructor
		$this->role = $role ? $role : 'member';
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$this->page = (int) ( '' == $page ) ? 1 : $page;

		$this->prepare_query();
		$this->query();
		$this->do_paging();
	}

	function query() {
		$this->results = bb_get_ids_by_role( $this->role, 0, $this->page, $this->users_per_page );

		if ( $this->results )
			$this->total_users_for_query = bb_count_last_query();
		else
			$this->search_errors = new WP_Error('no_matching_users_found', __('No matching users were found!'));

		if ( is_wp_error( $this->search_errors ) )
			bb_admin_notice( $this->search_errors );
	}

}

/* Forums */

// Expects forum_name, forum_desc to be pre-escaped
function bb_new_forum( $args ) {
	global $bbdb, $bb_cache;
	if ( !bb_current_user_can( 'manage_forums' ) )
		return false;

	$defaults = array( 'forum_name' => '', 'forum_desc' => '', 'forum_parent' => 0, 'forum_order' => false );
	$args = wp_parse_args( $args, $defaults );
	if ( 1 < func_num_args() ) : // For back compat
		$args['forum_name']  = func_get_arg(0);
		$args['forum_desc']  = func_get_arg(1);
		$args['forum_order'] = 2 < func_num_args() ? func_get_arg(2) : 0;
	endif;

	extract($args, EXTR_SKIP);

	if ( !is_numeric($forum_order) )
		$forum_order = (int) $bbdb->get_var("SELECT MAX(forum_order) FROM $bbdb->forums") + 1;

	$forum_order = (int) $forum_order;
	$forum_parent = (int) $forum_parent;

	$forum_name = apply_filters( 'bb_pre_forum_name', stripslashes($forum_name) );
	$forum_desc = apply_filters( 'bb_pre_forum_desc', stripslashes($forum_desc) );
	$forum_name = bb_trim_for_db( $forum_name, 150 );

	if ( strlen($forum_name) < 1 )
		return false;

	$forum_sql = "SELECT forum_slug FROM $bbdb->forums WHERE forum_slug = %s";

	$forum_slug = $_forum_slug = bb_slug_sanitize($forum_name);
	if ( strlen($_forum_slug) < 1 )
		return false;

	while ( is_numeric($forum_slug) || $existing_slug = $bbdb->get_var( $bbdb->prepare( $forum_sql, $forum_slug ) ) )
		$forum_slug = bb_slug_increment($_forum_slug, $existing_slug);

	$bbdb->insert( $bbdb->forums, compact( 'forum_name', 'forum_slug', 'forum_desc', 'forum_parent', 'forum_order' ) );
	$forum_id = $bbdb->insert_id;

	$bb_cache->flush_one( 'forums' );
	return $forum_id;
}

// Expects forum_name, forum_desc to be pre-escaped
function bb_update_forum( $args ) {
	global $bbdb, $bb_cache;
	if ( !bb_current_user_can( 'manage_forums' ) )
		return false;

	$defaults = array( 'forum_id' => 0, 'forum_name' => '', 'forum_desc' => '', 'forum_parent' => 0, 'forum_order' => 0 );
	$args = wp_parse_args( $args, $defaults );
	if ( 1 < func_num_args() ) : // For back compat
		$args['forum_id']    = func_get_arg(0);
		$args['forum_name']  = func_get_arg(1);
		$args['forum_desc']  = 2 < func_num_args() ? func_get_arg(2) : '';
		$args['forum_order'] = 3 < func_num_args() && is_numeric(func_get_arg(3)) ? func_get_arg(3) : 0;
	endif;

	extract($args, EXTR_SKIP);

	if ( !$forum_id = (int) $forum_id )
		return false;
	$forum_order = (int) $forum_order;
	$forum_parent = (int) $forum_parent;

	$forum_name = apply_filters( 'bb_pre_forum_name', stripslashes($forum_name) );
	$forum_desc = apply_filters( 'bb_pre_forum_desc', stripslashes($forum_desc) );
	$forum_name = bb_trim_for_db( $forum_name, 150 );

	if ( strlen($forum_name) < 1 )
		return false;

	$bb_cache->flush_many( 'forum', $forum_id );
	$bb_cache->flush_one( 'forums' );

	return $bbdb->update( $bbdb->forums, compact( 'forum_name', 'forum_desc', 'forum_parent', 'forum_order' ), compact( 'forum_id' ) );
}

// When you delete a forum, you delete *everything*
// NOT bbdb::prepared
function bb_delete_forum( $forum_id ) {
	global $bbdb, $bb_cache;
	if ( !bb_current_user_can( 'delete_forum', $forum_id ) )
		return false;
	if ( !$forum_id = (int) $forum_id )
		return false;

	if ( !$forum = get_forum( $forum_id ) )
		return false;

	if ( $topic_ids = $bbdb->get_col( $bbdb->prepare( "SELECT topic_id FROM $bbdb->topics WHERE forum_id = %d", $forum_id ) ) ) {
		$_topic_ids = join(',', array_map('intval', $topic_ids));
		$bbdb->query("DELETE FROM $bbdb->posts WHERE topic_id IN ($_topic_ids) AND topic_id != 0");
		$bbdb->query("DELETE FROM $bbdb->topicmeta WHERE topic_id IN ($_topic_ids) AND topic_id != 0");
		$bbdb->query( $bbdb->prepare( "DELETE FROM $bbdb->topics WHERE forum_id = %d", $forum_id ) );
	}
	
	$bbdb->update( $bbdb->forums, array( 'forum_parent' => $forum->forum_parent ), array( 'forum_parent' => $forum_id ) );

	$return = $bbdb->query( $bbdb->prepare( "DELETE FROM $bbdb->forums WHERE forum_id = %d", $forum_id ) );

	if ( $topic_ids )
		foreach ( $topic_ids as $topic_id ) {
			$bb_cache->flush_one( 'topic', $topic_id );
			$bb_cache->flush_many( 'thread', $topic_id );
		}

	$bb_cache->flush_many( 'forum', $forum_id );
	$bb_cache->flush_one( 'forums' );
	return $return;
}

function bb_forum_row( $forum_id = 0, $echo = true, $close = false ) {
	global $forum, $forums_count;
	if ( $forum_id )
		$_forum = get_forum( $forum_id );
	else
		$_forum =& $forum;

	if ( !$_forum )
		return;

	$r  = '';
	if ( $close )
		$r .= "\t<li id='forum-$_forum->forum_id'" . get_alt_class( 'forum', 'forum clear list-block' ) . ">\n";
	$r .= "\t\t<div class='list-block posrel'>\n";
	$r .= "\t\t\t<div class='alignright'>\n";
	if ( bb_current_user_can( 'manage_forums' ) )
		$r .= "\t\t\t\t<a class='edit' href='" . attribute_escape( bb_get_option('uri') . "bb-admin/content-forums.php?action=edit&id=$_forum->forum_id" ) . "'>" . __('Edit') . "</a>\n";
	if ( bb_current_user_can( 'delete_forum', $_forum->forum_id ) && 1 < $forums_count )
		$r .= "\t\t\t\t<a class='delete' href='" . attribute_escape( bb_get_option('uri') . "bb-admin/content-forums.php?action=delete&id=$_forum->forum_id" ) . "'>" . __('Delete') . "</a>\n";
	$r .= "\t\t\t</div>\n";
	$r .= "\t\t\t" . get_forum_name( $_forum->forum_id ) . ' &#8212; ' . get_forum_description( $_forum->forum_id ) . "\n\t\t</div>\n";
	if ( $close )
		$r .= "\t</li>\n";

	if ( $echo )
		echo $r;
	return $r;
}

function bb_forum_form( $forum_id = 0 ) {
	$forum_id = (int) $forum_id;
	if ( $forum_id && !get_forum( $forum_id ) )
		return;
	$action = $forum_id ? 'update' : 'add';
?>
<form method="post" id="<?php echo $action; ?>-forum" action="<?php bb_option('uri'); ?>bb-admin/bb-forum.php">
	<fieldset>
	<table><col /><col style="width: 80%" />
		<tr><th scope="row"><label for="forum-name"><?php _e('Forum Name:'); ?></label></th>
			<td><input type="text" name="forum_name" id="forum-name" value="<?php if ( $forum_id ) echo attribute_escape( get_forum_name( $forum_id ) ); ?>" tabindex="10" class="widefat" /></td>
		</tr>
		<tr><th scope="row"><label for="forum-desc"><?php _e('Forum Description:'); ?></label></th>
			<td><input type="text" name="forum_desc" id="forum-desc" value="<?php if ( $forum_id ) echo attribute_escape( get_forum_description( $forum_id ) ); ?>" tabindex="11" class="widefat" /></td>
		</tr>
		<tr id="forum-parent-row"><th scope="row"><label for="forum_parent"><?php _e('Forum Parent:'); ?></label></th>
			<td><?php bb_forum_dropdown( array('cut_branch' => $forum_id, 'id' => 'forum_parent', 'none' => true, 'selected' => $forum_id ? get_forum_parent( $forum_id ) : 0) ); ?></td>
		</tr>
		<tr id="forum-position-row"><th scope="row"><label for="forum-order"><?php _e('Position:'); ?></label></th>
			<td><input type="text" name="forum_order" id="forum-order" value="<?php if ( $forum_id ) echo get_forum_position( $forum_id ); ?>" tabindex="12" maxlength="10" class="widefat" /></td>
		</tr>
	</table>
	<p class="submit">
<?php if ( $forum_id ) : ?>
		<input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>" />
<?php endif; ?>
		<?php bb_nonce_field( 'order-forums', 'order-nonce' ); ?>
		<?php bb_nonce_field( "$action-forum" ); ?>
		<input type="hidden" name="action" value="<?php echo $action; ?>" />
		<input name="Submit" type="submit" value="<?php if ( $forum_id ) _e('Update Forum &#187;'); else _e('Add Forum &#187;'); ?>" tabindex="13" />
	</p>
	</fieldset> 
</form>
<?php
}

class BB_Walker_ForumAdminlistitems extends BB_Walker {
	var $tree_type = 'forum';
	var $db_fields = array ('parent' => 'forum_parent', 'id' => 'forum_id'); //TODO: decouple this
	
	function start_lvl($output, $depth) {
		$indent = str_repeat("\t", $depth) . '    ';
		$output .= $indent . "<ul id='forum-root-$this->forum_id' class='list-block holder'>\n";
		return $output;
	}
	
	function end_lvl($output, $depth) {
		$indent = str_repeat("\t", $depth) . '    ';
		$output .= $indent . "</ul>\n";
		return $output;
	}
	
	function start_el($output, $forum, $depth) {
		$this->forum_id = $forum->forum_id;
		$indent = str_repeat("\t", $depth + 1);
		$output .= $indent . "<li id='forum-$this->forum_id'" . get_alt_class( 'forum', 'forum clear list-block' ) . ">\n";

		return $output;
	}
	
	function end_el($output, $forum, $depth) {
		$indent = str_repeat("\t", $depth + 1);
		$output .= $indent . "</li>\n";
		return $output;
	}
}

/* Tags */

function rename_tag( $tag_id, $tag ) {
	global $bbdb;
	if ( !bb_current_user_can( 'manage_tags' ) )
		return false;

	$tag_id = (int) $tag_id;
	$raw_tag = bb_trim_for_db( $tag, 50 );
	$tag     = tag_sanitize( $tag ); 

	if ( empty( $tag ) )
		return false;
	if ( $bbdb->get_var( $bbdb->prepare( "SELECT tag_id FROM $bbdb->tags WHERE tag = %s AND tag_id <> %d", $tag, $tag_id ) ) )
		return false;

	$old_tag = bb_get_tag( $tag_id );

	if ( $bbdb->update( $bbdb->tags, compact( 'tag', 'raw_tag' ), compact( 'tag_id' ) ) ) {
		do_action('bb_tag_renamed', $tag_id, $old_tag->raw_tag, $raw_tag );
		return bb_get_tag( $tag_id );
	}
	return false;
}

// merge $old_id into $new_id.  MySQL 4.0 can't do IN on tuples!
// NOT bbdb::prepared
function merge_tags( $old_id, $new_id ) {
	global $bbdb;
	if ( !bb_current_user_can( 'manage_tags' ) )
		return false;

	$old_id = (int) $old_id;
	$new_id = (int) $new_id;

	if ( $old_id == $new_id )
		return false;

	do_action('bb_pre_merge_tags', $old_id, $new_id);

	$tagged_del = 0;
	if ( $old_topic_ids = (array) $bbdb->get_col( $bbdb->prepare( "SELECT topic_id FROM $bbdb->tagged WHERE tag_id = %d", $old_id ) ) ) {
		$old_topic_ids = join(',', array_map('intval', $old_topic_ids));
		$shared_topics = (array) $bbdb->get_results( "SELECT user_id, topic_id FROM $bbdb->tagged WHERE tag_id = '$new_id' AND topic_id IN ($old_topic_ids)" );
		foreach ( $shared_topics as $st ) {
			$tagged_del += $bbdb->query( $bbdb->prepare(
				"DELETE FROM $bbdb->tagged WHERE tag_id = %d AND user_id = %d AND topic_id = %d",
				$old_id, $st->user_id, $st->topic_id
			) );
			$count = (int) $bbdb->get_var( $bbdb->prepare(
				"SELECT COUNT(DISTINCT tag_id) FROM $bbdb->tagged WHERE topic_id = %d GROUP BY topic_id",
				$st->topic_id
			) );
			$bbdb->update( $bbdb->topics, array( 'tag_count' => $count ), array( 'topic_id' => $st->topic_id ) );
		}
	}

	if ( $diff_count = $bbdb->update( $bbdb->tagged, array( 'tag_id' => $new_id ), array( 'tag_id' => $old_id ) ) ) {
		$count = (int) $bbdb->get_var( $bbdb->prepare( "SELECT COUNT(DISTINCT topic_id) FROM $bbdb->tagged WHERE tag_id = %d GROUP BY tag_id", $new_id ) );
		$bbdb->update( $bbdb->tags, array( 'tag_count' => $count ), array( 'tag_id' => $new_id ) );
	}

	// return values and destroy the old tag
	return array( 'destroyed' => bb_destroy_tag( $old_id, false ), 'old_count' => $diff_count + $tagged_del, 'diff_count' => $diff_count );
}

/* Topics */

function bb_move_forum_topics( $from_forum_id, $to_forum_id ) {
	global $bb_cache, $bbdb;
	
	$from_forum_id = (int) $from_forum_id ;
	$to_forum_id = (int) $to_forum_id;
	
	add_filter('get_forum_where', 'no_where'); // Just in case
	
	$from_forum = get_forum( $from_forum_id );
	if ( !$to_forum = get_forum( $to_forum_id ) )
		return false;

	$bb_cache->flush_many( 'forum', $from_forum_id );
	$bb_cache->flush_many( 'forum', $to_forum_id );
	
	$posts = $to_forum->posts + ( $from_forum ? $from_forum->posts : 0 );
	$topics = $to_forum->topics + ( $from_forum ? $from_forum->topics : 0 );
	
	$bbdb->update( $bbdb->forums, compact( 'topics', 'posts' ), array( 'forum_id' => $to_forum_id ) );
	$bbdb->update( $bbdb->forums, array( 'topics' => 0, 'posts' => 0 ), array( 'forum_id' => $from_forum_id ) );
	$bbdb->update( $bbdb->posts, array( 'forum_id' => $to_forum_id ), array( 'forum_id' => $from_forum_id ) );
	$topic_ids = $bbdb->get_col( $bbdb->prepare( "SELECT topic_id FROM $bbdb->topics WHERE forum_id = %d", $from_forum_id ) );
	$return = $bbdb->update( $bbdb->topics, array( 'forum_id' => $to_forum_id ), array( 'forum_id' => $from_forum_id ) );
	if ( $topic_ids )
		foreach ( $topic_ids as $topic_id ) {
			$bb_cache->flush_one( 'topic', $topic_id );
			$bb_cache->flush_many( 'thread', $topic_id );
		}
	$bb_cache->flush_one( 'forum', $to_forum_id );
	$bb_cache->flush_many( 'forum', $from_forum_id );
	return $return;
}

/* Posts */

function bb_admin_list_posts() {
	global $bb_posts, $bb_post;
	if ( $bb_posts ) {
?>
<ol id="the-list">
<?php foreach ( $bb_posts as $bb_post ) : ?>
	<li<?php alt_class('post'); ?>>
		<div class="threadauthor">
			<p>
				<strong><?php post_author_link(); ?></strong><br />
				<small><?php post_author_type(); ?></small>
			</p>
		</div>
		<div class="threadpost">
			<div class="post"><?php post_text(); ?></div>
			<div class="poststuff">
				<?php printf(__('Posted: %1$s in <a href="%2$s">%3$s</a>'), bb_get_post_time(), get_topic_link( $bb_post->topic_id ), get_topic_title( $bb_post->topic_id ));?> IP: <?php post_ip_link(); ?> <?php post_edit_link(); ?> <?php post_delete_link();?>
			</div>
		</div>
	</li>
<?php endforeach; ?>
</ol>
<?php
	}
}

/* Recounts */

function bb_recount_list() {
	global $recount_list;
	$recount_list = array();
	$recount_list[5] = array('topic-posts', __('Count posts of every topic'));
	$recount_list[10] = array('topic-deleted-posts', __('Count deleted posts on every topic'));
	$recount_list[15] = array('forums', __('Count topics and posts in every forum (relies on the above)'));
	$recount_list[20] = array('topics-replied', __('Count topics to which each user has replied'));
	$recount_list[25] = array('topic-tag-count', __('Count tags for every topic'));
	$recount_list[30] = array('tags-tag-count', __('Count topics for every tag'));
	$recount_list[35] = array('zap-tags', __('DELETE tags with no topics.  Only functions if the above checked'));
	$recount_list[40] = array('clean-favorites', __('REMOVE deleted topics from users\' favorites'));

	do_action('bb_recount_list');
	ksort($recount_list);
	return $recount_list;
}

/* Plugins */

function bb_get_plugins_callback( $type = 'normal', $path, $filename ) {
	if ( '.php' != substr($filename, -4) )
		return false;
	
	switch ($type) {
		case 'all':
			// Catch, but do nothing
			break;
		case 'autoload':
			if ( '_' != substr($filename, 0, 1) )
				return false;
			break;
		case 'normal':
		default:
			if ( '_' == substr($filename, 0, 1) )
				return false;
			break;
	}
	
	return bb_get_plugin_data( $path );
}

function bb_get_plugins($location = 'all', $type = 'normal') {
	switch ($location) {
		case 'core':
			$directories = array(BB_CORE_PLUGIN_DIR);
			break;
		case 'user':
			$directories = array(BB_PLUGIN_DIR);
			break;
		case 'all':
		default:
			$directories = array(BB_CORE_PLUGIN_DIR, BB_PLUGIN_DIR);
			break;
	}
	unset($location);
	
	$plugin_arrays = array();
	foreach ($directories as $directory) {
		$dir_map = new BB_Dir_Map(
			$directory,
			array(
				'callback' => 'bb_get_plugins_callback',
				'callback_args' => array($type),
				'recurse' => 1
			)
		);
		$dir_plugins = $dir_map->get_results();
		$dir_plugins = is_wp_error($dir_plugins) ? array() : $dir_plugins;
		$plugin_arrays[] = $dir_plugins;
		unset($dir_map, $dir_plugins);
	}
	
	$plugins = array();
	foreach ($plugin_arrays as $plugin_array) {
		$plugins = array_merge($plugins, $plugin_array);
	}
	
	$adjusted_plugins = array();
	foreach ($plugins as $plugin => $plugin_data) {
		$adjusted_plugins[$plugin_data['location'] . '#' . $plugin] = $plugin_data;
	}
	
	return $adjusted_plugins;
}

// Output sanitized for display
function bb_get_plugin_data($plugin_file) {
	if ( strpos($plugin_file, '#') !== false ) {
		$plugin_file = str_replace(
			array('core#', 'user#'),
			array(BB_CORE_PLUGIN_DIR, BB_PLUGIN_DIR),
			$plugin_file
		);
	}
	$plugin_code = implode('', file($plugin_file));
	// Grab just the first commented area from the file
	if ( !preg_match( '|/\*(.*)\*/|msU', $plugin_code, $plugin_block ) )
		return false;
	$plugin_data = trim( $plugin_block[1] );
	if ( !preg_match("|Plugin Name:(.*)|i", $plugin_data, $plugin_name) )
		return false;
	preg_match("|Plugin URI:(.*)|i", $plugin_data, $plugin_uri);
	preg_match("|Description:(.*)|i", $plugin_data, $description);
	preg_match("|Author:(.*)|i", $plugin_data, $author_name);
	preg_match("|Author URI:(.*)|i", $plugin_data, $author_uri);
	if ( preg_match("|Requires at least:(.*)|i", $plugin_data, $requires) )
		$requires = wp_specialchars( trim($requires[1]) );
	else
		$requires = '';
	if ( preg_match("|Tested up to:(.*)|i", $plugin_data, $tested) )
		$tested = wp_specialchars( trim($tested[1]) );
	else
		$tested = '';
	if ( preg_match("|Version:(.*)|i", $plugin_data, $version) )
		$version = wp_specialchars( trim($version[1]) );
	else
		$version = '';

	$plugin_name = wp_specialchars( trim($plugin_name[1]) );
	$plugin_uri = clean_url( trim($plugin_uri[1]) );
	$author_name = wp_specialchars( trim($author_name[1]) );
	$author_uri = clean_url( trim($author_uri[1]) );

	$description = trim($description[1]);
	$description = bb_encode_bad( $description );
	$description = bb_code_trick( $description );
	$description = force_balance_tags( $description );
	$description = bb_filter_kses( $description );
	$description = bb_autop( $description );

	$plugin_file = str_replace( '\\', '/', $plugin_file );
	$core_dir    = str_replace( '\\', '/', BB_CORE_PLUGIN_DIR );

	if (substr($plugin_file, 0, strlen($core_dir)) == $core_dir) {
		$location = 'core';
	} else {
		$location = 'user';
	}

	$r = array(
		'location' => $location,
		'name' => $plugin_name,
		'uri' => $plugin_uri,
		'description' => $description,
		'author' => $author_name,
		'author_uri' => $author_uri,
		'requires' => $requires,
		'tested' => $tested,
		'version' => $version
	);

	$r['plugin_link'] = ( $plugin_uri ) ?
		"<a href='$plugin_uri' title='" . attribute_escape( __('Visit plugin homepage') ) . "'>$plugin_name</a>" :
		$plugin_name;
	$r['author_link'] = ( $author_name && $author_uri ) ?
		"<a href='$author_uri' title='" . attribute_escape( __('Visit author homepage') ) . "'>$author_name</a>" :
		$author_name;

	return $r;
}

/* Themes */

// Output sanitized for display
function bb_get_theme_data( $theme_file ) {
	if ( strpos($theme_file, '#') !== false )
		$theme_file = bb_get_theme_directory( $theme_file ) . 'style.css';
	$theme_code = implode( '', file( $theme_file ) );
	$theme_code = str_replace ( '\r', '\n', $theme_code );
	// Grab just the first commented area from the file
	preg_match( '|/\*(.*)\*/|msU', $theme_code, $theme_block );
	$theme_data = trim( $theme_block[1] );
	preg_match( '|Theme Name:(.*)|i', $theme_data, $theme_name );
	preg_match( '|Theme URI:(.*)|i', $theme_data, $theme_uri );
	preg_match( '|Description:(.*)|i', $theme_data, $description );
	preg_match( '|Author:(.*)|i', $theme_data, $author_name );
	preg_match( '|Author URI:(.*)|i', $theme_data, $author_uri );
	preg_match( '|Ported By:(.*)|i', $theme_data, $porter_name );
	preg_match( '|Porter URI:(.*)|i', $theme_data, $porter_uri );
//	preg_match( '|Template:(.*)|i', $theme_data, $template );
	if ( preg_match( '|Version:(.*)|i', $theme_data, $version ) )
		$version = wp_specialchars( trim( $version[1] ) );
	else
		$version ='';
	if ( preg_match('|Status:(.*)|i', $theme_data, $status) )
		$status = wp_specialchars( trim($status[1]) );
	else
		$status = 'publish';

	$description = trim($description[1]);
	$description = bb_encode_bad( $description );
	$description = bb_code_trick( $description );
	$description = force_balance_tags( $description );
	$description = bb_filter_kses( $description );
	$description = bb_autop( $description );

	$name = $theme_name[1];
	$name = wp_specialchars( trim($name) );
	$theme = $name;

	if ( '' == $author_uri[1] ) {
		$author = wp_specialchars( trim($author_name[1]) );
	} else {
		$author = '<a href="' . clean_url( trim($author_uri[1]) ) . '" title="' . attribute_escape( __('Visit author homepage') ) . '">' . wp_specialchars( trim($author_name[1]) ) . '</a>';
	}

	if ( '' == $porter_uri[1] ) {
		$porter = wp_specialchars( trim($porter_name[1]) );
	} else {
		$porter = '<a href="' . clean_url( trim($porter_uri[1]) ) . '" title="' . attribute_escape( __('Visit porter homepage') ) . '">' . wp_specialchars( trim($porter_name[1]) ) . '</a>';
	}

	return array(
		'Name' => $name,
		'Title' => $theme,
		'Description' => $description,
		'Author' => $author,
		'Porter' => $porter,
		'Version' => $version,
//		'Template' => $template[1],
		'Status' => $status,
		'URI' => clean_url( $theme_uri[1] )
	);
}

?>
