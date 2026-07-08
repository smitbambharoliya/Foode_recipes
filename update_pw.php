<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=recipe_calculator;charset=utf8mb4', 'root', '');
$pdo->exec("UPDATE user SET password = '\$2y\$13\$4yLia1GiU5NzLpdvd91LcOigz1Ejatyl4gAs1oOulijMdqAVQoS9G' WHERE email = 'chef@example.com'");
echo "Password updated\n";
