<?php

namespace Travelhood\OtpSimplePay;

use Countable;
use SeekableIterator;

interface ProductCollectionInterface extends Countable, SeekableIterator
{
    function clear();

    function findProductByCode($code);

    function findIndexByCode($code);

    function findIndex(Product $product);

    function addProduct(Product $product, $quantity = 1);

    function removeProductByCode($code);

    function removeProduct(Product $product);

    function setProductQuantityByCode($code, $quantity);

    function setProductQuantity(Product $product, $quantity);

    function countProductByCode($code);

    function countProduct(Product $product);

    function sumProductByCode($code, $gross = true);

    function sumProduct(Product $product, $gross = true);

    function sum($gross = true);

    function __toString();
}