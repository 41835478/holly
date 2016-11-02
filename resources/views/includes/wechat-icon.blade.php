@if ($client->isWechat)
  <div style="display:none"><img src="@yield('wechat-icon', asset_url('img/icon-300.png'))"></div>
@endif
