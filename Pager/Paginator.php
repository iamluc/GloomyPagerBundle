<?php

namespace Gloomy\PagerBundle\Pager;

use Gloomy\PagerBundle\Pager\Wrapper;

/**
 * A simplified version of the Zend_Paginator to avoid an hard dependy to Zend Framework
 * trying to stay compatible as much as possible, so we could revert
 */
class Paginator
{
    protected $_wrapper;

    protected $_currentPageNumber;

    protected $_itemCountPerPage;

    protected $_pageRange;

    protected $_numberOfPages;

    protected $_pages;

    public function __construct(Wrapper $wrapper)
    {
        $this->_wrapper = $wrapper;
    }

    public function setCurrentPageNumber($currentPageNumber)
    {
        $this->_currentPageNumber = $currentPageNumber;
    }

    public function getCurrentPageNumber()
    {
        if ($this->_currentPageNumber < 1) {
            $this->_currentPageNumber = 1;
        }
        elseif ($this->_currentPageNumber > ($nb = $this->getNumberOfPages())) {
            $this->_currentPageNumber = $nb;
        }

        return $this->_currentPageNumber;
    }

    public function setItemCountPerPage($itemCountPerPage)
    {
        $this->_itemCountPerPage = $itemCountPerPage;
    }

    public function getItemCountPerPage()
    {
        return $this->_itemCountPerPage;
    }

    public function setPageRange($pageRange)
    {
        $this->_pageRange = $pageRange;
    }

    public function getPageRange()
    {
        return $this->_pageRange;
    }

    public function getNumberOfPages()
    {
        $this->_numberOfPages = ceil($this->getNumberOfItems() / $this->getItemCountPerPage());
        if ( $this->_numberOfPages <= 0 ) {
            $this->_numberOfPages        = 1;
        }

        return $this->_numberOfPages;
    }

    public function getNumberOfItems()
    {
        return $this->_wrapper->count();
    }

    public function getCurrentItems()
    {
        $itemsPerPage = $this->getItemCountPerPage();
        $offset       = ($this->getCurrentPageNumber() - 1) * $itemsPerPage;

        return $this->_wrapper->getItems($offset, $itemsPerPage);
    }

    public function getCurrentItemCount()
    {
        if ($this->getCurrentPageNumber() < $this->getNumberOfPages())
        {
            return $this->getItemCountPerPage();
        }

        return $this->getNumberOfItems() - (($this->getCurrentPageNumber() - 1) * $this->getItemCountPerPage());
    }

    /**
     * From Zend\Paginator\Paginator
     */
    public function getPages()
    {
        if (is_null($this->_pages)) {
            $this->initializePages();
        }

        return $this->_pages;
    }

    public function initializePages()
    {
        $pageCount         = $this->getNumberOfPages();
        $currentPageNumber = $this->getCurrentPageNumber();
        $itemCountPerPage  = $this->getItemCountPerPage();

        $pages = new \stdClass();
        $pages->pageCount        = $pageCount;
        $pages->itemCountPerPage = $itemCountPerPage;
        $pages->first            = 1;
        $pages->current          = $currentPageNumber;
        $pages->last             = $pageCount;

        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages->previous = $currentPageNumber - 1;
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages->next = $currentPageNumber + 1;
        }

        // Pages in range
        $pages->pagesInRange     = $this->getPagesRange();
        $pages->firstPageInRange = min($pages->pagesInRange);
        $pages->lastPageInRange  = max($pages->pagesInRange);

        // Item numbers
        $pages->currentItemCount = $this->getCurrentItemCount();
        $pages->itemCountPerPage = $itemCountPerPage;
        $pages->totalItemCount   = $this->getNumberOfItems();
        $pages->firstItemNumber  = (($currentPageNumber - 1) * $this->getItemCountPerPage()) + 1;
        $pages->lastItemNumber   = $pages->firstItemNumber + $pages->currentItemCount - 1;

        $this->_pages = $pages;
    }

    /**
     * From Zend\Paginator\Paginator
     */
    public function getPagesRange()
    {
        $pageRange  = $this->getPageRange();
        $pageNumber = $this->getCurrentPageNumber();
        $pageCount  = $this->getNumberOfPages();

        if ($pageRange > $pageCount) {
            $pageRange = $pageCount;
        }

        $delta = ceil($pageRange / 2 );

        if ($pageNumber - $delta > $pageCount - $pageRange) {
            $lowerBound = $pageCount - $pageRange + 1;
            $upperBound = $pageCount;
        } else {
            if ($pageNumber - $delta < 0) {
                $delta = $pageNumber;
            }

            $offset     = $pageNumber - $delta;
            $lowerBound = $offset + 1;
            $upperBound = $offset + $pageRange;
        }

        $pages = array();
        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        return $pages;
    }
}