# Ananta — Legacy Rank Reward vs. 10-Level Rank Reward + VIP Club + Company Turnover System Audit

**Audit Date:** October 12, 2026  
**Audit Scope:** Public HTML user panel (`dashboard/user1/`), admin panel (`dashboard/admin/`), shared utilities (`db_method.php`, `tree_method.php`), database tables, and closing workflows.  
**Mode:** Phase 1 Read-Only Comprehensive Audit (No code or production/local database modifications performed).

---

## Executive Summary

1. **Legacy Rank System Status:**  
   The legacy X1–X5 / Manager–Director system (`tbl_rewardlevel`, `rank_reward()`) has already been deactivated or stubbed out (`rank_reward()` returns `false` in `dashboard/user1/common/db_method.php`). The legacy page `dashboard/user1/reward_income.php` contains a redirect to `vip-club.php`.
2. **Current VIP Club Status:**  
   A 10-level VIP Club engine (`evaluateUserVIPQualifications`, `tbl_vip_level_config`, `tbl_vip_user_qualification`, `tbl_vip_monthly_schedule`) is implemented and active.
3. **Critical Discrepancies Found Against the Canonical Specification:**
   - **Rank Names:** Existing database uses generic names (`VIP Level 1` to `VIP Level 10`), whereas the canonical specification mandates: **PRIME, PRO, ELITE, CROWN, ROYAL, TITAN, PEAK, APEX, ICON, ANANT**.
   - **Business Threshold Errors in Database (`tbl_vip_level_config`):**  
     - TITAN (Level 6): Stored as `$30,000,000` (legacy INR artifact), canonical is **$300,000**.
     - PEAK (Level 7): Stored as `$100,000,000`, canonical is **$1,000,000**.
     - APEX (Level 8): Stored as `$160,000,000`, canonical is **$2,000,000**.
     - ICON (Level 9): Stored as `$260,000,000`, canonical is **$4,000,000**; reward is stored as `$150,000`, canonical is **$170,000**.
     - ANANT (Level 10): Stored as `$750,000,000`, canonical is **$10,000,000**.
     - Monthly Repeat Business for TITAN (Level 6): Stored as `$30,000,000`, canonical is **$100,000**.
   - **Wallet Crediting Conflict:**  
     Currently, `evaluateUserVIPQualifications()` credits the **one-time rank reward into `vip_club_wallet`** instead of **`rank_reward_wallet`**.  
     Under the canonical 7-stream architecture:
     - One-time Rank Rewards must credit **`rank_reward_wallet`**.
     - Monthly VIP weaker-leg income must credit **`vip_club_wallet`**.
     - Company Turnover dividends must credit **`company_turnover_wallet`**.
   - **Company Turnover Allocation Ambiguity:**  
     In `processVIPMonthlyIncome()`, PEAK, APEX, ICON, and ANANT users receive a flat `0.005 * totalCompanyTurnoverUsd` individually. In a multi-user qualified environment, this creates over-distribution unless explicitly clarified as pool-based vs. individual flat share.

---

## 1. Canonical 10-Rank Specification vs. Current Database Configuration

### Canonical Specification
Each leg (Left & Right) must independently satisfy both the Minimum Active IDs and Business Amount.

