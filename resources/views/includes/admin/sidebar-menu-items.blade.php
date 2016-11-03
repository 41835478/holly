<li class="{{ active_if('/') }}">
  <a href="/"><i class='fa fa-home'></i> <span>首页</span></a>
</li>

<li class="treeview {{ active_if('stock') }}">
  <a href="#"><i class="fa fa-wrench"></i> <span>功能</span><i class="fa fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    <li class="{{ active_if('stock') }}"><a href="/stock"><i class="fa fa-line-chart"></i> <span>推荐股</span></a></li>
  </ul>
</li>

<li class="treeview {{ active_if('user', 'user/*', 'device', 'device/*', 'news', 'feedback', 'order', 'promotion') }}">
  <a href="#"><i class="fa fa-database"></i> <span>数据管理</span><i class="fa fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    <li class="{{ active_if('user', 'user/*') }}"><a href="/user"><i class="fa fa-users"></i> <span>用户</span></a></li>
    <li class="{{ active_if('device', 'device/*') }}"><a href="/device"><i class="fa fa-tablet"></i> <span>设备</span></a></li>
    <li class="{{ active_if('order') }}"><a href="/order"><i class="fa fa-cart-plus"></i> <span>订单</span></a></li>
    <li class="{{ active_if('promotion') }}"><a href="/promotion"><i class="fa fa-star"></i> <span>优惠活动</span></a></li>
    <li class="{{ active_if('news') }}"><a href="/news"><i class="fa fa-newspaper-o"></i> <span>资讯</span></a></li>
    <li class="{{ active_if('feedback') }}"><a href="/feedback"><i class="fa fa-pencil-square-o"></i> <span>意见反馈</span></a></li>
  </ul>
</li>

<li class="treeview {{ active_if('admin/*') }}">
  <a href="#"><i class="fa fa-lock"></i> <span>管理员</span><i class="fa fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    <li class="{{ active_if('admin/users') }}"><a href="/admin/users"><i class="fa fa-users"></i> <span>管理员列表</span></a></li>
    <li class="{{ active_if('admin/profile') }}"><a href="/admin/profile"><i class="fa fa-user"></i> <span>用户资料</span></a></li>
  </ul>
</li>
