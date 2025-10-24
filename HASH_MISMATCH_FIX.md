# Hash Mismatch Issue - Root Cause and Fix

## Problem Summary

All donations are showing "Hash mismatch!" errors when verifying against the blockchain, even though the data appears correct.

## Root Cause

The issue was **NOT with the hash generation algorithm itself**, but with **how failed transactions were handled**:

1. **Scenario**: A donation is recorded to the blockchain successfully with certain data
2. **Data Change**: The donation data is later edited in the database (e.g., amount changes from 2000 to 12)
3. **Re-recording Attempt**: The system tries to record the donation again with the new data
4. **Transaction Fails**: The blockchain rejects the transaction because the tracking code already exists (smart contract prevents duplicates)
5. **Database Updated Anyway**: The PHP code incorrectly treated the failed transaction as successful and updated the database with:
   - A new `offchain_hash` based on the NEW data
   - The failed transaction hash
6. **Verification Fails**: When verifying, the blockchain has the OLD hash but the database has the NEW hash

### Evidence

From the logs for tracking code `CC003-2025-00002`:

**First Recording (SUCCESS):**
```
Timestamp: 2025-10-23 19:44:13
Amount: 2000.00
Hash: 0x9a53d8afcfc98a17ac9183a13a35923e17bfb5a4a999ba7c6896cae4a14a6402
TX: 0xaf50718c2972c134dff0a86f2f4297e858a7810a700265d02871363a60b294ec
Status: SUCCESS ✓
```

**Second Recording Attempt (FAILED):**
```
Timestamp: 2025-10-24 11:46:46
Amount: 12.00
Hash: 0x99a1c8820d3a360d8246ec74caa10f2b90eb47e145ccfd805fa3ed651c48c135
TX: 0x4cd5c7f5d279417474107f7237640d985101eb330d131c8013a1a413f91caadb
Status: FAILED ✗ (duplicate tracking code)
```

The database was updated with the second (failed) transaction's data, causing the mismatch.

## The Fix

### 1. **Fixed `recordDonation.js`** (Lines 95-105)

Added a check for transaction receipt status:

```javascript
// Check if transaction was successful
if (receipt.status === 0) {
  console.error("\n❌ Transaction FAILED!");
  console.error(`   TX Hash: ${tx.hash}`);
  // ... error details ...
  process.exit(1);  // Exit with error code
}
```

**Result**: Failed transactions now return a non-zero exit code, allowing PHP to detect the failure.

### 2. **Fixed `PhysicalDonationBlockchainService.php`** (Lines 79-82, 85-86)

Changed the success detection logic:

**Before:**
```php
if (strpos($outputString, 'TX Hash:') !== false || strpos($outputString, 'Success') !== false) {
    // Treat as success (WRONG - failed TXs also have TX hashes!)
}
```

**After:**
```php
// Check return code first
if ($returnCode !== 0) {
    throw new \Exception("Blockchain script failed...");
}

// Check for explicit success message
if (strpos($outputString, '🎉 Donation recorded successfully!') !== false
    || strpos($outputString, 'Donation recorded successfully!') !== false) {
    // Only treat as success if script succeeded
}
```

**Result**: Failed transactions are now properly detected and the database won't be incorrectly updated.

### 3. **Added Duplicate Prevention** (Lines 48-60)

Added a check to prevent re-recording already confirmed donations:

```php
// Check if already recorded successfully
if ($donation->blockchain_status === 'confirmed' && $donation->blockchain_tx_hash) {
    Log::warning("Donation already recorded to blockchain");
    return ['success' => false, 'error' => 'Already recorded'];
}
```

**Result**: Prevents accidental attempts to record the same donation twice.

## Fixing Existing Mismatched Donations

Run the fix script:

```bash
php fix-mismatched-donations.php
```

This script offers three options:

1. **Quick fix**: Update `offchain_hash` to match `onchain_hash` (marks as verified)
2. **Review**: Display all mismatched donations for manual review
3. **Full fix**: Fetch actual blockchain data and update database to match

**Recommended**: Use option 3 to ensure database data matches blockchain.

## Prevention

The fixes ensure that:

1. ✅ Failed transactions are properly detected
2. ✅ Database is only updated on successful blockchain transactions
3. ✅ Duplicate tracking codes cannot be re-recorded
4. ✅ All future donations will have matching hashes

## Testing

To verify the fix works:

1. Create a new donation
2. Verify it records successfully
3. Try to record it again (should fail gracefully)
4. Check that the hash verification passes

```bash
php artisan tinker
$d = App\Models\PhysicalDonation::latest()->first();
$d->verifyBlockchainIntegrity();
// Should return: ['success' => true, 'status' => 'verified']
```

## Files Modified

1. `blockchain-services/scripts/recordDonation.js` - Added transaction status check
2. `app/Services/PhysicalDonationBlockchainService.php` - Fixed success detection and added duplicate prevention

## Files Created

1. `fix-mismatched-donations.php` - Script to fix existing mismatched donations
2. `check-hash-test.php` - Diagnostic script for testing hash generation
3. `check-all-hashes.php` - Script to check all donations for mismatches
4. `blockchain-services/scripts/checkOnchainHash.js` - Query blockchain for hash data
5. `blockchain-services/scripts/decodeTx.js` - Decode transaction data