| Level | Rank Name | Min IDs / Leg | Business / Leg ($) | One-Time Reward ($) | VIP Weaker-Leg Rate | Monthly Repeat Biz ($) | Company Turnover Share |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| 1 | **PRIME** | 30 | $4,000 | $200 | 0.5% | $4,000 | None |
| 2 | **PRO** | 60 | $10,000 | $500 | 1.0% | $10,000 | None |
| 3 | **ELITE** | 100 | $16,000 | $800 | 1.0% | $16,000 | None |
| 4 | **CROWN** | 200 | $30,000 | $2,000 | 1.0% | $30,000 | None |
| 5 | **ROYAL** | 500 | $100,000 | $4,000 | 1.0% | $100,000 | None |
| 6 | **TITAN** | 1,200 | $300,000 | $10,000 | 1.0% | $100,000 | None |
| 7 | **PEAK** | 5,000 | $1,000,000 | $30,000 | 0.5% | $100,000 | 0.5% |
| 8 | **APEX** | 12,000 | $2,000,000 | $70,000 | 0.5% | $100,000 | 0.5% |
| 9 | **ICON** | 20,000 | $4,000,000 | $170,000 | 0.5% | $100,000 | 0.5% |
| 10 | **ANANT** | 50,000 | $10,000,000 | $350,000 | 0.5% | $100,000 | 0.5% |

### Current Stored Values in `tbl_vip_level_config` (Read from Database)
| Level | Stored Name | IDs / Leg | Stored Left/Right Biz ($) | Stored Reward ($) | Stored VIP Rate | Stored Monthly Repeat ($) | Discrepancy Flag |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: | :--- |
| 1 | VIP Level 1 | 30 | 4,000.00 | 200.00 | 0.50% | 4,000.00 | Name mismatch (should be PRIME) |
| 2 | VIP Level 2 | 60 | 10,000.00 | 500.00 | 1.00% | 10,000.00 | Name mismatch (should be PRO) |
| 3 | VIP Level 3 | 100 | 16,000.00 | 800.00 | 1.00% | 16,000.00 | Name mismatch (should be ELITE) |
| 4 | VIP Level 4 | 200 | 30,000.00 | 2,000.00 | 1.00% | 30,000.00 | Name mismatch (should be CROWN) |
| 5 | VIP Level 5 | 500 | 100,000.00 | 4,000.00 | 1.00% | 100,000.00 | Name mismatch (should be ROYAL) |
| 6 | VIP Level 6 | 1,200 | **30,000,000.00** | 10,000.00 | 1.00% | **30,000,000.00** | **CRITICAL: Business is 100x too high; Repeat is 300x too high** |
| 7 | VIP Level 7 | 5,000 | **100,000,000.00** | 30,000.00 | 0.50% | 100,000.00 | **CRITICAL: Business is 100x too high** |
| 8 | VIP Level 8 | 12,000 | **160,000,000.00** | 70,000.00 | 0.50% | 100,000.00 | **CRITICAL: Business is 80x too high** |
| 9 | VIP Level 9 | 20,000 | **260,000,000.00** | **150,000.00** | 0.50% | 100,000.00 | **CRITICAL: Biz 65x too high; Reward $150k vs $170k** |
| 10 | VIP Level 10 | 50,000 | **750,000,000.00** | 350,000.00 | 0.50% | 100,000.00 | **CRITICAL: Business is 75x too high** |

---

## 2. Architecture & Data Flow Audit

### Current Call Graph & Data Flow
1. **User Dashboard Hit (`dashboard/user1/index.php` line 166):**
   - Calls `evaluateUserVIPQualifications($userid, $pdo)`.
   - Computes left/right branch totals via `getBinaryLegDetails($user_id, $db)`.
   - Compares leg metrics against `tbl_vip_level_config`.
   - If conditions met and not yet recorded in `tbl_vip_user_qualification`:
     - Inserts record into `tbl_vip_user_qualification`.
     - *Issue:* Updates `vip_club_wallet` instead of `rank_reward_wallet`.
     - Inserts `tbl_transaction` with subject `"VIP Club Level {lvl} Reward ($ {amt})"`.
2. **User VIP Page (`dashboard/user1/vip-club.php` & `get_vip_club.php`):**
   - `get_vip_club.php` calls `evaluateUserVIPQualifications($userid, $pdo)`.
   - Dynamically checks selected currency (`$_SESSION['currency']` or `$_REQUEST['currency']`).
   - Converts and formats currency using `convertCurrency()` and `formatCurrency()`.
   - Returns JSON containing `leg_details`, `configs`, `qualifications`, and `monthly_payouts`.
   - The UI displays progress bars, ID counts, business requirements, and history tables.
