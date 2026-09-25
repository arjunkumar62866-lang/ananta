<?php
ob_start();
session_start();
require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];
$selectedCurrency = getUserCurrency();

// Fetch single source of truth income wallet summary & date/type filtered history
$incomeSummary = getUserIncomeWalletSummary($userid, $pdo);

$fromDate   = $_GET['from_date'] ?? '';
$toDate     = $_GET['to_date'] ?? '';
$incomeType = $_GET['income_type'] ?? '';

$history = getUserIncomeWalletHistory($userid, $incomeType, $fromDate, $toDate, $pdo);

$typeLabels = [
    'PROFIT_INCOME'    => 'Profit Income',
    'PROFIT_SHARING'   => 'Profit Sharing',
    'DIRECT_BONUS'     => 'Direct Bonus',
    'MENTOR_INCOME'    => 'Mentor Income',
    'RANK_REWARD'      => 'Rank Reward',
    'VIP_CLUB'         => 'VIP Club Income',
    'COMPANY_TURNOVER' => 'Company Turnover Income'
];
?>

<style>
/* =========================================================
   INCOME WALLET - UI ONLY
   Green + Black + Purple Theme
   ========================================================= */

.income-wallet-page {
    background: #f7f8f6 !important;
    min-height: 100vh;
    color: #111827;
}

/* ---------- Page Header ---------- */

.iw-page-header {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 22px;
    margin-bottom: 25px;
}

.iw-page-title {
    color: #111827;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 25px;
    font-weight: 800;
    margin: 0 0 5px;
    letter-spacing: -0.4px;
}

.iw-page-title i {
    color: #7c3aed;
    margin-right: 8px;
}

.iw-page-subtitle {
    color: #6b7280 !important;
    font-size: 13px;
    margin: 0;
}

.iw-breadcrumb {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 3px 12px rgba(17, 24, 39, 0.04);
}

.iw-breadcrumb a {
    color: #7c3aed;
    text-decoration: none;
}

.iw-breadcrumb .slash {
    color: #9ca3af;
}

.iw-breadcrumb .current {
    color: #111827;
}

/* ---------- Hero ---------- */

.iw-hero {
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    margin-bottom: 26px;
    background:
        radial-gradient(circle at 8% 20%, rgba(34, 197, 94, 0.25), transparent 28%),
        radial-gradient(circle at 85% 10%, rgba(124, 58, 237, 0.38), transparent 30%),
        linear-gradient(135deg, #111827 0%, #18121f 45%, #24103b 100%);
    box-shadow: 0 18px 45px rgba(17, 24, 39, 0.16);
    border: 1px solid rgba(124, 58, 237, 0.25);
}

.iw-hero::before {
    content: "";
    position: absolute;
    width: 220px;
    height: 220px;
    right: -90px;
    top: -100px;
    background: rgba(124, 58, 237, 0.22);
    border-radius: 50%;
    filter: blur(5px);
}

.iw-hero::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    left: 30%;
    bottom: -140px;
    background: rgba(34, 197, 94, 0.15);
    border-radius: 50%;
    filter: blur(8px);
}

.iw-hero-inner {
    position: relative;
    z-index: 2;
    padding: 30px;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 25px !important;
}

.iw-hero-left {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 24px !important;
    min-width: 0 !important;
    flex: 1 !important;
    position: relative !important;
}

.iw-hero-icon {
    width: 64px !important;
    height: 64px !important;
    min-width: 64px !important;
    max-width: 64px !important;
    flex-shrink: 0 !important;
    position: relative !important;
    left: auto !important;
    top: auto !important;
    right: auto !important;
    bottom: auto !important;
    float: none !important;
    margin: 0 !important;
    border-radius: 18px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: linear-gradient(
        135deg,
        rgba(34, 197, 94, 0.25),
        rgba(124, 58, 237, 0.3)
    ) !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.12),
        0 8px 20px rgba(0, 0, 0, 0.2) !important;
}

.iw-hero-icon i {
    color: #ffffff !important;
    font-size: 30px !important;
}

.iw-hero-content {
    min-width: 0 !important;
    flex: 1 !important;
    position: relative !important;
    left: auto !important;
    top: auto !important;
    float: none !important;
    margin: 0 !important;
    padding: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    justify-content: center !important;
}

