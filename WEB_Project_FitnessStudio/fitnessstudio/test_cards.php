<?php
require __DIR__ . '/includes/crypto.php';
require __DIR__ . '/includes/db.php';   // <-- brings in $conn

/**
 * Encrypts & inserts a test card for a given user.
 *
 * @param int    $user_id
 * @param string $pan     Plain‐text PAN (card number)
 * @param string $cvc     3-digit CVC
 * @param int    $m       Expiry month
 * @param int    $y       Expiry year
 */
function addCard($user_id, $pan, $cvc, $m, $y) {
    global $conn;
    $enc = encrypt($pan);
    $stmt = $conn->prepare("
        INSERT INTO bank_accounts
            (user_id, card_number, cvc, expire_month, expire_year)
        VALUES (?, ?, ?, ?, ?)
    ");
    // i = integer, s = string (enc yields binary data okay in VARBINARY)
    $stmt->bind_param('issii', $user_id, $enc, $cvc, $m, $y);
    if (! $stmt->execute()) {
        echo "Error inserting user $user_id: " . $stmt->error . "\n";
    } else {
        echo "Inserted card ending in " . substr($pan, -4) . " for user ID $user_id\n";
    }
    $stmt->close();
}

// ——————————————————————————
// Insert five test cards:
addCard(1, '4111111111111111', '123', 12, 2025);
addCard(2, '4012888888881881', '369',  3, 2028);
addCard(3, '5500000000000004', '456', 11, 2030);
addCard(4, '5105105105105100', '258',  9, 2026);
addCard(5, '3782822463100058', '159',  4, 2027);

echo "✅ All test cards inserted.\n";

$conn->close();
