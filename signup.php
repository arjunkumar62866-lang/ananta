<?php
ob_start();
session_start();

$getParams = $_GET;
if (!empty($getParams['ref']) && empty($getParams['refferalId'])) {
    $getParams['refferalId'] = $getParams['ref'];
}
if (!empty($getParams['referral']) && empty($getParams['refferalId'])) {
    $getParams['refferalId'] = $getParams['referral'];
}
if (!empty($getParams['sponsor']) && empty($getParams['refferalId'])) {
    $getParams['refferalId'] = $getParams['sponsor'];
}
if (!empty($getParams['sponsorid']) && empty($getParams['refferalId'])) {
    $getParams['refferalId'] = $getParams['sponsorid'];
}
if (!empty($getParams['sponsor_id']) && empty($getParams['refferalId'])) {
    $getParams['refferalId'] = $getParams['sponsor_id'];
}
if (!empty($getParams['referral_code']) && empty($getParams['refferalId'])) {
    $getParams['refferalId'] = $getParams['referral_code'];
}
if (!empty($getParams['uid']) && empty($getParams['refferalId'])) {
    $getParams['refferalId'] = $getParams['uid'];
}

$queryString = !empty($getParams) ? '?' . http_build_query($getParams) : '';
header("Location: dashboard/user1/register.php" . $queryString);
exit();
?>
