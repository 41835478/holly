<script>
(function ($) {
  $('#adminUsersTable tbody').on('click', '[class*="admin-user-action-"]', function (e) {
    e.preventDefault();
    let action = /admin-user-action-(\w+)/g.exec($(this).attr('class'))[1];
    let table = DataTables['adminUsersTable'];
    let data = table.row($(this).dataTableRow()).data();

    if (action == 'edit') {
      location.href = '/admin/profile/'+data.id;
    } else if (action == 'delete') {
      swal({
        type: 'warning',
        html: '确定删除管理员 <strong>'+data.username+'</strong> 吗？',
        showCancelButton: true,
        confirmButtonText: '确定删除'
      }).then(function () {
        $.post('/admin/user/delete/'+data.id, function(json) {
            if (json.code == 1) {
              table.ajax.reload(null, false);
            } else {
              swalApi(json);
            }
        });
      });
    }
  });
})(jQuery)
</script>
