<?php
function isOfficeOpen() {
  $newYorkTimeZone = new DateTimeZone('America/New_York');
  $newYorkTime = new DateTime('now', $newYorkTimeZone);
  $openingTime = new DateTime('9:00', $newYorkTimeZone);
  $closingTime = new DateTime('17:00', $newYorkTimeZone);
  $closingTimeSat = new DateTime('14:00', $newYorkTimeZone);
  $y = (int) $newYorkTime->format('Y');
  $m = (int) $newYorkTime->format('n');
  $d = (int) $newYorkTime->format('d');
  $openingTime->setDate($y, $m, $d);
  $closingTime->setDate($y, $m, $d);
  $dayOfWeek = (int) $newYorkTime->format('N');
  $isSaturday = ($dayOfWeek === 6);
  $isSunday = ($dayOfWeek === 7);
  if ($isSaturday) {
    $isOpen = $newYorkTime >= $openingTime && $newYorkTime <= $closingTimeSat;
  } elseif ($isSunday) {
    $isOpen = false;
  } else {
    $isOpen = $newYorkTime >= $openingTime && $newYorkTime <= $closingTime;
  }
  return $isOpen;
}
