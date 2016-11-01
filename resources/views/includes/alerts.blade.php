@hasSection ('alerts-container')
<?php
$__alerts = session('alert', []);
$__alerts['danger'] = array_merge(
  (array) $errors->all(),
  (array) array_pull($__alerts, 'danger'),
  (array) array_pull($__alerts, 'error')
);
$__alerts = array_filter($__alerts);

foreach ($__alerts as $__alertType => $__alertMessage) {
  if (is_array($__alertMessage)) {
    $__alertMessage = '<ul>'.implode(array_map(function ($value) {
      return '<li>'.htmlentities($value).'</li>';
    }, $__alertMessage)).'</ul>';
  } else {
    $__alertMessage = string_value($__alertMessage);
  }

  $__alertMessage = trim(json_encode($__alertMessage), '"');
?>
<script>
  $(function () {
    $("@yield('alerts-container')").first().bootnotify("{!! $__alertMessage !!}", "{{  $__alertType }}");
  });
</script>
<?php
}
?>
@endif
