@hasSection ('alert-container')
@foreach (session('alert', []) as $__alertType => $__alertMessage)
<?php
if (empty($__alertMessage)) {
  continue;
}

if ($__alertType == 'error') {
  $__alertType = 'danger';
}

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
    $("@yield('alert-container')").first().bootnotify("{!! $__alertMessage !!}", "{{  $__alertType }}");
  });
</script>
@endforeach
@endif
