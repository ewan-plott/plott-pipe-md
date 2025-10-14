<?php
/**
 * log_changelog.php
 * Posts composer update deltas to a Nuxt/Nitro API (no files).
 *
 * Required env:
 *  - NUXT_API_URL=https://dashboard.example.com/api/changelogs
 *  - NUXT_API_KEY=your-bearer-key
 *  - NUXT_HMAC_SECRET=your-long-random-secret
 *  - SITE_ID=cc-london (short slug)
 *  - SITE_NAME="Clements & Church"
 *  - SITE_ENV=production|staging|dev
 * Optional:
 *  - CI_BUILD_URL, GIT_SHA, GIT_BRANCH
 */

date_default_timezone_set(@date_default_timezone_get());

// ---------- Config ----------
$API_URL   = getenv('NUXT_API_URL') ?: 'https://maintenance.plott.co.uk/api/changelogs';
$API_KEY   = getenv('NUXT_API_KEY') ?: '55616d31e0e60b8f91406ed4af325eccc690b020f55b8df9d67199af8b782f94';
$HMAC      = getenv('NUXT_HMAC_SECRET') ?: 'a43138c3df492aced2b641ef34f1314eee4b87eee090c94cecb9ec172811c7be';
$SITE_ID   = getenv('SITE_ID') ?: '{{ SITE_ID }}';
$SITE_NAME = getenv('SITE_NAME') ?: '{{ SITE_NAME }}';
$SITE_ENV  = getenv('SITE_ENV') ?: 'production';

$oldLockFile = __DIR__ . '/composer.lock.bak';
$newLockFile = __DIR__ . '/composer.lock';

if (!file_exists($newLockFile)) {
    fwrite(STDERR, "New composer.lock file is missing.\n");
    exit(1);
}
if (!file_exists($oldLockFile)) {
    echo "Backup composer.lock is missing. Skipping (fresh install / install path).\n";
    exit(0);
}

$oldLock = json_decode(file_get_contents($oldLockFile), true) ?: [];
$newLock = json_decode(file_get_contents($newLockFile), true) ?: [];

function map_packages(array $lock): array {
    $out = [];
    foreach (($lock['packages'] ?? []) as $p)      { $out[$p['name']] = $p['version'] ?? ''; }
    foreach (($lock['packages-dev'] ?? []) as $p)  { $out[$p['name']] = $p['version'] ?? ''; }
    return $out;
}
$old = map_packages($oldLock);
$new = map_packages($newLock);

// diffs
$updated = [];
foreach ($new as $name => $newVer) {
    $oldVer = $old[$name] ?? null;
    if ($oldVer === null) continue;         // will appear in $added
    if ($newVer !== $oldVer) {
        $updated[] = ['name'=>$name,'old'=>$oldVer,'new'=>$newVer];
    }
}
$added = [];
foreach (array_diff_key($new, $old) as $name => $ver) {
    $added[] = ['name'=>$name,'new'=>$ver];
}
$removed = [];
foreach (array_diff_key($old, $new) as $name => $ver) {
    $removed[] = ['name'=>$name,'old'=>$ver];
}

// sort for stability
usort($updated, fn($a,$b)=>strcmp($a['name'],$b['name']));
usort($added,   fn($a,$b)=>strcmp($a['name'],$b['name']));
usort($removed, fn($a,$b)=>strcmp($a['name'],$b['name']));

$nowIso = (new DateTimeImmutable())->format(DateTimeInterface::ATOM);
$payload = [
    'site' => [
        'id'   => $SITE_ID,
        'name' => $SITE_NAME,
        'env'  => $SITE_ENV,
    ],
    'run' => [
        'timestamp'   => $nowIso,
        'php_version' => PHP_VERSION,
        'composer'    => $newLock['plugin-api-version'] ?? null,
        'ci_url'      => getenv('CI_BUILD_URL') ?: null,
        'git_sha'     => getenv('GIT_SHA') ?: trim((string)@shell_exec('git rev-parse --short HEAD')),
        'git_branch'  => getenv('GIT_BRANCH') ?: trim((string)@shell_exec('git rev-parse --abbrev-ref HEAD')),
    ],
    'summary' => [
        'updated_count' => count($updated),
        'added_count'   => count($added),
        'removed_count' => count($removed),
        'has_changes'   => (bool)(count($updated)+count($added)+count($removed)),
    ],
    'changes' => [
        'updated' => $updated, // [{name, old, new}]
        'added'   => $added,   // [{name, new}]
        'removed' => $removed, // [{name, old}]
    ],
];

// sign with HMAC (body-based)
$body = json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
$nonce = bin2hex(random_bytes(16));
$signature = base64_encode(hash_hmac('sha256', $nonce . '.' . $body, $HMAC, true));

// send
$ch = curl_init($API_URL);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $API_KEY,
        'X-Nonce: ' . $nonce,
        'X-Signature: ' . $signature,
    ],
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_POSTFIELDS     => $body,
]);
$response = curl_exec($ch);
$errno    = curl_errno($ch);
$http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

@unlink($oldLockFile);

if ($errno) {
    fwrite(STDERR, "POST failed. cURL error {$errno}\n");
    exit(2);
}
if ($http < 200 || $http >= 300) {
    fwrite(STDERR, "API HTTP {$http}. Body:\n{$response}\n");
    exit(3);
}
echo "Posted to dashboard (HTTP {$http}).\n";
