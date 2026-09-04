<?php
 
$authService->logout();
 
header("Location: index.php?page=login");
exit;