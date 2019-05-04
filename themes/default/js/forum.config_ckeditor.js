/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 26 May 2014 16:02:36 GMT
 */

CKEDITOR.editorConfig = function( config ) {
    config.language = nv_lang_data;
	config.enterMode = CKEDITOR.ENTER_P;
    config.allowedContent = true;
    config.extraPlugins = 'codesnippet';
    config.entities = false;
    config.codeSnippet_theme = 'github';
    config.removeButtons = 'HorizontalRule';
	config.removeButtons = 'Source';
	config.smiley_path = nv_base_siteurl + 'themes/'+ site_theme +'/images/forum/meep/';
	config.smiley_images = ['1.gif', '2.gif', '5.gif','6.gif','7.gif','8.gif','9.gif','10.gif','11.gif','12.gif','13.gif','14.gif','15.gif','16.gif','18.gif','19.gif','20.gif','21.gif','22.gif','23.gif','24.gif','25.gif','26.gif','27.gif','28.gif','29.gif','laluot_01.gif','laluot_02.gif','laluot_03.gif','laluot_05.gif','laluot_06.gif','laluot_07.gif','laluot_08.gif','laluot_09.gif','laluot_10.gif','laluot_11.gif','laluot_12.gif','laluot_13.gif','laluot_15.gif','laluot_16.gif','laluot_17.gif','laluot_18.gif','laluot_19.gif','laluot_20.gif','laluot_21.gif','laluot_22.gif','laluot_23.gif','laluot_24.gif','laluot_25.gif','laluot_26.gif','laluot_27.gif','laluot_28.gif','30.gif','31.gif','32.gif','33.gif','34.gif','35.gif','36.gif','37.gif','38.gif','39.gif','40.gif','41.gif','42.gif','43.gif','44.gif','45.gif','46.gif','47.gif','48.gif','49.gif','50.gif','51.gif','52.gif','53.gif','54.gif','55.gif','56.gif','57.gif','58.gif','59.gif','60.gif','61.gif','62.gif','63.gif','64.gif','65.gif','66.gif','69.gif','70.gif','71.gif','72.gif','grin.png', 'smile.png','heart_eyes.png', 'angel.png', 'tears.png', 'thinking.png', 'laughing.png', 'laughing_big.png', 'loud.png', 'sad_weeping.png'
	];
	config.removePlugins = 'autosave,templates,iframe,pastefromword,placeholder,pagebreak,div';
	config.contentsCss = nv_base_siteurl + 'themes/'+ site_theme +'/css/forum.config_ckeditor.css';
	// Default setting.
    config.toolbarGroups = [
        { name: 'styles',   groups: [ 'Styles','FontSize' ] },
        { name: 'colors' },
        { name: 'links' },
        { name: 'clipboard',   groups: [ 'undo' ] },
        { name: 'insert' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'forms' },
        { name: 'document', groups: [ 'mode', 'doctools' ] }, '/',
        
    ];
};