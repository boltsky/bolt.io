<?php

session_start();
session_unset();   
session_destroy();

header("Location: http://localhost:8443/Websites/Forum/login.php");