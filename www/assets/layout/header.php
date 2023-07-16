<?php

ob_start();
ob_implicit_flush(true);
Header::Display();
ob_flush();
