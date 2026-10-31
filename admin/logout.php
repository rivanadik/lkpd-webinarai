<?php
require '../config.php';
unset($_SESSION['admin_id']); session_regenerate_id(true);
header('Location: index.php');
