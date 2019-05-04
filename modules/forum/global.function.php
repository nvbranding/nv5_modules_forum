<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

 
function deleteCache( $part, $module )
{
	//array('cat', setting)

	if( is_array( $part ) )
	{
		foreach( $part as $_part )
		{
			$files = glob( NV_ROOTDIR . '/' . NV_CACHEDIR . '/' . $module . '/' . NV_CACHE_PREFIX . '.' . preg_replace( '/[^A-Z0-9._-]/i', '', $_part ) . '.*.cache' );
			if( $files )
			{
				foreach( $files as $file )
				{
					if( file_exists( $file ) )
					{
						unlink( $file );
					}
				}
			}
		}
	}
	else
	{
		$files = glob( NV_ROOTDIR . '/' . NV_CACHEDIR . '/' . $module . '/' . NV_CACHE_PREFIX . '.' . preg_replace( '/[^A-Z0-9._-]/i', '', $part ) . '.*.cache' );
		if( $files )
		{
			foreach( $files as $file )
			{
				if( file_exists( $file ) )
				{
					unlink( $file );
				}
			}
		}
	}

	return true;
}

function getdbCache( $module, $sql, $part, $key = '' )
{
	global $db, $nv_Cache;

	$data = array();

	if( empty( $sql ) ) return $data;
	$cache_file = NV_CACHE_PREFIX . '.' . $part . '.' . NV_LANG_DATA . '.cache';
	if( ( $cache = $nv_Cache->getItem( $module, $cache_file ) ) != false )
	{
		$data = @unserialize( $cache );
	}
	else
	{
		if( ( $result = $db->query( $sql ) ) !== false )
		{
			$a = 0;
			while( $row = $result->fetch() )
			{
				$key2 = ( ! empty( $key ) and isset( $row[$key] ) ) ? $row[$key] : $a;
				$data[$key2] = $row;
				++$a;
			}
			$result->closeCursor();

			$cache = @serialize( $data );
			$nv_Cache->setItem( $module, $cache_file, $cache );
		}
	}

	return $data;
}

function getForumEditor( $textareaname, $val = '', $width = '100%', $height = '450px' )
{
	global $module_name, $module_data, $admin_info, $client_info, $module_file, $module_info, $global_config;
	$return = '';
	if( ! defined( 'CKEDITOR' ) )
	{
		define( 'CKEDITOR', true );
		$return .= '<script type="text/javascript">var site_theme = "' . $module_info['template'] . '"</script>';
		$return .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js?t=' . $global_config['timestamp'] . '"></script>';
	}
	$return .= '<textarea style="width: ' . $width . '; height:' . $height . ';" id="' . $module_data . '_' . $textareaname . '" name="' . $textareaname . '">' . $val . '</textarea>';
	$return .= "<script type=\"text/javascript\">
            CKEDITOR.replace( '" . $module_data . "_" . $textareaname . "', {
                width: '" . $width . "',
                height: '" . $height . "',
                customConfig : '" . NV_BASE_SITEURL . "themes/" . $module_info['template'] . "/js/forum.config_ckeditor.js'
        });
        </script>";
	return $return;
}

function ForumRandomString( $length = 32 ) 
{
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, $length);
}

function ForumMakeDir( $currentpath )
{
	global $module_upload, $db;
	if( file_exists( NV_UPLOADS_REAL_DIR . '/' . $currentpath ) )
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
	}
	else
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_upload;
		$e = explode( "/", $currentpath );
		if( ! empty( $e ) )
		{
			$cp = "";
			foreach( $e as $p )
			{	
 
				if( $p != '' and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $cp . $p ) )
				{
					
					$mk = nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $cp, $p );
					if( $mk[0] > 0 )
					{
						$upload_real_dir_page = $mk[2];
						//$db->query( "INSERT IGNORE INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)" );
					}
				}
				elseif( ! empty( $p ) )
				{
					$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
				}
				$cp .= $p . '/';
			}
			die('dsadsa');
		}
		$upload_real_dir_page = str_replace( "", "/", $upload_real_dir_page );
	}
	return $upload_real_dir_page;
} 

