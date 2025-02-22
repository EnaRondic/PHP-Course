<?php

declare(strict_types=1);

const ALLOWED_METHODS = ['GET', 'POST'];
const INDEX_URI = '';
const INDEX_ROUTE = 'index';


function normalizeUri(string $uri): string {
  $uri = strtok($uri, '?');
  $uri = strtolower(trim($uri, '/'));
  return $uri === INDEX_URI ? INDEX_ROUTE : $uri;
}

function getFilePath(string $uri, string $method): string {
  return ROUTES_DIR . '/' . normalizeUri($uri) . '_' . strtolower($method) . '.php';
}

function notFound(): void {
  http_response_code(404);
  echo "404 Not Found";
  exit;
}

function badRequest(string $message = 'Bad request'): void {
    http_response_code(400);
    echo $message;
    exit;
  }

 function serverError(string $message = 'Server error'): void {
  http_response_code(500);
  echo $message;
  exit;
 } 

 function redirect(string $uri): void {
  header("Location: $uri");
  exit();
}

function dispatch(string $uri, string $method): void {
  $uri = normalizeUri($uri);
  $method = strtoupper($method);

  if (!in_array($method, ALLOWED_METHODS)) {
    notFound();
    return;
  }

  $filePath = getFilePath($uri, $method);

  if (file_exists($filePath)) {
    include($filePath);
    return;
  }

  notFound();
}
