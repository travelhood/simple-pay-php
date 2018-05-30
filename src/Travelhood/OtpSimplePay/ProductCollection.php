<?php

namespace Travelhood\OtpSimplePay;

use Countable;
use SeekableIterator;
use Travelhood\OtpSimplePay\Exception\ProductCollectionException;

class ProductCollection implements Countable, SeekableIterator
{
    /** @var int */
    protected $_position = 0;

    /** @var Product[] */
    protected $_collection = [];

    /** @var int[] */
    protected $_quantity = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_collection);
    }

    /**
     * @return Product
     */
    public function current()
    {
        return $this->_collection[$this->_position];
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->_position++;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->_position >= 0 && $this->_position < count($this->_collection);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * @param int $position
     */
    public function seek($position)
    {
        $this->_position = max(0, min(count($this->_collection)-1, $position));
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->_position = 0;
        $this->_collection = [];
        $this->_quantity = [];
        return $this;
    }

    /**
     * @param string $code
     * @return int
     */
    public function findProductByCode($code)
    {
        /** @var Product $product */
        foreach($this as $position=>$product) {
            if($product->getCode() == $code) {
                return $position;
            }
        }
        return -1;
    }

    /**
     * @param Product $product
     * @return int
     */
    public function findProduct(Product $product)
    {
        return $this->findProductByCode($product->getCode());
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return $this
     */
    public function addProduct(Product $product, $quantity=1)
    {
        $position = $this->findProduct($product);
        if($position >= 0) {
            $this->_quantity[$position]+= $quantity;
        }
        else {
            $this->_collection[] = $product;
            $this->_quantity[] = $quantity;
        }
        $this->_position = count($this->_collection) - 1;
        return $this;
    }

    /**
     * @param string $code
     * @param int $limit
     * @return $this
     */
    public function removeProductByCode($code, $limit=0)
    {
        $position = $this->findProductByCode($code);
        if($position >= 0) {
            if($limit==0 || $limit >= $this->_quantity[$position]) {
                array_splice($this->_collection, $position, 1);
                array_splice($this->_quantity, $position, 1);
            }
            else {
                $this->_quantity[$position]-= $limit;
            }
        }
        return $this;
    }

    /**
     * @param Product $product
     * @param int $limit
     * @return $this
     */
    public function removeProduct(Product $product, $limit=0)
    {
        return $this->removeProductByCode($product->getCode(), $limit);
    }

    /**
     * @param string $code
     * @param int $quantity
     * @return $this
     * @throws ProductCollectionException
     */
    public function setProductQuantityByCode($code, $quantity)
    {
        $position = $this->findProductByCode($code);
        if($position < 0) {
            throw new ProductCollectionException('Product code not found in collection: '.$code);
        }
        if($quantity < 1) {
            $this->removeProductByCode($code);
        }
        else {
            $this->_quantity[$position] = $quantity;
        }
        return $this;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return $this
     * @throws ProductCollectionException
     */
    public function setProductQuantity(Product $product, $quantity)
    {
        return $this->setProductQuantityByCode($product->getCode(), $quantity);
    }

    /**
     * @param string $code
     * @return int
     */
    public function countProductByCode($code)
    {
        $position = $this->findProductByCode($code);
        if($position < 0) {
            return 0;
        }
        return $this->_quantity[$position];
    }

    /**
     * @param Product $product
     * @return int
     */
    public function countProduct(Product $product)
    {
        return $this->countProductByCode($product->getCode());
    }

}