3. **Admin Controls (`dashboard/admin/rank_settings.php`, `vip-club.php`, `vip_club_action.php`):**
   - `rank_settings.php` reads `tbl_vip_level_config` and displays the matrix.
   - `vip-club.php` provides manual evaluation, balance adjustment modal, and monthly closing trigger.
   - `vip_club_action.php` handles AJAX endpoints:
     - `get_vip_configs`: Fetches levels.
     - `evaluate_qualifications`: Runs evaluation for an input `user_id`.
     - `get_qualifications`: Reads `tbl_vip_user_qualification`.
     - `get_schedules`: Reads `tbl_vip_monthly_schedule`.
     - `trigger_11th_closing`: Calls `processVIPMonthlyIncome()`.
4. **Monthly Closing Action (`dashboard/admin/monthly_closing_action.php` line 358):**
   - Calls `processVIPMonthlyIncome($closing_month, $closing_date, $pdo)`.
   - Enforces 11th day of the month rule.
   - Checks weaker-leg repeat business requirement.
   - Calculates weaker leg payout and company turnover payout.
   - Inserts record into `tbl_vip_monthly_schedule`.
   - Credits `vip_club_wallet` and `amount` (Net balance).

---

## 3. Wallet Architecture & Seven-Stream Segregation

Under the platform's standard accounting schema:
- **`user_growth_wallet`** = Sum of 7 distinct income streams:
  1. `profit_income_wallet`
  2. `profit_sharing_wallet`
  3. `direct_bonus_wallet`
  4. `mentor_income_wallet`
  5. `rank_reward_wallet` (One-Time Rank Reward)
  6. `vip_club_wallet` (Monthly Weaker-Leg Payout)
  7. `company_turnover_wallet` (Global Company Turnover Share)

