<?php

require_once __DIR__ . '/../bootstrap.php';
global $simplePay;

$amount = (isset($_GET['amount'])&&$_GET['amount']) ? intval($_GET['amount']) : 55084;
$page = $simplePay->pageBack();

if(isset($_GET['idn']) && $_GET['idn']) {
    $data = $simplePay->instantDeliveryNotification($page->getSimplePayRef(), $amount)->getData();
?>
    <div class="notification is-success">
        <strong><i class="fa fa-check"></i> Successful delivery notification!</strong><br/>
    </div>
    <table class="table is-bordered is-striped is-narrow">
        <tr>
            <td>SimplePay transaction ID</td>
            <th><?= $data['REFNO'] ?></th>
        </tr>
        <tr>
            <td>Date of transaction</td>
            <th><?= $data['DATE'] ?></th>
        </tr>
    </table>
<?php
    return;
}

if(isset($_GET['irn']) && $_GET['irn']) {
    $data = $simplePay->instantRefundNotification($page->getSimplePayRef(), $amount, $amount)->getData();
    ?>
    <div class="notification is-success">
        <strong><i class="fa fa-check"></i> Successful refund notification!</strong><br/>
    </div>
    <table class="table is-bordered is-striped is-narrow">
        <tr>
            <td>SimplePay transaction ID</td>
            <th><?= $data['REFNO'] ?></th>
        </tr>
        <tr>
            <td>Date of transaction</td>
            <th><?= $data['DATE'] ?></th>
        </tr>
    </table>
    <?php
    return;
}

$status = $simplePay->instantOrderStatus($page->getOrderRef(), $page->getOrderCurrency());

?>

<?php if($page->hasError()) : ?>
    <div class="notification is-danger">
        <strong><i class="fa fa-times"></i> Unsuccessful transaction!</strong><br/>
        <br/>
        Please check the validity of data provided during the transaction. If all data are correct, please contact your bank which issued the card to request the reason for the refusal.
    </div>
<?php else : ?>
    <div class="notification is-success">
        <strong><i class="fa fa-check"></i> Successful transaction!</strong>
    </div>
<?php endif ?>

<table class="table is-bordered is-striped is-narrow">
    <tr>
        <td>SimplePay transaction ID</td>
        <th><?= $page->getSimplePayRef() ?></th>
    </tr>
    <tr>
        <td>Merchant order ID</td>
        <th><?= $page->getOrderRef() ?></th>
    </tr>
    <tr>
        <td>Date of transaction</td>
        <th><?= $page->getOrderDate() ?></th>
    </tr>
    <tr>
        <td>Order status</td>
        <th><?= $status->getOrderStatus() ?></th>
    </tr>
</table>

<hr/>

<?php if($status->getOrderStatus() == \Travelhood\OtpSimplePay\Enum\Status::PAYMENT_AUTHORIZED) : ?>
    <a class="button is-primary" href="?<?= http_build_query($_GET); ?>&idn=1">
        <i class="fa fa-check"></i>
        &nbsp;
        Send delivery notification
    </a>
    <a class="button is-warning" href="?<?= http_build_query($_GET); ?>&irn=1">
        <i class="fa fa-times"></i>
        &nbsp;
        Send refund notification
    </a>
<?php endif ?>

<a class="button is-danger" href="/">
    <i class="fa fa-chevron-left"></i>
    &nbsp;
    Back
</a>
