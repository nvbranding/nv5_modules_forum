<!-- BEGIN: main -->
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr class="header">
            <!-- BEGIN: header -->
            <td>
                <a href="{header.href}">{header.title}</a>
            </td>
            <!-- END: header -->
            <td style="text-align: center">
                <strong>{LANG.admin_permissions}</strong>
            </td>
            <td>&nbsp;</td>
        </tr>
    </thead>
    <!-- BEGIN: loop -->
    <tbody>
        <tr>
            <td>{content.userid}</td>
            <td>
                <!-- BEGIN: is_admin --><img style="vertical-align: middle;" alt="{content.level}" title="{content.level}" src="{NV_BASE_SITEURL}themes/{NV_ADMIN_THEME}/images/{content.img}.png" width="38" height="18" /><!-- END: is_admin -->{content.username}
            </td>
            <td>{content.full_name}</td>
            <td>
                <a href="mailto:{content.email}">{content.email}</a>
            </td>
            <td>{content.admin_module_cat}</td>
            <td class="text-center">
                <!-- BEGIN: is_edit --><span class="edit_icon"><a href="{EDIT_URL}">{LANG.admin_edit}</a></span>
                <!-- END: is_edit -->
            </td>
        </tr>
    <!-- END: loop -->
    <tbody>
</table>
<!-- BEGIN: edit -->
<form class="form-inline" method="post" enctype="multipart/form-data" action="">
    <table class="table table-striped table-bordered table-hover">
        <caption>{CAPTION_EDIT}</caption>
        <tr>
            <td>{LANG.admin_permissions}</td>
            <td>
                <!-- BEGIN: admin_module -->
                <input name="admin_module" value="{ADMIN_MODULE.value}" type="radio" {ADMIN_MODULE.checked}>

                {ADMIN_MODULE.text}

                <!-- END: admin_module -->
            </td>
        </tr>
        <tbody style="{ADMINDISPLAY}" id="id_admin_module">
            <tr>
                <td colspan="2">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr class="header" class="text-center">
                                <td>{LANG.content_cat}</td>
                                <td>{LANG.permissions_add_content}</td>
                                <td>{LANG.permissions_pub_content}</td>
                                <td>{LANG.permissions_edit_content}</td>
                                <td>{LANG.permissions_del_content}</td>
                                <td>{LANG.comment}</td>
                                <td>{LANG.permissions_admin}</td>
                            </tr>
                        </thead>
                        <!-- BEGIN: catid -->
                        <tbody>
                            <tr>
                                <td>{cat.title}</td>
                                <td class="text-center">
                                    <input type="checkbox" name="add_content[{cat.catid}]" value="1"{cat.checked_add_content}></td>
                                <td class="text-center">
                                    <input type="checkbox" name="pub_content[{cat.catid}]" value="1"{cat.checked_pub_content}></td>
                                <td class="text-center">
                                    <input type="checkbox" name="edit_content[{cat.catid}]" value="1"{cat.checked_edit_content}></td>
                                <td class="text-center">
                                    <input type="checkbox" name="del_content[{cat.catid}]" value="1"{cat.checked_del_content}></td>
                                <td class="text-center">
                                    <input type="checkbox" name="comment[{cat.catid}]" value="1"{cat.checked_comment}></td>
                                <td class="text-center">
                                    <input type="checkbox" name="admin[{cat.catid}]" value="1"{cat.checked_admin}></td>
                            </tr>
                        <!-- END: catid -->
                        <tbody>
                    </table>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">
                    <input class="btn btn-primary" type="submit" value="{LANG.save}" name="submit"></td>
            </tr>
        </tfoot>
    </table>
</form>
<script type="text/javascript">
    $("input[name=admin_module]").click(function(){
        var type = $(this).val();
        if (type == 0) {
            $("#id_admin_module").show();
        }
        else {
            $("#id_admin_module").hide();
        }
    });
</script>
<!-- END: edit -->
<!-- END: main -->
<!-- BEGIN: view_user -->
<table class="table table-striped table-bordered table-hover">
    <caption>{CAPTION_EDIT}</caption>
    <thead>
        <tr class="header" class="text-center">
            <td>{LANG.content_cat}</td>
            <td>{LANG.permissions_add_content}</td>
            <td>{LANG.permissions_pub_content}</td>
            <td>{LANG.permissions_edit_content}</td>
            <td>{LANG.permissions_del_content}</td>
            <td>{LANG.comment}</td>
            <td>{LANG.permissions_admin}</td>
        </tr>
    </thead>
    <!-- BEGIN: catid -->
    <tbody>
        <tr>
            <td>{CONTENT.title}</td>
            <td class="text-center">{CONTENT.checked_add_content}</td>
            <td class="text-center">{CONTENT.checked_pub_content}</td>
            <td class="text-center">{CONTENT.checked_edit_content}</td>
            <td class="text-center">{CONTENT.checked_del_content}</td>
            <td class="text-center">{CONTENT.checked_comment}</td>
            <td class="text-center">{CONTENT.checked_admin}</td>
        </tr>
    <!-- END: catid -->
    <tbody>
</table>
<!-- END: view_user -->