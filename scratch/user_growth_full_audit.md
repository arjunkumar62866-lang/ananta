# User Growth Wallet & 7 Income Streams — Full Database Audit Report

**Date & Time:** 2026-10-12 07:56:45 UTC
**Environment:** Isolated Audit Environment (Public HTML Database)
**Mode:** PHASE 1 — READ-ONLY (Zero DB mutations performed)

## 1. Executive Summary

| Metric | Count |
|---|---|
| **Total Users Audited** | **69** |
| **Users with No Discrepancy** | **62** |
| **Users with Positive Discrepancies** (`stored > expected`) | **1** |
| **Users with Negative Discrepancies** (`stored < expected`) | **6** |
| **Category 1: Confirmed Display-Only Issues** | **0** |
| **Category 2: Confirmed Stale/Incorrect `user_growth_wallet`** | **7** |
| **Category 3: Duplicate / Source Record Issues** | **0** |
| **Category 4: Unresolved Cases** | **0** |

**Total Proposed Discrepancy Value:** $7,330.00

## 2. Discrepancy Details (Users with Differences)

| User ID | Name | Status | 7 Streams Breakdown (PI / PS / DB / MI / RR / VIP / CT) | Expected Sum | Stored `user_growth_wallet` | Displayed User Growth | Diff (Stored - Exp) | Category | Recommended Action |
|---|---|---|---|---|---|---|---|---|---|
| `TEST_A_2069` | User A | Active | 120 / 0 / 0 / 0 / 0 / 0 / 0 | **$120** | $0 | $120 | **$-120** | Confirmed stale/incorrect user_growth_wallet | Update stored user_growth_wallet from 0 to 120 (Safe summary sync) |
| `TEST_LOCK_USER_2806` | Withdrawal Test User | Active | 1500 / 200 / 100 / 50 / 0 / 0 / 0 | **$1850** | $0 | $1850 | **$-1850** | Confirmed stale/incorrect user_growth_wallet | Update stored user_growth_wallet from 0 to 1850 (Safe summary sync) |
| `TEST_LOCK_USER_6561` | Withdrawal Test User | Active | 1500 / 200 / 100 / 50 / 0 / 0 / 0 | **$1850** | $0 | $1850 | **$-1850** | Confirmed stale/incorrect user_growth_wallet | Update stored user_growth_wallet from 0 to 1850 (Safe summary sync) |
| `TEST_LOCK_USER_4433` | Withdrawal Test User | Active | 1500 / 200 / 100 / 50 / 0 / 0 / 0 | **$1850** | $0 | $1850 | **$-1850** | Confirmed stale/incorrect user_growth_wallet | Update stored user_growth_wallet from 0 to 1850 (Safe summary sync) |
| `TEST_USR21_USERA` | Req21 User A | Active | 200 / 50 / 100 / 80 / 0 / 150 / 0 | **$580** | $0 | $580 | **$-580** | Confirmed stale/incorrect user_growth_wallet | Update stored user_growth_wallet from 0 to 580 (Safe summary sync) |
| `TEST_USR21_USERB` | Req21 User B | Active | 200 / 50 / 100 / 80 / 0 / 150 / 0 | **$580** | $0 | $580 | **$-580** | Confirmed stale/incorrect user_growth_wallet | Update stored user_growth_wallet from 0 to 580 (Safe summary sync) |
| `TEST_SAFEGUARD_U1` | SG Root | Active | 0 / 0 / 0 / 0 / 0 / 0 / 0 | **$0** | $500 | $0 | **$500** | Confirmed stale/incorrect user_growth_wallet | Update stored user_growth_wallet from 500 to 0 (Safe summary sync) |


## 3. Complete User Roster (All 69 Users)

| User ID | Status | Expected Total ($) | Stored `user_growth_wallet` ($) | Displayed ($) | Net Balance ($) | Discrepancy Category |
|---|---|---|---|---|---|---|
| `1290` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `215567` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `789260` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `224891` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `970637` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `509701` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `594401` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `773330` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `310169` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `172097` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `191660` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `596511` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `134783` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `843217` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `587327` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_CURR22_USER` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_ADMIN_CTRL_USER` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TST_MT_P1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TST_MT_U1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TST_MT_C1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TST_MT_C2` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TST_MT_P2` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TST_MT_X1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_ACT_USERA` | Active | $0 | $0 | $0 | $50 | No Discrepancy |
| `TEST_ACT_USERB` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_A_2069` | Active | $120 | $0 | $120 | $750 | Confirmed stale/incorrect user_growth_wallet |
| `TEST_LOCK_USER_2806` | Active | $1850 | $0 | $1850 | $500 | Confirmed stale/incorrect user_growth_wallet |
| `TEST_LOCK_USER_6561` | Active | $1850 | $0 | $1850 | $500 | Confirmed stale/incorrect user_growth_wallet |
| `TEST_LOCK_USER_4433` | Active | $1850 | $0 | $1850 | $500 | Confirmed stale/incorrect user_growth_wallet |
| `TEST_W1_9712` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W2_9712` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W3_9712` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W4_9712` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_PI_9712` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W1_9781` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W2_9781` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W3_9781` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W4_9781` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_PI_9781` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W1_9829` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W2_9829` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W3_9829` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_W4_9829` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_PI_9829` | Active | $0 | $0 | $0 | $1000 | No Discrepancy |
| `TEST_USR21_USERA` | Active | $580 | $0 | $580 | $1000 | Confirmed stale/incorrect user_growth_wallet |
| `TEST_USR21_USERB` | Active | $580 | $0 | $580 | $1000 | Confirmed stale/incorrect user_growth_wallet |
| `TEST_REQ23_USERA` | Active | $0 | $0 | $0 | $500 | No Discrepancy |
| `TEST_REQ23_USERB` | Active | $0 | $0 | $0 | $500 | No Discrepancy |
| `TEST_SAFEGUARD_U1` | Active | $0 | $500 | $0 | $500 | Confirmed stale/incorrect user_growth_wallet |
| `TEST_SAFEGUARD_L1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_1` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_2` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_2` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_3` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_3` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_4` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_4` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_5` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_5` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_6` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_6` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_7` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_7` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_8` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_8` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_L_SUB_9` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
| `TEST_SAFEGUARD_R_SUB_9` | Active | $0 | $0 | $0 | $0 | No Discrepancy |
