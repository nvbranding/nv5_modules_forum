<?php
/**
 * @Project NUKEVIET 4.x
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Dec 3, 2010  11:33:22 AM 
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$error = array();
if ( $nv_Request->isset_request( 'submit', 'post' ) )
{
	$arr_config['indexfile'] = $nv_Request->get_title( 'indexfile', 'post', '', 1 );
    $arr_config['type_thread'] = $nv_Request->get_title( 'type_thread', 'post', '', 1 );
    $arr_config['upload_logo'] = $nv_Request->get_title( 'upload_logo', 'post', '', 0 );	
	$arr_config['addlogo'] = $nv_Request->get_int( 'addlogo', 'post', 0 );
	$arr_config['paper_page'] = $nv_Request->get_int( 'paper_page', 'post', 0 );
    $arr_config['paper_post'] = $nv_Request->get_int( 'paper_post', 'post', 0 );
	$arr_config['verify_post'] = $nv_Request->get_int( 'verify_post', 'post', 0 );
    $arr_config['paper_thread'] = $nv_Request->get_int( 'paper_thread', 'post', 0 );
    $arr_config['profile_perpage'] = $nv_Request->get_int( 'profile_perpage', 'post', 0 );
    $arr_config['other_link'] = $nv_Request->get_int( 'other_link', 'post', 0 );
    $arr_config['time_edit_user'] = $nv_Request->get_int( 'time_edit_user', 'post', 0 );
    $arr_config['show_smile'] = $nv_Request->get_int( 'show_smile', 'post', 0 );
    $arr_config['thumb_width'] = $nv_Request->get_int( 'thumb_width', 'post', 0 );
    $arr_config['thumb_height'] = $nv_Request->get_int( 'thumb_height', 'post', 0 );
    $arr_config['img_template_width'] = $nv_Request->get_int( 'img_template_width', 'post', 0 );
    $arr_config['maxupload'] = $nv_Request->get_int( 'maxupload', 'post', 0 );
	$arr_config['maxupload'] = min( nv_converttoBytes( ini_get( 'upload_max_filesize' ) ), nv_converttoBytes( ini_get( 'post_max_size' ) ), $arr_config['maxupload']);

	$arr_config['verify_post'] = empty( $arr_config['verify_post'] ) ? 3 : $arr_config['verify_post'];	
	
	if( ! nv_is_url( $arr_config['upload_logo'] ) and file_exists( NV_DOCUMENT_ROOT . $arr_config['upload_logo'] ) )
	{
		$lu = strlen( NV_BASE_SITEURL );
		$arr_config['upload_logo'] = substr( $arr_config['upload_logo'], $lu );
	}
	elseif( ! nv_is_url( $arr_config['upload_logo'] ) )
	{
		$arr_config['upload_logo'] = $global_config['site_logo'];
	}			
	
	
	if ( $arr_config['addlogo'] == 1 and empty($arr_config['upload_logo']) ) 
	{    
		$error[] = "Lỗi: không tồn tại file logo đóng dấu ảnh";
	}
	if ( $arr_config['paper_post'] < 5 )
	{    
		$error[] = "Lỗi: Số bài trả lời trên cùng một trang không nhỏ hơn 5";
	}
	if ( $arr_config['paper_page'] < 5 )
	{    
		$error[] = "Lỗi: Số bài viết trên cùng một trang không nhỏ hơn 5";
	}
	if ( $arr_config['paper_thread'] < 10 )
	{    
		$error[] = "Lỗi: Số chủ đề trên cùng một trang không nhỏ hơn 10";
	}
	if ( $arr_config['profile_perpage'] < 10 )
	{    
		$error[] = "Lỗi: Số bình luận trên cùng một trang không nhỏ hơn 10";
	}
	if ( $arr_config['other_link'] < 2 )
	{    
		$error[] = "Lỗi: Số liên kết chủ đề trên cùng một trang không nhỏ hơn 2";
	}
	if ( $arr_config['time_edit_user'] == 0)
	{    
		$error[] = "Lỗi: Thời hạn được phép sửa bài viết phải lớn hơn 0";
	}
	if ( $arr_config['time_edit_user'] == 0)
	{    
		$error[] = "Lỗi: Thời hạn được phép sửa bài viết phải lớn hơn 0";
	}
	if ( $arr_config['thumb_width'] < 150)
	{    
		$error[] = "Lỗi: Ảnh thumb phải có chiều rộng lớn hơn 150px";
	}
	if ( $arr_config['thumb_height'] < 150)
	{    
		$error[] = "Lỗi: Ảnh thumb phải có chiều cao lớn hơn 150px";
	}
	if ( $arr_config['img_template_width'] < 500)
	{    
		$error[] = "Lỗi: Ảnh hiển thị trên bài viết phải có chiều rộng lớn hơn 500px";
	}

    if ( empty( $error ) )
    {
		
        foreach ( $arr_config as $config_name => $config_value )
        {
            
            $query = "REPLACE INTO " . NV_PREFIXLANG . "_" . $module_data . "_config VALUES (" . $db->quote( $config_name ) . "," . $db->quote( $config_value ) . ")";
            $db->query( $query );
        }
        $forum->del_file_cache( 'setting' );
        $forum->del_file_cache( 'setting2' );
        
        Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
        die();
    }
}

$arr_config['addlogo'] = ! empty( $arr_config['addlogo'] ) ? " checked=\"checked\"" : "";
$arr_config['show_smile'] = ! empty( $arr_config['show_smile'] ) ? " checked=\"checked\"" : "";

$upload_logo = ( isset( $arr_config['upload_logo'] ) ) ? $arr_config['upload_logo'] : $global_config['site_logo'];
$upload_logo = ( ! nv_is_url( $upload_logo ) ) ? NV_BASE_SITEURL . $upload_logo : $upload_logo;

$xtpl = new XTemplate( "config.tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'DATA', $arr_config );
$xtpl->assign( 'upload_logo', $upload_logo );
$xtpl->assign( 'PATH', defined( "NV_IS_SPADMIN" ) ? "" : NV_UPLOADS_DIR . '/' . $module_name );
$xtpl->assign( 'CURRENTPATH', defined( "NV_IS_SPADMIN" ) ? "images" : NV_UPLOADS_DIR . '/' . $module_name );

foreach( $array_viewcat_full as $key => $val )
{
	$xtpl->assign( 'INDEXFILE', array( "key" => $key, "title" => $val, "selected" => $key == $arr_config['indexfile'] ? " selected=\"selected\"" : "" ) );
	$xtpl->parse( 'main.indexfile' );
}

foreach( $array_type_thread as $key => $val )
{
	$xtpl->assign( 'TYPE', array( "key" => $key, "title" => $val, "selected" => $key == $arr_config['type_thread'] ? " selected=\"selected\"" : "" ) );
	$xtpl->parse( 'main.type_thread' );
}



$sys_max_size = min( nv_converttoBytes( ini_get( 'upload_max_filesize' ) ), nv_converttoBytes( ini_get( 'post_max_size' ) ) );
$p_size = $sys_max_size / 100;

$xtpl->assign( 'SYS_MAX_SIZE', nv_convertfromBytes( $sys_max_size ) );

$config_maxupload = min( nv_converttoBytes( ini_get( 'upload_max_filesize' ) ), nv_converttoBytes( ini_get( 'post_max_size' ) ), $arr_config['maxupload']);
for ( $index = 100; $index > 0; --$index )
{
    $size1 = floor( $index * $p_size );
	
	$xtpl->assign( 'SIZE1', array(
		'key' => $size1,
		'title' => nv_convertfromBytes( $size1 ),
		'selected' => ( $config_maxupload == $size1 ) ? " selected=\"selected\"" : ""
	) );
	
	$xtpl->parse( 'main.size1' );
}

if ( !empty( $error ) )
{
    $xtpl->assign( 'ERROR', implode('<br />',$error) );
    $xtpl->parse( 'main.error' );
}
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

$page_title = $lang_module['config'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';