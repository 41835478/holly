<li class="{{ active_if('/') }}">
  <a href="/"><i class='fa fa-fw fa-home'></i> <span>首页</span></a>
</li>

<li class="treeview {{ active_if('user', 'user/*', 'device', 'device/*', 'feedback') }}">
  <a href="#"><i class="fa fa-fw fa-database"></i> <span>数据管理</span><i class="fa fa-fw fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    <li class="{{ active_if('user', 'user/*') }}">
      <a href="/user"><i class="fa fa-fw fa-users"></i> <span>用户</span></a>
    </li>
    <li class="{{ active_if('device', 'device/*') }}">
      <a href="/device"><i class="fa fa-fw fa-tablet"></i> <span>设备</span></a>
    </li>
    <li class="{{ active_if('feedback') }}">
      <a href="/feedback"><i class="fa fa-fw fa-pencil-square-o"></i> <span>意见反馈</span></a>
    </li>
  </ul>
</li>

<li class="treeview {{ active_if('admin/*') }}">
  <a href="#"><i class="fa fa-fw fa-lock"></i> <span>管理员</span><i class="fa fa-fw fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    @can('manager', 'App\Models\AdminUser')
    <li class="{{ active_if('admin/users') }}">
      <a href="/admin/users"><i class="fa fa-fw fa-users"></i> <span>管理员列表</span></a>
    </li>
    @endcan
    @can('create', 'App\Models\AdminUser')
    <li class="{{ active_if('admin/user/create') }}">
      <a href="/admin/user/create"><i class="fa fa-fw fa-user-plus"></i> <span>新增管理员</span></a>
    </li>
    @endcan
    <li class="{{ active_if('admin/profile', 'admin/profile/*') }}">
      <a href="/admin/profile"><i class="fa fa-fw fa-user"></i> <span>用户资料</span></a>
    </li>
  </ul>
</li>
