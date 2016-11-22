@if ($client->isWechat)
  <div style="display:none"><img src="@yield('wechat-icon', asset_url('assets/icon-300.png'))"></div>
@endif
