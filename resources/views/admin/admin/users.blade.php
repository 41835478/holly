@extends('layouts.admin.default')

@section('title', '管理员列表')

@push('js')
<script>
$(function() {
  var usersTable = $('#users-table').DataTable({
    ajax: '/admin/users-data',
    columns: [
    {title: 'ID', data: 'id'},
    {title: '头像', data: 'avatar', orderable: false, searchable: false,
    render: function(data, type, row, meta) {
      if (type === 'display') {
        if (data) {
          return "<a href='" + data + "' data-lightbox='user-avatar-" + row.id + "'> \
            <img src='" + data + "' class='img-circle' style='width:28px;height:28px;'> \
            </a>";
        } else {
          return "";
        }
      }
      return data;
    }},
    {title: '用户名', data: 'username'},
    {title: 'Email', data: 'email'},
    {title: '创建日期', data: 'created_at'}
    @if (Auth::user()->isSuperAdmin())
    ,{title: 'Action', name:'action', orderable: false, searchable: false, data: function(data) {
      return '<div class="btn-group"> \
        <button type="button" class="btn btn-info users-action users-action-edit"><i class="fa fa-edit"></i></button> \
        <button type="button" class="btn btn-danger users-action users-action-delete">删除</button> \
        </div>';
    }}
    @endif
    ]
  });

  $('#users-table').on('click', '.users-action', function() {
      // var row = usersTable.row($(this).closest('tr'));
      // Fixed: 折叠后 closest('tr') 找不到对应的行
      // https://www.datatables.net/forums/discussion/29875/datatable-responsive-row-collapse-problem
      var row = usersTable.row($(this).closest('tr').prev('.parent').length ? $(this).closest('tr').prev('.parent') : $(this).closest('tr'));

      var data = row.data();
      var className = $(this).attr('class');
      if (className.indexOf('users-action-edit') > -1) {
        location.href = '/admin/profile/' + data.id;
      } else if (className.indexOf('users-action-delete') > -1) {
        bootbox.dialog({
          title: '<i class="icon fa fa-warning"></i> 警告',
          message: '<strong>确定删除管理员「'+ data.username +'」（'+ data.email +'）吗?</strong>',
          className: 'modal-danger',
          backdrop: true,
          buttons: {
            success: {
              label: '确定删除',
              className: 'btn-outline pull-left',
              callback: function() {
                $('#users-list').bootnotify(false);
                $.post('/admin/user/delete/'+data.id,
                  function(json) {
                    if (json.code == 1) {
                      usersTable.ajax.reload();
                    } else {
                      $('#users-list').bootnotifyJSON(json, {position:"top"});
                    }
                  });
              }
            },
            cancel: {
              label: '取消',
              className: 'btn-outline'
            }
          }
        });
      }
    });

  $('#create-user-form').ajaxForm({
    beforeSerialize: function($form, options) {
      var form = $form[0];
      if (form.password && form.password.value.length) {
        options.data = {
          password: md5(form.password.value)
        };
      }
    },
    beforeSubmit: function(data, form) {
      $(form).find('button[type=submit]').prop('disabled', true);
      $(form).bootnotify(false);
      $(form).spin();
    },
    success: function(data, status, xhr, form) {
      $(form).bootnotifyJSON(data);
      if (data.code == 1) {
        usersTable.ajax.reload();
      }
    },
    complete: function(xhr, status, form) {
      $(form).spin(false);
      $(form).find('button[type=submit]').prop('disabled', false);
    }
  });

});
</script>
@endpush

@section('content')

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary" id="users-list">
      <div class="box-header with-border">
        <h3 class="box-title">管理员列表</h3>
      </div>
      <div class="box-body">
        <table id="users-table" class="table table-bordered table-striped nowrap" style="width:100%"></table>
      </div>
    </div>
  </div>
</div>

@can('create', \App\Models\AdminUser::class)
<div class="row">
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-danger">
      <div class="box-header with-border">
        <h3 class="box-title">新增管理员</h3>
      </div><!-- /.box-header -->
      <!-- form start -->
      <form id="create-user-form" action="/admin/user/create" method="POST" role="form">
        <div class="box-body">

          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <span class="fa fa-envelope"></span>
              </div>
              <input type="email" name="email" class="form-control" placeholder="邮箱">
            </div><!-- /.input group -->
          </div>

          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <span class="fa fa-user"></span>
              </div>
              <input type="text" name="username" class="form-control" placeholder="用户名">
            </div><!-- /.input group -->
          </div>

          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <span class="fa fa-lock"></span>
              </div>
              <input type="text" name="password" class="form-control" placeholder="密码">
            </div><!-- /.input group -->
          </div>

        </div><!-- /.box-body -->

        <div class="box-footer">
          <button type="submit" class="btn btn-primary">提交</button>
        </div>
      </form>
    </div><!-- /.box -->
  </div><!--/.col -->
</div>
@endcan

@stop
