@extends('layouts.admin.master')

@section('alerts-selector', '#login-form')
@section('alerts-position', 'bottom')

@section('title', '登录')
@section('body-class', 'login-page')

@push('js')
<script>
$(function() {
  $(':input:enabled:visible:first').select();

  $('#login-form').ajaxForm({
    beforeSerialize: function($form, options) {
      var form = $form[0];
      if (form.password && form.password.value.length) {
        options.data = {
          password: md5(form.password.value)
        };
      }
    },
    beforeSubmit: function(data, form) {
      $(form).bootnotify(false);
      for (var i = 0; i < data.length; i++) {
        if (!data[i].value) {
          $(form).bootnotify('请输入' + $(form).find('input[name=' + data[i].name + ']').attr('placeholder'), 'danger');
          return false;
        }
      }

      $(form).find('button[type=submit]').prop('disabled', true);
      $(form).spin();
    },
    success: function(data, status, xhr, form) {
      $(form).spin(false);
      if (data.code == 1) {
        $(form).bootnotifyApi(data).delay(1000, function() {
          window.location.replace(data.url !== undefined ? data.url : '/');
        });
      } else {
        $(form).bootnotifyApi(data);
        $(form).find('button[type=submit]').prop('disabled', false);
      }
    }
  });

  $("html").keydown(function(e) {
    if (e.keyCode == 13) {
      $('#login-form').submit();
    }
  });
});
</script>
@endpush

@section('body')
<div class="login-box">
  <div class="login-logo">
    <b>{{ config('app.name') }}</b>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">登录到管理后台</p>
    <form id="login-form" action="" method="POST">
      <div class="form-group has-feedback">
        <input name="email" type="email" class="form-control" placeholder="邮箱" value="{{ Request::cookie('email') }}">
        <i class="fa fa-envelope form-control-feedback"></i>
      </div>

      <div class="form-group has-feedback">
        <input name="password" type="password" class="form-control" placeholder="密码">
        <i class="fa fa-lock form-control-feedback"></i>
      </div>

      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck"><label><input name="remember" type="checkbox" {{ Request::cookie('remember_me') ? 'checked' : '' }}> 记住账号</label></div>
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block">登录</button>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <a href="/password/reset">忘记密码</a>
        </div>
      </div>
    </form>
  </div> <!-- //.login-box-body -->

</div> <!-- //.login-box -->
@stop
