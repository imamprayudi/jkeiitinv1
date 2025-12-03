<?php
header("Access-Control-Allow-Origin: same-origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: text/html; charset=utf-8");
header("X-Content-Type-Options: nosniff");

if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    die("Forbidden Access");
    exit();
}

// Fungsi untuk mendapatkan data session
// dan mengembalikannya dalam format JSON
// Programmer: Imam Prayudi
// Date Created : 2025-10-20
// Date Updated : 2025-11-07
//-----------------------------------------------
session_start();
if (isset($_SESSION['user'])) 
{
  $user = trim($_SESSION['user']);
  $level = $_SESSION['level'];
  $env = parse_ini_file(__DIR__ . '/.env');
  $urlmasukdetail = $env['API_MASUKDETAIL_URL'];
  $urlkeluardetail = $env['API_KELUARDETAIL_URL'];
  $urlperiode = $env['API_PERIODE_URL'];
  $urlscrap = $env['API_SCRAP_URL'];
  $urlmaterial = $env['API_MATERIAL_URL'];
  $urlfg = $env['API_FG_URL'];
  $urlfa = $env['API_FA_URL'];
  // Buat array respons JSON
  $response = array(
    'user' => $user,
    'level' => $level,
    'urlmasukdetail' => $urlmasukdetail,
    'urlkeluardetail' => $urlkeluardetail,
    'urlperiode' => $urlperiode,
    'urlscrap' => $urlscrap,
    'urlmaterial' => $urlmaterial,
    'urlfg' => $urlfg,
    'urlfa' => $urlfa
  );
  echo json_encode($response);
} else 
{
    $user = "";
    echo "Session not found";
}

?>
