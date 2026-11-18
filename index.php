<?php
require_once __DIR__ . '/includes/init.php';
if (!currentUser()) redirect('login.php');
redirect(currentUser()['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php');
