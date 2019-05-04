<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (! nv_function_exists('nv_news_block_forum_archives')) {
    function nv_block_config_forum_archives($module, $data_block, $lang_block)
    {
        $html = '<tr>';
        $html .= '	<td>' . $lang_block['numrow'] . '</td>';
        $html .= '	<td><input type="text" name="config_numrow" class="form-control w100" size="5" value="' . $data_block['numrow'] . '"/></td>';
        $html .= '</tr>';
        return $html;
    }

    function nv_block_config_forum_archives_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        return $return;
    }

    function nv_news_block_forum_archives($block_config)
    {
        global $nv_Cache, $module_array_cat, $module_info, $site_mods, $module_config, $global_config,$db_config, $db;
        $module = $block_config['module'];

        $numrow = (isset($block_config['numrow'])) ? $block_config['numrow'] : 50;

        
       
            $array_block_news = array();

            $db->sqlreset()
                ->select("FROM_UNIXTIME(post_date, '%Y') AS year, FROM_UNIXTIME(post_date, '%m') AS month, thread_id, title, view_count, username ,   last_post_date, last_post_id,  last_post_user_id, last_post_username, prefix_id")
                ->from( $db_config['prefix'] . '_' . $module . '_thread')
              
                ->order('last_post_date DESC')
                ->limit($numrow);
            $result = $db->query($db->sql());
			
			$prevYear = '';
			$prevMonth = '';
			$show_year = '';
			$show_month = '';
			$close_month = '';
			$close_year = '';
			
            while (list($year, $month, $thread_id,$title, $view_count, $username ,  $last_post_date, $last_post_id,  $last_post_user_id, $last_post_username, $prefix_id ) = $result->fetch(3)) {
             
                
                  $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . "&amp;" . NV_OP_VARIABLE . "=threads/" . strtolower( change_alias( $title ) ) . "-" .  $thread_id . $global_config['rewrite_exturl'];
				// Year
				if ( $year <> $prevYear ) {
					$show_year = $year;
					if(!empty($prevYear)){
						$close_year = true;
					}
				}
				else{
					$show_year = '';
					$close_year = false;
				}
				// Month
				if ($year <> $prevYear || $month <> $prevMonth) {
					if ($year == $prevYear) {
						$close_month = true;
					}
					$show_month = $month;
				}
				else{
					$show_month = '';
					$close_month = false;
				}
				
				$prevYear = $year;
				$prevMonth = $month;
				
				$array_block_news[] = array(
					'thread_id' => $thread_id,
					'title' => $title,
					'link' => $link,
					
					'year' => $year,
					'month' => $month,
					'prevYear' => $prevYear,
					'prevMonth' => $prevMonth,
					'show_year' => $show_year,
					'show_month' => $show_month,
					'close_year' => $close_year,
					'close_month' => $close_month
				);
            }
           

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/forum/block_forum_archives.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        $xtpl = new XTemplate('block_forum_archives.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/forum/');
        $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);

        foreach ($array_block_news as $array_news) {
			if( $array_news['close_month'] OR $array_news['close_year'] ){
				$xtpl->parse('main.post.close_month');
			}
			
			if(!empty($array_news['show_month'])){
				$xtpl->assign('show_month', $array_news['show_month']);
				$xtpl->parse('main.post.month');
			}
			
			if( $array_news['close_year'] ){
				$xtpl->parse('main.post.close_year');
			}
			
			if(!empty($array_news['show_year'])){
				$xtpl->assign('show_year', $array_news['show_year']);
				$xtpl->parse('main.post.year');
				$xtpl->parse('main.post.open_month');
			}
				$xtpl->assign('blocknews', $array_news);
				$xtpl->parse('main.post');
			}
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
   
        $content = nv_news_block_forum_archives($block_config);
   
}