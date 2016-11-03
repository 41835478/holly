@hasSection ('alerts-selector')
<?php
$__alerts = session('alert', []);
$__alerts['danger'] = array_merge(
  (array) $errors->all(),
  (array) array_pull($__alerts, 'danger'),
  (array) array_pull($__alerts, 'error')
);
if (count($__alerts['danger']) == 1) {
  $__alerts['danger'] = head($__alerts['danger']);
}
$__alerts = array_filter($__alerts);
?>
@if (count($__alerts) > 0)
<script>
@foreach ($__alerts as $__alertType => $__alertMessage)
<?php
if (is_array($__alertMessage)) {
  $__alertMessage = '<ul>'.implode(array_map(function ($value) {
    return '<li>'.htmlentities($value).'</li>';
  }, $__alertMessage)).'</ul>';
} else {
  $__alertMessage = string_value($__alertMessage);
}

$__alertMessage = trim(json_encode($__alertMessage), '"');
?>
$(function () {
  $("@yield('alerts-selector')").bootnotify("{!! $__alertMessage !!}", "{{  $__alertType }} @yield('alerts-class')", "@yield('alerts-position', 'bottom')");
});
@endforeach
</script>
@endif
@endif
