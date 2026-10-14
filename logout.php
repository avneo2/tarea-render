<?php
// Copiar a: C:\xampp\htdocs\login\logout.php

session_start();
$_SESSION = [];
session_destroy();

header('Location: login.php');