.iw-hero-label {
    display: block !important;
    position: relative !important;
    left: auto !important;
    top: auto !important;
    float: none !important;
    margin: 0 0 6px 0 !important;
    padding: 0 !important;
    color: rgba(255, 255, 255, 0.75) !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 1.1px !important;
    line-height: 1.4 !important;
}

.iw-hero-amount {
    display: block !important;
    position: relative !important;
    left: 0 !important;
    top: 0 !important;
    right: auto !important;
    bottom: auto !important;
    float: none !important;
    clear: both !important;
    margin: 0 !important;
    padding: 0 !important;
    color: #ffffff !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 36px !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
    letter-spacing: -0.5px !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
    white-space: normal !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.iw-hero-status {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
    margin-top: 8px !important;
    position: relative !important;
    left: auto !important;
    top: auto !important;
    float: none !important;
    color: #86efac !important;
    font-size: 12px !important;
    font-weight: 600 !important;
}

.iw-status-dot {
    width: 7px;
    height: 7px;
    background: #22c55e;
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
}

.iw-hero-btn-wrap {
    flex-shrink: 0;
    width: auto;
}

.iw-growth-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    padding: 0 20px;
    color: #ffffff !important;
    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 12px;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    box-shadow: 0 8px 22px rgba(124, 58, 237, 0.32);
    transition: all 0.2s ease;
}

.iw-growth-btn:hover {
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(124, 58, 237, 0.42);
}

.iw-growth-btn i {
    font-size: 17px;
}

/* ---------- Income Cards ---------- */

.iw-income-card {
    height: 100%;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 17px;
    padding: 19px;
    box-shadow: 0 5px 18px rgba(17, 24, 39, 0.045);
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.iw-income-card::after {
    content: "";
    position: absolute;
    right: -25px;
    bottom: -25px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(124, 58, 237, 0.035);
}

.iw-income-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(17, 24, 39, 0.09);
    border-color: #d8b4fe;
}

.iw-income-top {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 15px;
    min-width: 0;
}

.iw-income-icon {
    width: 39px;
    height: 39px;
    min-width: 39px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
}

.iw-income-icon i {
    font-size: 18px;
}