function ForumCteateThumb( $file, $dir, $newName, $width, $height = 0, $quality = 90, $crop = true )
{
 
	$image = new NukeViet\Files\Image ( $file, NV_MAX_WIDTH, NV_MAX_HEIGHT, $quality );
 
	if( empty( $height ) )
	{
		$image->resizeXY( $width, NV_MAX_HEIGHT );
	}
	else
	{
		if( ( $width * $image->fileinfo['height'] / $image->fileinfo['width'] ) > $height )
		{
			$image->resizeXY( $width, NV_MAX_HEIGHT );
		}
		else
		{
			$image->resizeXY( NV_MAX_WIDTH, $height );
		}
		if( $crop )
		{
			$image->cropFromCenter( $width, $height );
		}
		
	}

	// Kiem tra anh ton tai
//$fileName = basename( $file );
 
	// Luu anh
	$image->save( $dir, $newName );
	$image->close();

	return substr( $image->create_Image_info['src'], strlen( $dir . '/' ) );
}

function convertPostAttachment( $message )
{ 
	//$message = str_replace( array('&#91;', '&#93;'), array('[',']'), $message );
	if( preg_match_all( '/<img[^>]*alt="attach[^>]"*[^>]*>/i', $message, $result ) ) 
	{
		foreach( $result[0] as $img )
		{
			preg_match('/alt="([^"]*)"/i', $img, $matchs );
			
			if ( isset( $matchs[1] ) && preg_match('#attach(Thumb|Full)(\d+)#', $matchs[1], $match ) )
			{ 
				if ($match[1] == 'Full')
				{
					$output = '&#91;ATTACH=full&#93;' . $match[2] . '&#91;/ATTACH&#93;';
				}
				else
				{
					$output = '&#91;ATTACH&#93;' . $match[2] . '&#91;/ATTACH&#93;';
				}
 
				$message = str_replace( $img, $output, $message );			
			}				
		}		
	}
	
	return $message;
	
}

function ConvertAttachmentContent( $message, $attachment_array )
{
	$news_array = array();
	if( preg_match_all('/[ATTACH(.*?)](.+?)[/ATTACH]/is', $message, $matchs ) )
	{
		$i = 0;
		foreach( $matchs[0] as $value )
		{
		   $news_array[$i]['content'] = $value;
		   ++$i;
		}
		$i = 0;
		foreach( $matchs[1] as $value )
		{
			$news_array[$i]['is_full'] = !empty($value) ? true : false;
			 ++$i;
		}
		$i = 0;
		foreach( $matchs[2] as $value )
		{
			$news_array[$i]['attachment_id'] = $value;
			++$i;
		}
		
		
		foreach( $news_array as $content )
		{ 
			if( isset( $attachment_array[$content['attachment_id']] ) )
			{
				$attach = $attachment_array[$content['attachment_id']];
				if( $content['is_full'] )
				{
					$html_image = '<a rel="image" href="'. $attach['image_full'] .'" data-image-count="'. $attach['attachment_id'] .'"><img src="'. $attach['image_full'] .'" class="attach"  /></a>';
				}else
				{
					$html_image = '<img src="'. $attach['image_thumb'] .'" class="attach" data-image-count="'. $attach['attachment_id'] .'" />';
				}
				
				$message = str_replace( $content['content'], $html_image, $message );
			}
			
		}
		   
	}
 
	return $message;
	
}

