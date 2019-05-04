/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 26 May 2014 16:02:36 GMT
 */

CKEDITOR.editorConfig = function( config ) {
    config.language = nv_lang_data;
    config.allowedContent = true;
    config.extraPlugins = 'codesnippet';
    config.entities = false;
    config.codeSnippet_theme = 'github';
    //config.removeButtons = 'Source';
	config.smiley_path = nv_base_siteurl + 'themes/'+ site_theme +'/images/forum/meep/';
	config.smiley_images = [	 
		'grin.png', 'smile.png','heart_eyes.png', 'angel.png', 'tears.png', 'thinking.png', 'laughing.png', 'laughing_big.png', 'loud.png', 'sad_weeping.png'
	];
	config.removePlugins = 'autosave';
	config.enterMode = 'br';
	// Default setting.
    config.toolbarGroups = [
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'forms' },
        { name: 'links' },
        { name: 'insert' },
        { name: 'tools' },
        { name: 'others' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'styles' },
        { name: 'colors' },
		{ name: 'document',    groups: [ 'mode', 'document', 'doctools' ] }, '/',
        
    ];
};