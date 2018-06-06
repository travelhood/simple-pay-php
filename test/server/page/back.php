<?php

require_once __DIR__ . '/../bootstrap.php';
global $simplePay;


$page = $simplePay->pageBack();
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

<a class="button is-danger" href="/">
    <i class="fa fa-chevron-left"></i>
    &nbsp;
    Back
</a>