function GetNodeidInParent( $node_id )
{
	global $forum_node;
	$array_cat = array();
	$array_cat[] = $node_id;
	$subcatid = array_map('intval', explode( ',', $forum_node[$node_id]['subcatid'] ) );
	if( ! empty( $subcatid ) )
	{
		foreach( $subcatid as $id )
		{
			if( $id > 0 )
			{
 
				if( $forum_node[$id]['numsubcat'] == 0 )
				{
					$array_cat[] = $id;
				}
				else
				{
					$array_cat_temp = GetNodeidInParent( $id );
					foreach( $array_cat_temp as $catid_i )
					{
						$array_cat[] = $catid_i;
					}
				}
			}
		}
	}
	return array_unique( $array_cat );
}

 
function GeneratePagePost($title, $base_url, $num_items, $per_page, $on_page, $add_prevnext_text = true, $full_theme = true)
{
    global $lang_global, $global_config;
    $total_pages = ceil($num_items / $per_page);
    if ($total_pages < 2) {
        return '';
    }
    $title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'];
    $page_string = ($on_page == 1) ? '<li class="active"><a href="#">1</a></li>' : '<li><a rel="prev" title="' . $title . ' 1" href="' . $base_url . '">1</a></li>';
    if ($total_pages > 10) {
        $init_page_max = ($total_pages > 3) ? 3 : $total_pages;
        for ($i = 2; $i <= $init_page_max; ++$i) {
            if ($i == $on_page) {
                $page_string .= '<li class="active"><a href="#">' . $i . '</a></li>';
            } else {
                $rel = ($i > $on_page) ? 'next' : 'prev';
                $page_string .= '<li><a rel="' . $rel . '" title="' . $title . ' ' . $i . '" href="' . $base_url . '/page-' . $i . $global_config['rewrite_exturl']. '">' . $i . '</a></li>';
            }
        }
        if ($total_pages > 3) {
            if ($on_page > 1 && $on_page < $total_pages) {
                if ($on_page > 5) {
                    $page_string .= '<li class="disabled"><span>...</span></li>';
                }
                $init_page_min = ($on_page > 4) ? $on_page : 5;
                $init_page_max = ($on_page < $total_pages - 4) ? $on_page : $total_pages - 4;
                for ($i = $init_page_min - 1; $i < $init_page_max + 2; ++$i) {
                    if ($i == $on_page) {
                        $page_string .= '<li class="active"><a href="#">' . $i . '</a></li>';
                    } else {
                        $rel = ($i > $on_page) ? 'next' : 'prev';
                        $page_string .= '<li><a rel="' . $rel . '" title="' . $title . ' ' . $i . '" href="' . $base_url . '/page-' . $i . $global_config['rewrite_exturl'] .'">' . $i . '</a></li>';
                    }
                }
                if ($on_page < $total_pages - 4) {
                    $page_string .= '<li class="disabled"><span>...</span></li>';
                }
            } else {
                $page_string .= '<li class="disabled"><span>...</span></li>';
            }
            for ($i = $total_pages - 2; $i < $total_pages + 1; ++$i) {
                if ($i == $on_page) {
                    $page_string .= '<li class="active"><a href="#">' . $i . '</a></li>';
                } else {
                    $rel = ($i > $on_page) ? 'next' : 'prev';
                    $page_string .= '<li><a rel="' . $rel . '" title="' . $title . ' ' . $i . '" href="' . $base_url . '/page-' . $i . $global_config['rewrite_exturl'] .'">' . $i . '</a></li>';
                }
            }
        }
    } else {
        for ($i = 2; $i < $total_pages + 1; ++$i) {
            if ($i == $on_page) {
                $page_string .= '<li class="active"><a href="#">' . $i . '</a><li>';
            } else {
                $rel = ($i > $on_page) ? 'next' : 'prev';
                $page_string .= '<li><a rel="' . $rel . '" title="' . $title . ' ' . $i . '" href="' . $base_url . '/page-' . $i . $global_config['rewrite_exturl'] .'">' . $i . '</a></li>';
            }
        }
    }
    if ($add_prevnext_text) {
        if ($on_page > 1) {
            $href = ($on_page > 2) ? $base_url . '/page-' . ($on_page - 1) . $global_config['rewrite_exturl'] : $base_url;
            $page_string = '<li><a rel="prev" title="' . $title . ' ' . ($on_page - 1) . '" href="' . $href . '">&laquo;</a></li>' . $page_string;
        } else {
            $page_string = '<li class="disabled"><a href="#">&laquo;</a></li>' . $page_string;
        }
        if ($on_page < $total_pages) {
            $page_string .= '<li><a rel="next" title="' . $title . ' ' . ($on_page + 1) . '" href="' . $base_url . '/page-' . ($on_page + 1) . $global_config['rewrite_exturl'] .'">&raquo;</a></li>';
        } else {
            $page_string .= '<li class="disabled"><a href="#">&raquo;</a></li>';
        }
    }
    if ($full_theme !== true) {
        return $page_string;
    }
    return '<ul class="pagination">' . $page_string . '</ul>';
}

function spamMessageCheck( $message = '', $nodePermissions = array() )
{
	global $global_config;
	if( ! ModelPost_keepOutLinkPost( $nodePermissions ) )
	{

		preg_match_all( "'<a[^>]*>(.*)</a>'siU", $message, $matches );

		if( isset( $matches[0] ) )
		{
			foreach( $matches[0] as $key => $taga )
			{
				preg_match( "/href=\"(.*)\"/", $taga, $match );

				if( isset( $match[1] ) && checkIsUrl( $match[1] ) )
				{
					$host = parse_url( $match[1] );
					if( ! in_array( $host['host'], $global_config['my_domains'] ) )
					{
						if( ModelPost_keepOutTextLinkPost( $nodePermissions ) )
						{
							$message = str_replace( $taga, $matches[1][$key], $message );
						}
						else
						{

							$message = str_replace( $taga, '', $message );

						}

					}

				}

			}
		}

	}

	return $message;
}