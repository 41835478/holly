@extends('layouts.admin.default')

@section('title', '新增管理员')

@push('js')
<script>
$(function() {
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
    },
    complete: function(xhr, status, form) {
      $(form).spin(false);
      $(form).find('button[type=submit]').prop('disabled', false);
    }
  });
})
</script>
@endpush

@section('content')
<div class="row">
  <div class="col-md-6">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">新增管理员</h3>
      </div>
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
@stop
