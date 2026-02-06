# Tax Repartition Lines API Documentation

## Overview
The Tax API now supports repartition lines for both invoices and refunds. Repartition lines define how taxes are distributed across different accounts.

## Requirements

### Minimum Structure
- **At least 2 lines required** for both invoice and refund repartition lines
- **Exactly 1 BASE line** required for both invoice and refund
- **At least 1 TAX line** required for both invoice and refund

### Validation Rules
1. Invoice and refund must have the same number of lines
2. Lines must match in type (BASE/TAX) and order
3. Lines must match in percentage order
4. Total positive factor percentages must equal 100%
5. Total negative factor percentages (if any) must equal -100%

## API Structure

### Creating a Tax with Repartition Lines

```json
POST /api/v1/accounts/taxes

{
  "name": "VAT 20%",
  "type_tax_use": "sale",
  "amount_type": "percent",
  "amount": 20.00,
  "tax_group_id": 1,
  "company_id": 1,
  "is_active": true,
  "invoice_repartition_lines": [
    {
      "repartition_type": "base",
      "factor_percent": null,
      "account_id": null,
      "use_in_tax_closing": false
    },
    {
      "repartition_type": "tax",
      "factor_percent": 100.00,
      "account_id": 1,
      "use_in_tax_closing": false
    }
  ],
  "refund_repartition_lines": [
    {
      "repartition_type": "base",
      "factor_percent": null,
      "account_id": null,
      "use_in_tax_closing": false
    },
    {
      "repartition_type": "tax",
      "factor_percent": 100.00,
      "account_id": 1,
      "use_in_tax_closing": false
    }
  ]
}
```

### Updating a Tax with Repartition Lines

```json
PUT /api/v1/accounts/taxes/{id}

{
  "name": "VAT 20% - Updated",
  "type_tax_use": "sale",
  "amount_type": "percent",
  "amount": 20.00,
  "tax_group_id": 1,
  "invoice_repartition_lines": [
    {
      "repartition_type": "base",
      "factor_percent": null,
      "account_id": null,
      "use_in_tax_closing": false
    },
    {
      "repartition_type": "tax",
      "factor_percent": 50.00,
      "account_id": 1,
      "use_in_tax_closing": false
    },
    {
      "repartition_type": "tax",
      "factor_percent": 50.00,
      "account_id": 2,
      "use_in_tax_closing": false
    }
  ],
  "refund_repartition_lines": [
    {
      "repartition_type": "base",
      "factor_percent": null,
      "account_id": null,
      "use_in_tax_closing": false
    },
    {
      "repartition_type": "tax",
      "factor_percent": 50.00,
      "account_id": 1,
      "use_in_tax_closing": false
    },
    {
      "repartition_type": "tax",
      "factor_percent": 50.00,
      "account_id": 2,
      "use_in_tax_closing": false
    }
  ]
}
```

### Retrieving Tax with Repartition Lines

```bash
GET /api/v1/accounts/taxes/{id}?include=invoiceRepartitionLines,refundRepartitionLines
```

**Response:**

```json
{
  "data": {
    "id": 1,
    "name": "VAT 20%",
    "type_tax_use": "sale",
    "amount_type": "percent",
    "amount": 20.00,
    "tax_group_id": 1,
    "invoiceRepartitionLines": [
      {
        "id": 1,
        "repartition_type": "base",
        "factor_percent": null,
        "account_id": null,
        "document_type": "invoice",
        "sort": 0,
        "use_in_tax_closing": false
      },
      {
        "id": 2,
        "repartition_type": "tax",
        "factor_percent": 100.00,
        "account_id": 1,
        "document_type": "invoice",
        "sort": 1,
        "use_in_tax_closing": false
      }
    ],
    "refundRepartitionLines": [
      {
        "id": 3,
        "repartition_type": "base",
        "factor_percent": null,
        "account_id": null,
        "document_type": "refund",
        "sort": 0,
        "use_in_tax_closing": false
      },
      {
        "id": 4,
        "repartition_type": "tax",
        "factor_percent": 100.00,
        "account_id": 1,
        "document_type": "refund",
        "sort": 1,
        "use_in_tax_closing": false
      }
    ]
  }
}
```

## Field Descriptions

### Repartition Line Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `repartition_type` | enum | Yes | Type of repartition line: `base` or `tax` |
| `factor_percent` | decimal | No | Percentage factor for TAX lines (null for BASE lines) |
| `account_id` | integer | No | Account ID for TAX lines (null for BASE lines) |
| `use_in_tax_closing` | boolean | No | Whether to use this line in tax closing |

### Repartition Type Values
- `base`: Base amount line (no account or percentage)
- `tax`: Tax repartition line (requires account and percentage)

## Validation Examples

### ✅ Valid Example: Simple Tax
```json
{
  "invoice_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 100, "account_id": 1}
  ],
  "refund_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 100, "account_id": 1}
  ]
}
```

### ✅ Valid Example: Split Tax
```json
{
  "invoice_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 60, "account_id": 1},
    {"repartition_type": "tax", "factor_percent": 40, "account_id": 2}
  ],
  "refund_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 60, "account_id": 1},
    {"repartition_type": "tax", "factor_percent": 40, "account_id": 2}
  ]
}
```

### ❌ Invalid Example: Missing BASE line
```json
{
  "invoice_repartition_lines": [
    {"repartition_type": "tax", "factor_percent": 100, "account_id": 1}
  ],
  "refund_repartition_lines": [
    {"repartition_type": "tax", "factor_percent": 100, "account_id": 1}
  ]
}
```
**Error:** Must have exactly 1 BASE line for invoice and refund

### ❌ Invalid Example: Percentages don't add up
```json
{
  "invoice_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 60, "account_id": 1}
  ],
  "refund_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 60, "account_id": 1}
  ]
}
```
**Error:** Total positive factors must equal 100%

### ❌ Invalid Example: Structure mismatch
```json
{
  "invoice_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 100, "account_id": 1}
  ],
  "refund_repartition_lines": [
    {"repartition_type": "base"},
    {"repartition_type": "tax", "factor_percent": 60, "account_id": 1},
    {"repartition_type": "tax", "factor_percent": 40, "account_id": 2}
  ]
}
```
**Error:** Invoice and refund must have the same number of lines

## Notes

1. **BASE lines** are used to identify the base amount for tax calculation
   - Must not have `account_id` or `factor_percent`
   - Exactly 1 required per document type

2. **TAX lines** define how the tax amount is distributed
   - Must have `account_id` and `factor_percent`
   - At least 1 required per document type
   - Factor percentages must sum to 100%

3. **Order matters**: The order of lines is preserved via the `sort` field

4. **Symmetry required**: Invoice and refund structures must match in type, count, and order

5. **Update behavior**: When updating a tax, all existing repartition lines are deleted and recreated

6. **Validation timing**: Repartition line validation occurs after creation/update via `TaxPartition::validateRepartitionLines()`
