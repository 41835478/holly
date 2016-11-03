<header class="main-header"><!-- Main Header -->
  <a href="/" class="logo"><!-- Logo -->
    <span class="logo-mini"><b>{{ config('app.name') }}</b></span><!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-lg"><b>{{ config('app.name') }}</b></span><!-- logo for regular state and mobile devices -->
  </a>

  <nav class="navbar navbar-static-top" role="navigation"><!-- Header Navbar -->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><!-- Sidebar toggle button-->
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu"><!-- Navbar Right Menu -->
      <ul class="nav navbar-nav">
        @include('includes.admin.navbar-items')
      </ul>
    </div>
  </nav><!-- //.navbar -->
</header><!-- //.main-header -->