### Identified Bug in Current Code
In `evaluateUserVIPQualifications()`:
```php
$updWallet = $db->prepare("UPDATE user SET amount = amount + :amt, vip_club_wallet = vip_club_wallet + :amt, user_growth_wallet = user_growth_wallet + :amt WHERE userid = :uid");
```
One-time rank rewards are currently conflated into `vip_club_wallet`.  
**Required Fix in Phase 3:**
- One-time Rank Rewards must update `rank_reward_wallet` (and update user's `rank` / `rankid`).
- VIP Monthly Income must update `vip_club_wallet`.
- Company Turnover Income must update `company_turnover_wallet`.

---

## 4. Company Turnover Calculation & Ambiguities

In `processVIPMonthlyIncome()`:
```php
$stmtTurnover = $db->query("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_one");
$companyTurnoverInr = (float)$stmtTurnover->fetchColumn();
$companyTurnoverUsd = round($companyTurnoverInr / 90.0, 2);
...
$hasTurnoverShare = ((int)$c['has_turnover_share'] === 1);
$turnoverRate = $hasTurnoverShare ? 0.50 : 0.00;
$turnoverPayout = $hasTurnoverShare ? round($companyTurnoverUsd * 0.005, 2) : 0.00;
```
### Ambiguities Identified:
1. **Turnover Period:**
   - Currently, `SELECT SUM(package) FROM tbl_roi_one` takes the lifetime sum rather than the specific closing month's turnover.
   - Standard monthly closing logic requires querying investments made within the specific closing month (`WHERE DATE_FORMAT(created_date, '%Y-%m') = :closing_month`).
2. **Allocation Pool vs. Flat Payout:**
   - Currently, every user qualifying for Level 7–10 receives `0.005 * totalTurnover`.
   - If 4 users qualify, the company would pay out `4 * 0.5% = 2.0%` of turnover.
   - *Question for Business Approval:* Is 0.5% an equal-split pool for all eligible users in that tier (e.g. `$Turnover * 0.005 / count(eligible_users)`), or a flat individual percentage?

---

## 5. Duplicate Protection & Safeguards Audit

### Existing Protections:
- `tbl_vip_user_qualification`: Queried via `chkQual->execute([':uid' => $user_id, ':lvl' => $lvl])` before inserting.
- `tbl_vip_monthly_schedule`: Queried via `chkIdem->execute([':uid' => $userId, ':lvl' => $highestLvl, ':c_month' => $closing_month])` before crediting monthly payouts.
- Date Restriction: Strictly verifies `(int)date('d', strtotime($cDate)) === 11` unless explicitly bypassed by an admin parameter.

### Missing Safeguards to Implement in Phase 3:
1. **Database-level Unique Constraints:**
   - Add `UNIQUE KEY uq_user_vip_level (user_id, vip_level)` to `tbl_vip_user_qualification` to physically guarantee idempotency.
   - Add `UNIQUE KEY uq_user_level_month (user_id, vip_level, closing_month)` to `tbl_vip_monthly_schedule`.
2. **Atomic Database Transactions:**
   - Wrap qualification credits and schedule payouts inside `PDO::beginTransaction()` / `commit()` blocks.

---

## 6. Currency Handling & USD/INR Synchronization

- All canonical qualification metrics, wallet balances, and transaction amounts in the database are stored strictly in **USD**.
- The currency switcher in `dashboard/user1/common/header.php` sets `$_SESSION['currency'] = 'USD' | 'INR'`.
- The user-side VIP Club endpoint `get_vip_club.php` and page `vip-club.php` already support dynamic currency conversion:
  - Formatted with `$ ` when USD is selected.
  - Formatted with `₹ ` (converted at 1 USD = 90 INR) when INR is selected.
- In Phase 3, this behavior will be maintained across all updated rank and reward cards.

---

## 7. Backward Compatibility & Migration Plan

### Step-by-Step Migration Strategy:
1. **Database Migration (`tbl_vip_level_config`):**
   - Update canonical rank names: `PRIME, PRO, ELITE, CROWN, ROYAL, TITAN, PEAK, APEX, ICON, ANANT`.
   - Correct business thresholds and repeat requirements for Levels 6 to 10.
   - Correct ICON reward from $150,000 to $170,000.
2. **Function Decoupling & Alignment:**
   - Refactor `evaluateUserVIPQualifications()`:
     - Update user rank (`user.rank = 'PRIME'`, `user.rankid = 1`, etc.).
     - Credit one-time reward into **`rank_reward_wallet`** (not `vip_club_wallet`).
     - Record transaction under type/subject `"Rank Reward - {RankName} ($ {amt})"`.
   - Refactor `processVIPMonthlyIncome()`:
     - Keep monthly weaker-leg income credited to **`vip_club_wallet`**.
     - Keep company turnover income credited to **`company_turnover_wallet`**.
     - Fix turnover calculation period to specific closing month.
3. **UI Synchronization:**
   - Update `dashboard/user1/vip-club.php`: Display canonical rank names, criteria, and separate breakdown cards for Rank Reward, VIP Club, and Company Turnover.
   - Update `dashboard/admin/rank_settings.php`: Reflect updated canonical matrix.
   - Update `dashboard/admin/vip-club.php`: Ensure previews and closing summaries display correct rank names and segregated wallets.
4. **Historical Data Preservation:**
   - Existing historical tables (`tbl_rewardinc`, `tbl_rewardlevel`, `tbl_transaction`) remain 100% intact.
   - No historical wallets or balances will be wiped.

---

## 8. Rollback Plan

- A SQL rollback script will be created to restore previous row values in `tbl_vip_level_config`.
- Git commits will be granular and isolated per file.
- All migrations are additive and non-destructive.

---

## 9. Verification & Stop Point

Phase 1 audit is complete. Awaiting user review and authorization to proceed with Phase 3 implementation.
