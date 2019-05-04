<!-- BEGIN: main -->
<div class="login-form section visitorPanel">
<form action="{USER_LOGIN}" method="post" class="form-inline login clearfix">
    <fieldset>
        <p>
            <label class="display" for="block_login_iavim">
                {LANG.username} 
            </label>
            <input placeholder="Nhập username" id="block_login_iavim" name="nv_login" value="" type="text" class="form-control txt" maxlength="{NICK_MAXLENGTH}" />
        </p>
        <p style="position:relative;top:-6px">
            <label class="pass display" for="block_password_iavim">
                {LANG.password} 
            </label>
            <input placeholder="Nhập password" id="block_password_iavim" type="password" name="nv_password" value="" class="form-control txt" maxlength="{PASS_MAXLENGTH}" />
        </p><!-- BEGIN: captcha -->
        <p>
            <label for="block_vimg">
                {LANG.securitycode} 
            </label>
            <img id="block_vimg" src="{NV_BASE_SITEURL}index.php?scaptcha=captcha" width="{GFX_WIDTH}" height="{GFX_HEIGHT}" alt="{N_CAPTCHA}" /><img src="{CAPTCHA_REFR_SRC}" class="refesh" alt="{CAPTCHA_REFRESH}" onclick="nv_change_captcha('block_vimg','block_seccode_iavim');"/>
            <label for="block_seccode_iavim">
                {LANG.securitycode} 
            </label>
            <input id="block_seccode_iavim" name="nv_seccode" type="text" class="form-control txt" maxlength="{GFX_MAXLENGTH}" />
        </p><!-- END: captcha -->
		<div class="clear"></div>
        <div style="padding-top: 10px;" class="clearfix">
            <div class="submit">
                <input name="nv_redirect" value="{REDIRECT}" type="hidden" />
                <input class="button2" type="submit" value="{LANG.loginsubmit}" />
            </div>
			<a class="forgot fl" title="{LANG.lostpass}" href="{USER_LOSTPASS}">{LANG.lostpass}? </a>
			
			<a class="fl" title="{LANG.register}" href="{USER_REGISTER}">&nbsp;&nbsp;&nbsp;&nbsp; {LANG.register}</a>
			
        </div>
        <!-- BEGIN: openid -->
        <div style="padding-top:10px;">
            <label>
                <img style="margin-right:3px;vertical-align:middle;" alt="{LANG.openid_login}" title="{LANG.openid_login}" src="{OPENID_IMG_SRC}" width="{OPENID_IMG_WIDTH}" height="{OPENID_IMG_HEIGHT}" /> {LANG.openid_login} 
            </label>
            <!-- BEGIN: server -->
            <a class="forgot fl" title="{OPENID.title}" href="{OPENID.href}"><img style="margin-right:3px;vertical-align:middle;" alt="{OPENID.title}" title="{OPENID.title}" src="{OPENID.img_src}" width="{OPENID.img_width}" height="{OPENID.img_height}" /> {OPENID.title}</a>
            <!-- END: server -->
        </div>
        <!-- END: openid -->
    </fieldset>
</form>
</div>
<!-- END: main -->
<!-- BEGIN: signed -->
<div class="section visitorPanel">
	<div class="secondaryContent">
	
		<a href="{PAGE_USER}" class="avatar Av{USER.userid}"><img src="{AVATA}" width="96" height="96" alt="{USER.full_name}"></a>
		
		<div class="visitorText">
			<h2><!-- <span class="muted">Signed in as</span> --> <a href="{PAGE_USER}" class="username NoOverlay">{USER.full_name}</a></h2>		
			<div class="stats">
			
				<dl class="pairsJustified"><dt>Bài viết:</dt> <dd>0</dd></dl>
				<dl class="pairsJustified"><dt>Thích:</dt> <dd>0</dd></dl>
				<dl class="pairsJustified"><dt>Điểm:</dt> <dd>0</dd></dl>
			</div>
			
		</div>
		<div class="clear"></div>
		
	</div>
</div>
<!-- END: signed -->