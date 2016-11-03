<div class="content-wrapper"><!-- Content Wrapper. Contains page content -->
  @hasSection('content-title')
  <section class="content-header"><!-- Content Header (Page header) -->
    <h1>
      @yield('content-title')
      @hasSection('content-description')
      <small>@yield('content-description')</small>
      @endif
    </h1>
  </section>
  @endif

  <div class="content"><!-- Main content -->
    @yield('content')
  </div><!-- //.content -->
</div><!-- //.content-wrapper -->
