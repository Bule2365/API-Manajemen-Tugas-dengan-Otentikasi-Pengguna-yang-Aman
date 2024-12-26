<?php
$timestamp_exp = 1735369861;
echo date('Y-m-d H:i:s', $timestamp_exp);  // Menampilkan waktu dalam format yang mudah dibaca
echo "<br>";
$currentTime = time();  // Waktu sekarang (timestamp UNIX)
echo date('Y-m-d H:i:s', $currentTime);  // Menampilkan waktu sekarang dalam format yang lebih mudah dibaca
