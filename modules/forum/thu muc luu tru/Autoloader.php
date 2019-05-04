<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog http://dangdinhtu.com
 * @Developers http://developers.dangdinhtu.com/
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Mon, 20 Oct 2014 14:00:59 GMT
 */

class Forum_Autoloader
{

	private static function scansubdir( $base_dir, $subdir = true )
	{
		$result = array();

		foreach( scandir( $base_dir ) as $file )
		{
			if( $file == '.' || $file == '..' ) continue;
			$dir = $base_dir . '/' . $file;
			if( is_dir( $dir ) )
			{
				$result[] = $dir;
				if( $subdir == true )
				{
					$result = array_merge( $result, Forum_Autoloader::scansubdir( $dir ) );
				}
			}
		}
		return $result;
	}

	private static function Load( $class_name )
	{
		global $module_file;

		$class_name = strtolower( $class_name );

		$directory = array_merge( array( LOAD_PATH ), Forum_Autoloader::scansubdir( LOAD_PATH ) );
		
		foreach( $directory as $current_dir )
		{
			if( file_exists( $current_dir . '/'. $class_name . '.php' ) )
			{
				require $current_dir . '/' . $class_name . '.php';
				return;
			}
		}

	}

	public static function Register()
	{
		if( function_exists( '__autoload' ) )
		{
			spl_autoload_register( '__autoload' );
		}
		return spl_autoload_register( array( 'Forum_Autoloader', 'Load' ) );
	}
}

Forum_Autoloader::Register();