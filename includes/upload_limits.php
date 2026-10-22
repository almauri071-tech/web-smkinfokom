<?php
function maximum_upload_size(int $applicationLimit): int {
  $limits = [$applicationLimit];
  foreach (['upload_max_filesize'=>0, 'post_max_size'=>262144] as $setting=>$overhead) {
    $value = trim((string)ini_get($setting));
    if ($value === '' || $value === '0') continue;
    if (!preg_match('/^(\d+)\s*([KMG])?$/i', $value, $matches)) continue;
    $bytes = (int)$matches[1];
    $unit = strtoupper($matches[2] ?? '');
    if ($unit === 'K') $bytes *= 1024;
    if ($unit === 'M') $bytes *= 1024 * 1024;
    if ($unit === 'G') $bytes *= 1024 * 1024 * 1024;
    $limits[] = max(0, $bytes - $overhead);
  }
  return min($limits);
}
