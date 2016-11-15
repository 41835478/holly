<li class="{{ active_if('/') }}">
  <a href="/"><i class='fa fa-fw fa-home'></i> 首页</a>
</li>

<li class="treeview {{ active_if('user', 'user/*', 'device', 'device/*', 'feedback') }}">
  <a href="#"><i class="fa fa-fw fa-database"></i> 数据管理<i class="fa fa-fw fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    <li class="{{ active_if('user', 'user/*') }}">
      <a href="/user"><i class="fa fa-fw fa-users"></i> 用户</a>
    </li>
    <li class="{{ active_if('device', 'device/*') }}">
      <a href="/device"><i class="fa fa-fw fa-tablet"></i> 设备</a>
    </li>
    <li class="{{ active_if('feedback') }}">
      <a href="/feedback"><i class="fa fa-fw fa-pencil-square-o"></i> 意见反馈</a>
    </li>
  </ul>
</li>

<li class="treeview {{ active_if('admin/*') }}">
  <a href="#"><i class="fa fa-fw fa-lock"></i> 管理员<i class="fa fa-fw fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    @can('manager', 'App\Models\AdminUser')
    <li class="{{ active_if('admin/users') }}">
      <a href="/admin/users"><i class="fa fa-fw fa-users"></i> 管理员列表</a>
    </li>
    @endcan
    @can('create', 'App\Models\AdminUser')
    <li class="{{ active_if('admin/user/create') }}">
      <a href="/admin/user/create"><i class="fa fa-fw fa-user-plus"></i> 新增管理员</a>
    </li>
    @endcan
    <li class="{{ active_if('admin/profile', 'admin/profile/*') }}">
      <a href="/admin/profile"><i class="fa fa-fw fa-user"></i> 用户资料</a>
    </li>
  </ul>
</li>
