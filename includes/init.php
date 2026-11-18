<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly' => true, 'secure' => $secure, 'samesite' => 'Lax']);
    session_start();
}

function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return rtrim(BASE_PATH, '/') . '/' . ltrim($path, '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function currentUser(): ?array { return $_SESSION['user'] ?? null; }
function requireLogin(): void { if (!currentUser()) redirect('login.php'); }
function requireAdmin(): void { requireLogin(); if (currentUser()['role'] !== 'admin') { http_response_code(403); exit('Access denied.'); } }
function csrfToken(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verifyCsrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) { http_response_code(400); exit('Invalid request token. Refresh the page and try again.'); } }
function flash(string $type, string $message): void { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function takeFlash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
function dateValue(?string $date): string { return $date ? date('M j, Y', strtotime($date)) : '—'; }
function itemCategories(): array { return db()->query('SELECT DISTINCT category FROM items ORDER BY category')->fetchAll(PDO::FETCH_COLUMN); }
function historyRows(array $filters = [], int $limit = 0): array {
    $sql = 'SELECT sm.*, i.item_code, i.item_name, i.category, i.unit, u.full_name FROM stock_movements sm JOIN items i ON i.id=sm.item_id JOIN users u ON u.id=sm.created_by WHERE 1=1';
    $params = [];
    foreach (['item_id' => 'sm.item_id', 'movement_type' => 'sm.movement_type'] as $key => $col) {
        if (!empty($filters[$key])) { $sql .= " AND $col = :$key"; $params[$key] = $filters[$key]; }
    }
    if (!empty($filters['category'])) { $sql .= ' AND i.category=:category'; $params['category'] = $filters['category']; }
    if (!empty($filters['start_date'])) { $sql .= ' AND sm.movement_date >= :start_date'; $params['start_date'] = $filters['start_date']; }
    if (!empty($filters['end_date'])) { $sql .= ' AND sm.movement_date <= :end_date'; $params['end_date'] = $filters['end_date']; }
    $sql .= ' ORDER BY sm.movement_date DESC, sm.id DESC';
    if ($limit > 0) $sql .= ' LIMIT ' . (int)$limit;
    $stmt = db()->prepare($sql); $stmt->execute($params); return $stmt->fetchAll();
}
function allItems(): array { return db()->query('SELECT id,item_code,item_name,category,location,quantity,unit,reorder_level FROM items ORDER BY item_name')->fetchAll(); }
function layoutStart(string $title, string $active = ''): void { require __DIR__ . '/header.php'; }
function layoutEnd(): void { require __DIR__ . '/footer.php'; }
