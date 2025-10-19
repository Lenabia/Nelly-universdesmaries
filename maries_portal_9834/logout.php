<?php
session_start();
session_destroy();
header('Location: messages.php');
exit;
