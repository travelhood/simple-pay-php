<?php

namespace Travelhood\OtpSimplePay;

use Countable;
use SeekableIterator;

interface ProductCollectionInterface extends Countable, SeekableIterator
{
    /**
     * Removes all products from this list
     * @return $this
     */
    function clear();

    /**
     * Finds product instance by code (SKU)
     * @param string $code
     * @return ProductInterface
     */
    function findProductByCode($code);

    /**
     * Finds product index in list by code (SKU)
     * @param string $code
     * @return int
     */
    function findIndexByCode($code);

    /**
     * Finds product index in list by product instance
     * @param ProductInterface $product
     * @return int
     */
    function findIndex(ProductInterface $product);

    /**
     * Add product instance to list with quantity
     * If product already exists in the list, the quantity will be added
     * @param ProductInterface $product
     * @param int $quantity
     * @return $this
     */
    function addProduct(ProductInterface $product, $quantity = 1);

    /**
     * Removes product from list by code (SKU)
     * @param string $code
     * @return $this
     */
    function removeProductByCode($code);

    /**
     * Removes product from list by instance
     * @param ProductInterface $product
     * @return $this
     */
    function removeProduct(ProductInterface $product);

    /**
     * Sets quantity for product based on code (SKU)
     * @param string $code
     * @param int $quantity
     * @return $this
     */
    function setProductQuantityByCode($code, $quantity);

    /**
     * Sets quantity for product based on instance
     * @param ProductInterface $product
     * @param $quantity
     * @return $this
     */
    function setProductQuantity(ProductInterface $product, $quantity);

    /**
     * Counts product quantity by code (SKU)
     * @param string $code
     * @return int
     */
    function countProductByCode($code);

    /**
     * Counts product quantity by instance
     * @param ProductInterface $product
     * @return int
     */
    function countProduct(ProductInterface $product);

    /**
     * Sums product price by code (SKU)
     * @param string $code
     * @param bool $gross
     * @return float
     */
    function sumProductByCode($code, $gross = true);

    /**
     * Sums product price by instance
     * @param ProductInterface $product
     * @param bool $gross
     * @return float
     */
    function sumProduct(ProductInterface $product, $gross = true);

    /**
     * Sums all product prices
     * @param bool $gross
     * @return float
     */
    function sum($gross = true);

    /**
     * Returns a string representation of the product collection
     * @return string
     */
    function __toString();
}