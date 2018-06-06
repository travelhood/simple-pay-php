<?php

global $simplePay;

$orderRef = 'TO'.md5(microtime(true));

$simplePay->config->selectCurrency('HUF');
$order = $simplePay->createOrder($orderRef);
$order->products->addProduct(new \Travelhood\OtpSimplePay\Product('Product 1', 'sku123', 'Some nice product', 5000, .27));
$order->products->addProduct(new \Travelhood\OtpSimplePay\Product('Product 2', 'sku456', 'Another awesome product', 10000, .14), 5);

$order
    ->setLanguage('HU')
    ->setFirstName('Lajos')
    ->setLastName('Bencz')
    ->setEmail('lajos.bencz@travelhood.com')
    ->setPhone('0611234567')
    ->setAddress('Main street 1.')
    ->setZipCode('1000')
    ->setCity('Budapest')
    ->setState('Budapest')
    ->setCountryCode('HU')
;

?>
    <h4 class="title is-4">Products</h4>
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <thead>
        <tr>
            <th>SKU</th>
            <th>Product</th>
            <th>Info</th>
            <th>VAT</th>
            <th>Net Price</th>
            <th>Quantity</th>
            <th>Gross Sum</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($order->products as $product) : ?>
        <tr>
            <th><?= $product->getCode(); ?></th>
            <td><?= $product->getName(); ?></td>
            <td><?= $product->getInfo(); ?></td>
            <td><?= round($product->getVat()*100); ?>%</td>
            <td class="has-text-right"><?= number_format($product->getPrice(), 0, '', ' '); ?> <?= $order->getPricesCurrency() ?></td>
            <td><?= $order->products->countProduct($product); ?></td>
            <td class="has-text-right"><?= number_format($order->products->sumProduct($product), 0, '', ' ') ?> <?= $order->getPricesCurrency() ?></td>
        </tr>
        <?php endforeach ?>
        <tfoot>
        <tr>
            <td colspan="6" class="has-text-right">Gross total sum:</td>
            <th class="has-text-right"><?= number_format($order->products->sum(true), 0, '', ' ') ?> <?= $order->getPricesCurrency() ?></th>
        </tr>
        </tfoot>
        </tbody>
    </table>

    <hr/>

    <h4 class="title is-4">Order details</h4>
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <?php foreach($order->toArray() as $k=>$v) : ?>
        <?php if(is_array($v)) continue; ?>
        <tr>
            <th><?= $k ?></th>
            <td><?= $v ?></td>
        </tr>
        <?php endforeach ?>
    </table>
<?php

$liveUpdate = $simplePay->liveUpdate();

?>
<div class="has-text-right">
    <?=
    $liveUpdate->generateForm($order, null, function($formId) {
        return '<button type="submit" class="button is-primary"><i class="fa fa-money-bill-wave"></i> &nbsp; Start SimplePay Transaction</button>';
    })
    ?>
</div>