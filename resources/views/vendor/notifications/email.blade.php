<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<?php
$style = [
  'body' => 'margin: 20px; padding: 0; width: 100%; background-color: #FFFFFF;',
  'anchor' => 'color: #3869D4; text-decoration:none;',
  'header-1' => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
  'paragraph' => 'margin-top: 0; color: #0B0C0D; font-size: 16px; line-height: 1.5em; white-space:normal !important; -ms-word-break: break-all; word-break: break-all; word-break: break-word;',
  'paragraph-sub' => 'margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;',
  'paragraph-signature' => 'margin-top: 30px color: #5F5F5F; font-size: 14px;',
  'line' => 'border: 0; height: 0px; border-top: 1px solid rgba(0, 0, 0, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.3);',
];

$fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;';
?>

<body style="{{ $style['body'] }} {{ $fontFamily }}">

  <!-- Greeting -->
  @if (! empty($greeting))
  <h1 style="{{ $style['header-1'] }}">
    {{ $greeting }}
  </h1>
  @endif

  <!-- Intro -->
  @foreach ($introLines as $line)
  <p style="{{ $style['paragraph'] }}">
    {{ $line }}
  </p>
  @endforeach

  <!-- Action Button -->
  @if (isset($actionText))
  <?php
  switch ($level) {
    case 'success':
    $actionColor = '#29A33E';
    break;
    case 'error':
    $actionColor = '#FF1916';
    break;
    default:
    $actionColor = '#3869E8';
  }
  ?>
  <p style="{{ $style['paragraph'] }}">
    <strong>点击下面链接 <span style="color: {{ $actionColor }}">{{ $actionText }}</span> ：</strong>
    <br>
    <a href="{{ $actionUrl }}" target="_blank">{{ $actionUrl }}</a>
  </p>
  @endif

  <!-- Outro -->
  @foreach ($outroLines as $line)
  <p style="{{ $style['paragraph'] }}">
    {{ $line }}
  </p>
  @endforeach

  <!-- Salutation -->
  <p style="{{ $style['paragraph'] }} {{ $style['paragraph-signature'] }}">
    Best regards,<br>{{ config('app.name') }}
  </p>

  <!-- Footer -->
  <hr style="{{ $style['line'] }}">

  <p style="{{ $style['paragraph-sub'] }}">
    &copy; {{ date('Y') }}
    <a style="{{ $style['anchor'] }}" href="{{ app_url() }}" target="_blank">{{ config('app.name') }}</a>
    &nbsp;&nbsp;All rights reserved.
  </p>

</body>
</html>