.iw-icon-green {
    background: linear-gradient(135deg, #16a34a, #22c55e);
}

.iw-icon-blue {
    background: linear-gradient(135deg, #0284c7, #0ea5e9);
}

.iw-icon-purple {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
}

.iw-icon-orange {
    background: linear-gradient(135deg, #d97706, #f59e0b);
}

.iw-icon-pink {
    background: linear-gradient(135deg, #db2777, #ec4899);
}

.iw-icon-indigo {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
}

.iw-icon-teal {
    background: linear-gradient(135deg, #0f766e, #14b8a6);
}

.iw-income-name {
    color: #374151;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.iw-income-amount {
    color: #111827;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 19px;
    font-weight: 800;
    margin: 0;
    line-height: 1.25;
}

/* ---------- Section Card ---------- */

.iw-section-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    box-shadow: 0 5px 20px rgba(17, 24, 39, 0.045);
    overflow: hidden;
}

.iw-section-header {
    padding: 18px 21px;
    border-bottom: 1px solid #eef0f2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.iw-section-title {
    margin: 0;
    color: #111827;
    font-size: 15px;
    font-weight: 800;
}

.iw-section-title i {
    color: #7c3aed;
    margin-right: 8px;
}

.iw-record-badge {
    padding: 7px 12px;
    border-radius: 20px;
    background: rgba(124, 58, 237, 0.08);
    color: #7c3aed;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
}

/* ---------- Filter ---------- */

.iw-filter-body {
    padding: 22px;
}

.iw-filter-label {
    display: block;
    margin-bottom: 7px;
    color: #374151;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.iw-form-control {
    height: 46px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 10px !important;
    color: #111827 !important;
    background: #ffffff !important;
    font-size: 13px !important;
    box-shadow: none !important;
}

.iw-form-control:focus {
    border-color: #7c3aed !important;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.09) !important;
}

.iw-filter-btn {
    height: 46px;
    width: 100%;
    border: 0;
    border-radius: 10px;
    background: linear-gradient(135deg, #111827 0%, #25202e 100%);
    color: #ffffff;
    font-size: 13px;
    font-weight: 800;
    box-shadow: 0 6px 16px rgba(17, 24, 39, 0.14);
    transition: all 0.2s ease;
}

.iw-filter-btn:hover {
    color: #ffffff;
    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
}

.iw-reset-btn {
    height: 46px;
    min-width: 88px;
    padding: 0 15px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #374151;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    font-size: 12px;
    font-weight: 800;
}

.iw-reset-btn:hover {
    color: #7c3aed;
    border-color: #c4b5fd;
    background: #faf5ff;
}

/* ---------- Table ---------- */

.iw-table-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(17, 24, 39, 0.045);
}

.iw-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.iw-table {
    width: 100%;
    min-width: 950px;
    margin: 0;
    color: #111827;
}

.iw-table thead {
    background: #111827;
    color: #ffffff;
}

.iw-table thead th {
    border: 0 !important;
    padding: 14px 14px !important;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.iw-table tbody td {
    padding: 15px 14px !important;
    border-top: 1px solid #f1f5f9 !important;
    vertical-align: middle !important;
    font-size: 13px;
}

.iw-table tbody tr {
    transition: background 0.15s ease;
}

.iw-table tbody tr:hover {
    background: #fafafa;
}

.iw-row-number {
    color: #6b7280;
    font-weight: 800;
}

.iw-record-id {
    color: #7c3aed;
    font-weight: 800;
}

.iw-category {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 20px;
    background: rgba(124, 58, 237, 0.09);
    color: #7c3aed;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

.iw-amount {
    color: #16a34a;
    font-weight: 800;
    white-space: nowrap;
}

.iw-subject {
    color: #374151;
    font-weight: 600;
}

.iw-date {
    color: #6b7280;
    font-size: 12px;
    white-space: nowrap;
}

.iw-status-success {
    display: inline-flex;
    padding: 6px 10px;
    border-radius: 20px;
    background: rgba(22, 163, 74, 0.10);
    color: #15803d;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

.iw-status-pending {
    display: inline-flex;
    padding: 6px 10px;
    border-radius: 20px;
    background: rgba(234, 179, 8, 0.12);
    color: #a16207;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

.iw-empty {
    padding: 55px 20px !important;
    text-align: center;
    color: #6b7280;
}

.iw-empty i {
    display: block;
    margin-bottom: 10px;
    color: #c4b5fd;
}

/* ---------- Mobile ---------- */

@media (max-width: 767.98px) {

    .income-wallet-page {
        padding-top: 15px !important;
    }

    .iw-page-header {
        margin-bottom: 18px;
        padding-bottom: 17px;
    }

    .iw-page-title {
        font-size: 21px;
    }

    .iw-page-subtitle {
        font-size: 12px;
        line-height: 1.5;
    }

    .iw-breadcrumb {
        margin-top: 8px;
        font-size: 11px;
        padding: 6px 10px;
    }

    .iw-hero {
        border-radius: 19px;
    }

    .iw-hero-inner {
        padding: 22px 18px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 18px !important;
    }

    .iw-hero-left {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 14px !important;
        width: 100% !important;
        min-width: 0 !important;
    }

    .iw-hero-icon {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        max-width: 52px !important;
        border-radius: 14px !important;
    }

    .iw-hero-icon i {
        font-size: 24px !important;
    }

    .iw-hero-content {
        width: 100% !important;
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
    }

    .iw-hero-label {
        font-size: 10px !important;
        letter-spacing: 0.8px !important;
        margin-bottom: 5px !important;
    }

    .iw-hero-amount {
        font-size: 28px !important;
        line-height: 1.25 !important;
        letter-spacing: -0.5px !important;
        color: #ffffff !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
        left: 0 !important;
        top: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        overflow: visible !important;
        white-space: normal !important;
    }

    .iw-hero-status {
        font-size: 11px !important;
        margin-top: 6px !important;
    }

    .iw-hero-btn-wrap {
        width: 100% !important;
    }

    .iw-growth-btn {
        width: 100% !important;
        min-height: 44px !important;
        font-size: 12px !important;
    }

    .iw-income-card {
        padding: 15px;
        border-radius: 15px;
    }

    .iw-income-top {
        gap: 8px;
        margin-bottom: 12px;
    }

    .iw-income-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 10px;
    }

    .iw-income-icon i {
        font-size: 15px;
    }

    .iw-income-name {
        font-size: 11px;
    }

    .iw-income-amount {
        font-size: 16px;
    }

    .iw-section-header {
        padding: 15px;
        align-items: flex-start;
    }

    .iw-section-title {
        font-size: 13px;
        line-height: 1.4;
    }

    .iw-record-badge {
        font-size: 9px;
        padding: 6px 9px;
    }

    .iw-filter-body {
        padding: 16px;
    }

    .iw-filter-body .form-group {
        margin-bottom: 13px !important;
    }

    .iw-filter-btn {
        margin-bottom: 8px;
    }

    .iw-reset-btn {
        width: 100%;
    }

    .iw-table-card {
        border-radius: 15px;
    }

    .iw-table {
        min-width: 900px;
    }
}

/* ---------- Small Mobile ---------- */

@media (max-width: 420px) {

    .iw-hero-amount {
        font-size: 24px !important;
    }

    .iw-income-name {
        font-size: 10.5px;
    }

    .iw-income-amount {
        font-size: 15px;
    }
}
</style>


<div class="content-wrapper py-4 income-wallet-page">
    <div class="container-fluid">

        <!-- =====================================================
             PAGE HEADER
             ===================================================== -->
        <div class="iw-page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">

                <div class="mb-2 mb-md-0">
                    <h4 class="iw-page-title">
                        <i class="zmdi zmdi-chart"></i>
                        Income Wallet
                    </h4>

                    <p class="iw-page-subtitle">
                        Total accumulated income wallet balance across all 7 income streams and complete history statement
                    </p>
                </div>

                <nav aria-label="breadcrumb">
                    <div class="iw-breadcrumb">
                        <a href="index.php">Dashboard</a>
                        <span class="slash">/</span>
                        <span class="slash">Wallet</span>
                        <span class="slash">/</span>
                        <span class="current">Income Wallet</span>
                    </div>
                </nav>

            </div>
        </div>


        <!-- =====================================================
             TOTAL INCOME WALLET HERO
             ===================================================== -->
        <div class="iw-hero">

            <div class="iw-hero-inner">

                <!-- Left -->
                <div class="iw-hero-left">

                    <div class="iw-hero-icon">
                        <i class="zmdi zmdi-chart"></i>
                    </div>

                    <div class="iw-hero-content">

                        <span class="iw-hero-label">
                            Total Income Wallet Balance
                        </span>

                        <h1 class="iw-hero-amount">
                            <?php echo formatCurrency($incomeSummary['total_income_balance'], $selectedCurrency); ?>
                        </h1>

                        <div class="iw-hero-status">
                            <span class="iw-status-dot"></span>
                            All income streams combined
                        </div>

                    </div>

                </div>


                <!-- Growth Button -->
                <div class="iw-hero-btn-wrap">

                    <a href="user_growth.php" class="iw-growth-btn">
                        <i class="zmdi zmdi-eye"></i>
                        <span>Growth Breakdown</span>
                    </a>

                </div>

            </div>

        </div>


        <!-- =====================================================
             7 INCOME STREAM CARDS
             ===================================================== -->
        <div class="row mb-4">

            <!-- 1. Profit Income -->
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="iw-income-card">

                    <div class="iw-income-top">

                        <div class="iw-income-icon iw-icon-green">
                            <i class="zmdi zmdi-trending-up"></i>
                        </div>

                        <span class="iw-income-name">
                            Profit Income
                        </span>

                    </div>

                    <h4 class="iw-income-amount">
                        <?php echo formatCurrency($incomeSummary['profit_income'], $selectedCurrency); ?>
                    </h4>

                </div>
            </div>


            <!-- 2. Profit Sharing -->
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="iw-income-card">

                    <div class="iw-income-top">

                        <div class="iw-income-icon iw-icon-blue">
                            <i class="zmdi zmdi-accounts-alt"></i>
                        </div>

                        <span class="iw-income-name">
                            Profit Sharing
                        </span>

                    </div>

                    <h4 class="iw-income-amount">
                        <?php echo formatCurrency($incomeSummary['profit_sharing'], $selectedCurrency); ?>
                    </h4>

                </div>
            </div>


            <!-- 3. Direct Bonus -->
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="iw-income-card">

                    <div class="iw-income-top">

                        <div class="iw-income-icon iw-icon-purple">
                            <i class="zmdi zmdi-account-add"></i>
                        </div>

                        <span class="iw-income-name">
                            Direct Bonus
                        </span>

                    </div>

                    <h4 class="iw-income-amount">
                        <?php echo formatCurrency($incomeSummary['direct_bonus'], $selectedCurrency); ?>
                    </h4>

                </div>
            </div>


            <!-- 4. Mentor Income -->
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="iw-income-card">

                    <div class="iw-income-top">

                        <div class="iw-income-icon iw-icon-orange">
                            <i class="zmdi zmdi-group"></i>
                        </div>

                        <span class="iw-income-name">
                            Mentor Income
                        </span>

                    </div>

                    <h4 class="iw-income-amount">
                        <?php echo formatCurrency($incomeSummary['mentor_income'], $selectedCurrency); ?>
                    </h4>

                </div>
            </div>


            <!-- 5. Rank Reward -->
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="iw-income-card">

                    <div class="iw-income-top">

                        <div class="iw-income-icon iw-icon-pink">
                            <i class="zmdi zmdi-star"></i>
                        </div>

                        <span class="iw-income-name">
                            Rank Reward
                        </span>

                    </div>

                    <h4 class="iw-income-amount">
                        <?php echo formatCurrency($incomeSummary['rank_reward'], $selectedCurrency); ?>
                    </h4>

                </div>
            </div>


            <!-- 6. VIP Club -->
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="iw-income-card">

                    <div class="iw-income-top">

                        <div class="iw-income-icon iw-icon-indigo">
                            <i class="zmdi zmdi-crown"></i>
                        </div>

                        <span class="iw-income-name">
                            VIP Club
                        </span>

                    </div>

                    <h4 class="iw-income-amount">
                        <?php echo formatCurrency($incomeSummary['vip_club'], $selectedCurrency); ?>
                    </h4>

                </div>
            </div>


            <!-- 7. Company Turnover -->
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="iw-income-card">

                    <div class="iw-income-top">

                        <div class="iw-income-icon iw-icon-teal">
                            <i class="zmdi zmdi-city-alt"></i>
                        </div>

                        <span class="iw-income-name">
                            Company Turnover
                        </span>

                    </div>

                    <h4 class="iw-income-amount">
                        <?php echo formatCurrency($incomeSummary['company_turnover'], $selectedCurrency); ?>
                    </h4>

                </div>
            </div>

        </div>


        <!-- =====================================================
             FILTER SECTION
             ===================================================== -->
        <div class="iw-section-card mb-4">

            <div class="iw-section-header">

                <h6 class="iw-section-title">
                    <i class="zmdi zmdi-filter-list"></i>
                    Income History Filter
                </h6>

            </div>


            <div class="iw-filter-body">

                <form method="GET" action="income_wallet.php">

                    <div class="form-row align-items-end">

                        <!-- Income Category -->
                        <div class="form-group col-md-3 mb-3 mb-md-0">

                            <label for="income_type" class="iw-filter-label">
                                Income Category
                            </label>

                            <select
                                class="form-control form-control-lg iw-form-control"
                                id="income_type"
                                name="income_type"
                            >

                                <option value="">
                                    All 7 Income Streams
                                </option>

                                <option value="PROFIT_INCOME"
                                    <?php echo ($incomeType === 'PROFIT_INCOME') ? 'selected' : ''; ?>>
                                    1. Profit Income
                                </option>

                                <option value="PROFIT_SHARING"
                                    <?php echo ($incomeType === 'PROFIT_SHARING') ? 'selected' : ''; ?>>
                                    2. Profit Sharing
                                </option>

                                <option value="DIRECT_BONUS"
                                    <?php echo ($incomeType === 'DIRECT_BONUS') ? 'selected' : ''; ?>>
                                    3. Direct Bonus
                                </option>

                                <option value="MENTOR_INCOME"
                                    <?php echo ($incomeType === 'MENTOR_INCOME') ? 'selected' : ''; ?>>
                                    4. Mentor Income
                                </option>

                                <option value="RANK_REWARD"
                                    <?php echo ($incomeType === 'RANK_REWARD') ? 'selected' : ''; ?>>
                                    5. Rank Reward
                                </option>

                                <option value="VIP_CLUB"
                                    <?php echo ($incomeType === 'VIP_CLUB') ? 'selected' : ''; ?>>
                                    6. VIP Club Income
                                </option>

                                <option value="COMPANY_TURNOVER"
                                    <?php echo ($incomeType === 'COMPANY_TURNOVER') ? 'selected' : ''; ?>>
                                    7. Company Turnover Income
                                </option>

                            </select>

                        </div>


                        <!-- From Date -->
                        <div class="form-group col-md-3 mb-3 mb-md-0">

                            <label for="from_date" class="iw-filter-label">
                                From Date
                            </label>

                            <input
                                type="date"
                                class="form-control form-control-lg iw-form-control"
                                id="from_date"
                                name="from_date"
                                value="<?php echo htmlspecialchars($fromDate); ?>"
                            >

                        </div>


                        <!-- To Date -->
                        <div class="form-group col-md-3 mb-3 mb-md-0">

                            <label for="to_date" class="iw-filter-label">
                                To Date
                            </label>

                            <input
                                type="date"
                                class="form-control form-control-lg iw-form-control"
                                id="to_date"
                                name="to_date"
                                value="<?php echo htmlspecialchars($toDate); ?>"
                            >

                        </div>


                        <!-- Filter -->
                        <div class="form-group col-md-3 mb-0">

                            <div class="d-flex flex-column flex-sm-row" style="gap: 8px;">

                                <button
                                    type="submit"
                                    class="iw-filter-btn"
                                >
                                    <i class="zmdi zmdi-search mr-1"></i>
                                    Filter
                                </button>


                                <?php if (!empty($fromDate) || !empty($toDate) || !empty($incomeType)): ?>

                                    <a
                                        href="income_wallet.php"
                                        class="iw-reset-btn"
                                        title="Reset Filter"
                                    >
                                        Reset
                                    </a>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <!-- =====================================================
             COMPLETE INCOME HISTORY
             ===================================================== -->
        <div class="iw-table-card">

            <div class="iw-section-header">

                <h6 class="iw-section-title">
                    <i class="zmdi zmdi-time-restore"></i>
                    Complete Income Wallet Statement
                </h6>

                <span class="iw-record-badge">
                    Total: <?php echo count($history); ?> Records
                </span>

            </div>


            <div class="iw-table-wrapper">

                <table class="table iw-table">

                    <thead>

                        <tr>

                            <th class="text-center">
                                #
                            </th>

                            <th>
                                Record ID
                            </th>

                            <th class="text-center">
                                Income Category
                            </th>

                            <th class="text-right">
                                Amount
                            </th>

                            <th>
                                Source / Description
                            </th>

                            <th class="text-center">
                                Date & Time
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (!empty($history)): ?>

                            <?php
                            $sr = 1;

                            foreach ($history as $r):

                                $amt = (float)($r['amount'] ?? 0);

                                $amtUSD = parseInputToUSD(
                                    $amt,
                                    'INR',
                                    $pdo
                                );
                            ?>

                                <tr>

                                    <!-- Number -->
                                    <td class="text-center iw-row-number">
                                        <?php echo $sr++; ?>
                                    </td>


                                    <!-- Record ID -->
                                    <td class="iw-record-id">
                                        #<?php echo $r['id']; ?>
                                    </td>


                                    <!-- Category -->
                                    <td class="text-center">

                                        <span class="iw-category">
                                            <?php
                                            echo htmlspecialchars(
                                                $r['income_type']
                                            );
                                            ?>
                                        </span>

                                    </td>


                                    <!-- Amount -->
                                    <td class="text-right iw-amount">

                                        +<?php
                                        echo formatCurrency(
                                            $amtUSD,
                                            $selectedCurrency
                                        );
                                        ?>

                                    </td>


                                    <!-- Subject -->
                                    <td class="iw-subject">

                                        <?php
                                        echo htmlspecialchars(
                                            $r['subject']
                                        );
                                        ?>

                                    </td>


                                    <!-- Date -->
                                    <td class="text-center iw-date">

                                        <?php
                                        echo htmlspecialchars(
                                            ($r['created_date'] ?? '') .
                                            ' ' .
                                            ($r['time'] ?? '')
                                        );
                                        ?>

                                    </td>


                                    <!-- Status -->
                                    <td class="text-center">

                                        <?php
                                        if (
                                            strtoupper($r['status'] ?? '') === 'CREDITED' ||
                                            strtoupper($r['status'] ?? '') === 'ACHIEVED' ||
                                            ($r['status'] ?? '') == 1
                                        ):
                                        ?>

                                            <span class="iw-status-success">
                                                CREDITED
                                            </span>

                                        <?php else: ?>

                                            <span class="iw-status-pending">

                                                <?php
                                                echo htmlspecialchars(
                                                    strtoupper(
                                                        $r['status'] ?? 'PENDING'
                                                    )
                                                );
                                                ?>

                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="iw-empty"
                                >

                                    <i class="zmdi zmdi-chart-donut zmdi-hc-3x"></i>

                                    No income wallet records found for the selected criteria.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>


<?php include 'common/footer.php'; ?>