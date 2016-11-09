@extends('layouts.admin.master')

@section('alerts-selector', '#form')
@section('alerts-position', 'bottom')

@section('title', '重置密码')
@section('body-class', 'login-page')

@push('js')
<script>
$(function() {
  $(':input:enabled:visible:first').select();

  $('#form').ajaxForm({
    beforeSerialize: function($form, options) {
      var form = $form[0];
      if (form.password && form.password_confirmation && form.password.value.length && form.password_confirmation.value.length) {
        options.data = {
          password: md5(form.password.value),
          password_confirmation: md5(form.password_confirmation.value)
        };
      }
    },
    beforeSubmit: function(data, form){
      $(form).bootnotify(false);
      for (var i = 0; i < data.length; i++) {
        if (!data[i].value) {
          $(form).bootnotify('请输入'+$(form).find('input[name='+data[i].name+']').attr('placeholder'), 'danger');
          return false;
        }
      }

      $(form).find('button[type=submit]').prop('disabled', true);
      $(form).spin();
    },
    success: function(data, status, xhr, form){
      $(form).spin(false);
      if (data.code == 1) {
        $(form).bootnotifyJSON(data).delay(1000, function(){
          if (data.url !== undefined) {
            window.location.replace(data.url);
          }
        });
      } else {
        $(form).bootnotifyJSON(data);
        $(form).find('button[type=submit]').prop('disabled', false);
        $('a.captcha').refreshCaptcha();
      }
    }
  });

  $("html").keydown(function(e) {
    if (e.keyCode == 13) {
      $('#form').submit();
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
    <p class="login-box-msg">重置密码</p>

    <form id="form" action="{{ isset($token) ? '/password/reset' : '/password/email' }}" method="POST">
      <div class="form-group has-feedback">
        <input name="email" type="email" class="form-control" placeholder="邮箱" value="{{ Request::input('email') }}">
        <span class="fa fa-envelope form-control-feedback"></span>
      </div>

@if (!isset($token))
      <a href="#" class="captcha" tabindex="-1">
        <img class="captcha">
        <span class="fa fa-refresh"></span>
      </a>
      <div class="form-group has-feedback">
        <input name="captcha" type="text" class="form-control captcha" placeholder="验证码">
        <span class="fa fa-exclamation-circle form-control-feedback"></span>
      </div>

      <div class="form-group has-feedback">
        <button type="submit" class="btn btn-primary btn-block">重置密码</button>
      </div>

      <p>系统会发送一封包含重置密码的链接到您的邮箱。</p>
@else
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="form-group has-feedback">
        <input name="password" type="password" class="form-control" placeholder="新密码">
        <span class="fa fa-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input name="password_confirmation" type="password" class="form-control" placeholder="确认新密码">
        <span class="fa fa-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <button type="submit" class="btn btn-primary btn-block">修改密码</button>
      </div>
@endif

      <div class="row">
        <div class="col-xs-4">
          <a href="/login">返回登录</a>
        </div>
      </div>
    </form>
  </div> <!-- //.login-box-body -->

</div> <!-- //.login-box -->
@stop
