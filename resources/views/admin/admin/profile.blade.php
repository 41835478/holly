@extends('layouts.admin.default')

@section('title', '管理员用户资料')

@push('css')
<style type="text/css">
  .editface-img-container {
    margin: 0 20px;
  }
  #editface-img {
    width: 60px;
    height: 60px;
  }
</style>
@endpush

@push('js')
<script>
var user = {!! json_encode($user->toArray()) !!};

function updateUserInfo() {
  $('#editface-file-input').clearFields();
  $('#editface-img').attr('src', user.avatar);
  $('#editface-img').parent('a').attr('href', user.avatar);
  $('#profile-form input[name=email]').val(user.email);
  $('#profile-form input[name=username]').val(user.username);
}

$(function() {
  updateUserInfo();

  $('#profile-form').ajaxForm({
    beforeSerialize: function($form, options) {
      var form = $form[0];
      if (form.password && form.password.value.length) {
        options.data = {
          password: md5(form.password.value)
        };
      }
    },
    beforeSubmit: function(data, form){
      $(form).find('button[type=submit]').prop('disabled', true);
      $(form).bootnotify(false);
      $(form).spin();
    },
    success: function(data, status, xhr, form) {
      $(form).bootnotifyJSON(data);
      if (data.user) {
        user = data.user;
        updateUserInfo();
      }
    },
    complete: function(xhr, status, form) {
      $(form).spin(false);
      $(form).find('button[type=submit]').prop('disabled', false);
    }
  });

  $('#editface-button').on('click touchstart', function(e){
    e.preventDefault();
    $('#editface-file-input').trigger('click');
  });

  $('#editface-file-input').change(function(e) {
    var url = URL.createObjectURL(e.target.files[0]);
    if (url) {
      $('#editface-img').attr('src', url);
      $('#editface-img').parent('a').attr('href', url);
    }
  });

  $('#use-default-avatar').click(function(e){
    e.preventDefault();
    $('#profile-form').find('input[name=use_default_avatar]').val(1);
    $('#profile-form').submit();
  });
})
</script>
@endpush

@section('content')
<div class="row">
  <div class="col-md-6">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">编辑资料</h3>
      </div><!-- /.box-header -->
      <form id="profile-form" action="" method="POST" role="form">
        <div class="box-body">

          <div class="form-group">
            <a data-lightbox="editface" class="editface-img-container">
              <img id="editface-img" class="img-circle">
            </a>
            <a href="#" id="use-default-avatar">使用默认头像</a> |
            <a href="#" id="editface-button">上传头像</a>

            <input type="hidden" name="use_default_avatar">
            <input id="editface-file-input" type="file" name="avatar" accept="image/jpeg, image/gif, image/png" style="display:none">
          </div>

          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <span class="fa fa-envelope"></span>
              </div>
              <input type="email" name="email" class="form-control" placeholder="邮箱" disabled>
            </div><!-- /.input group -->
          </div>

          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <span class="fa fa-user"></span>
              </div>
              <input type="text" name="username" class="form-control" placeholder="用户名"">
            </div><!-- /.input group -->
          </div>

          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <span class="fa fa-lock"></span>
              </div>
              <input type="password" name="password" class="form-control" placeholder="密码">
            </div><!-- /.input group -->
          </div>

        </div><!-- /.box-body -->

        <div class="box-footer">
          <button type="submit" class="btn btn-primary">提交修改</button>
        </div>
      </form>

    </div><!-- /.box -->
  </div><!--/.col (left) -->
</div>
@stop
