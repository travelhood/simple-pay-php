<?php

class BaseTest extends \PHPUnit\Framework\TestCase
{
    const P1 = [
        'name' => 'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP',
        'code' => 'SKU123456',
        'info' => 'Some description of the product',
        'price' => 6000,
        'vat' => .0,
    ];

    const P2 = [
        'name' => '',
        'code' => 'SKU234567',
        'info' => 'Another description',
        'price' => 2400,
        'vat' => .0,
    ];
}