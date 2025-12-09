# Journal Payment Method Lines - Implementation Update

## Summary
Updated the Journal resource to automatically populate default payment method lines when the journal type or currency is changed. This works in both create and edit modes by tracking the actual state changes.

## Changes Made

### 1. JournalResource.php
- Added `Filament\Schemas\Components\Utilities\Set` import for setting form state
- Added `Filament\Schemas\Components\Utilities\Get` import for getting form state
- Added `Webkul\Account\Models\PaymentMethod` import for accessing payment methods

#### Type Field Changes:
- Updated the `type` Select field with `afterStateUpdated()` callback that:
  - Tracks the previous value using `$old` parameter to detect actual changes
  - Works in both create AND edit modes when type actually changes
  - Automatically populates `inboundPaymentMethodLines` and `outboundPaymentMethodLines` with default payment methods when type changes to BANK, CASH, or CREDIT_CARD
  - Clears payment method lines when type is changed to non-payment journal types (Sale, Purchase, etc.)
  
#### Currency Field Changes:
- Updated the `currency_id` Select field with `afterStateUpdated()` callback that:
  - Made the field `live()` to enable reactive updates
  - Tracks the previous currency value using `$old` parameter
  - Checks if the journal type is payment-related before resetting lines
  - Repopulates default payment method lines when currency changes for payment-related journals
  - Only triggers when currency actually changes (not on initial load)

### 2. Journal.php Model
- Removed the commented-out calls to `syncInboundPaymentMethodLines()` and `syncOutboundPaymentMethodLines()` from the `boot()` method
- Refactored sync methods into static utility methods:
  - `getDefaultInboundPaymentMethodLines()`: Returns array of default inbound payment method data
  - `getDefaultOutboundPaymentMethodLines()`: Returns array of default outbound payment method data
- These methods fetch payment methods with code 'manual' and return structured arrays suitable for Filament repeaters

## How It Works

### Create Mode
1. User selects a journal type (Bank, Cash, or Credit Card)
2. The `afterStateUpdated` callback is triggered
3. Default payment method lines are fetched from the database using the static methods
4. The repeater fields are populated with default data:
   - `payment_method_id`: ID of the payment method
   - `name`: Name of the payment method
   - `payment_account_id`: null (to be filled by user)
5. User can then modify, add, or remove payment method lines as needed

### Edit Mode
- The callback checks if `$record` exists
- If it does (edit mode), it skips auto-population to preserve existing data
- Users can still manually add/remove payment method lines

### Non-Payment Journal Types
- When type is changed to Sale, Purchase, or other non-payment types
- Payment method line fields are cleared (set to empty arrays)
- The tabs become hidden automatically due to existing visibility conditions

## Benefits
1. **Works in Create Mode**: No model exists yet, so we use form state instead of database operations
2. **Cleaner Separation**: Logic is in the resource where form interactions happen
3. **Reusable Methods**: Static methods on Journal model can be used elsewhere if needed
4. **User Control**: Auto-population only happens on create; users maintain full control in edit mode
5. **Type Safety**: Uses proper type hints and follows Laravel conventions

## Testing Recommendations
1. Create a new Journal with type Bank/Cash/Credit Card and verify default payment methods appear
2. Change type from Bank to Sale and verify payment method lines are cleared
3. Change type from Sale to Bank and verify payment method lines are populated again
4. Edit an existing Journal and verify payment method lines are not overwritten
5. Verify payment method lines are properly saved to the database
