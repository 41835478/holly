@hasSection ('wechat-icon')
@if ($client->isWechat)
  <div style="display:none"><img src="@yield('wechat-icon')"></div>
@endif
@endif
