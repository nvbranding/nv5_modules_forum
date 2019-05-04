<?php

if( ! defined( 'NV_IS_MOD_FORUM' ) )
{
	die( 'Stop!!!' );
}

$url = array();
$cacheFile = NV_LANG_DATA . '_sitemap_' . NV_CACHE_PREFIX . '.cache';
$pa = NV_CURRENTTIME - 7200;

if( ( $cache = $nv_Cache->getItem( $module_name, $cacheFile ) ) != false and filemtime( NV_ROOTDIR . '/' . NV_CACHEDIR . '/' . $module_name . '/' . $cacheFile ) >= $pa )
{
	$url = unserialize( $cache );
}
else
{
	$db_slave->sqlreset()->select( 'thread_id, node_id, title, post_date' )->from( NV_FORUM_GLOBALTABLE . '_thread' )->where( 'discussion_state=\'visible\' AND discussion_open=1' )->order( 'post_date DESC' )->limit( 2000 );
	$result = $db_slave->query( $db_slave->sql() );

	$url = array();

	while( list( $thread_id, $node_id, $title, $post_date ) = $result->fetch( 3 ) )
	{
		$catalias = $forum_node[$node_id]['alias'];
		$url[] = array( 'link' => nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $title ) ) . '-' . $thread_id . $global_config['rewrite_exturl'], true ), 'publtime' => $post_date );
	}

	$cache = serialize( $url );
	$nv_Cache->setItem( $module_name, $cacheFile, $cache );
}

nv_xmlSitemap_generate( $url );
die();
