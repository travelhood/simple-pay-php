<?php

namespace Travelhood\OtpSimplePay;

use Countable;
use SeekableIterator;

interface ProductCollectionInterface extends Countable, SeekableIterator, HtmlizeInterface
{
    function clear();

    function findProductByCode($code);

    function findProduct(Product $product);

    function addProduct(Product $product, $quantity=1);

    function removeProductByCode($code);

    function removeProduct(Product $product);

    function setProductQuantityByCode($code, $quantity);

    function setProductQuantity(Product $product, $quantity);

    function countProductByCode($code);

    function countProduct(Product $product);

    function __toString();
}