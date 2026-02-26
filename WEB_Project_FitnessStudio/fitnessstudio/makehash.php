<?php
// Zëvendëso 'secret123' me fjalëkalimin që do
$plain = 'secret123';
$hash  = password_hash($plain, PASSWORD_DEFAULT);
echo "<p>Plain: <strong>$plain</strong></p>";
echo "<p>Hash : <strong>$hash</strong></p>";
