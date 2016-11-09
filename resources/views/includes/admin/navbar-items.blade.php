<!-- Messages: style can be found in dropdown.less-->
<li class="dropdown messages-menu">
  <!-- Menu toggle button -->
  <a href="{{ app_url() }}" target="_blank">
    <i class="fa fa-home"> 网站首页</i>
  </a>
</li> <!-- end messages-menu-->

<!-- Messages: style can be found in dropdown.less-->
<li class="dropdown notifications-menu">
  <!-- Menu toggle button -->
  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-link"> 链接</i>
  </a>
  <ul class="dropdown-menu">
    <li>
      <ul class="menu">
        <li><a href="#" target="_blank"><i class="fa fa-fw fa-apple text-black"></i> App Store</a></li>
        <li><a href="/log" target="_blank"><i class="fa fa-fw fa-lock text-red"></i> Log Viewer</a></li>
      </ul>
    </li>
    {{-- <li class="footer"><a href="#">See All Messages</a></li> --}}
  </ul>
</li> <!-- end messages-menu-->

<!-- User Account Menu -->
<li class="dropdown user user-menu">
  <!-- Menu Toggle Button -->
  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <!-- The user image in the navbar-->
    <img src="{{ Auth::user()->avatar }}" class="user-image" alt="User Image">
    <!-- hidden-xs hides the username on small devices so only the image appears.-->
    <span class="hidden-xs">{{ Auth::user()->username }}</span>
  </a>
  <ul class="dropdown-menu">
    <!-- The user image in the menu -->
    <li class="user-header">
      <img src="{{ Auth::user()->avatar }}" class="img-circle" alt="User Image">
      <p>
        {{ Auth::user()->username }}
        <small>
          {{ Auth::user()->email }}
        </small>
      </p>
    </li>
    <!-- Menu Footer-->
    <li class="user-footer">
      <div class="pull-left">
        <a href="/admin/profile" class="btn btn-default btn-flat">用户中心</a>
      </div>
      <div class="pull-right">
        <a href="/logout" class="btn btn-default btn-flat">登出</a>
      </div>
    </li>
  </ul>
</li>
