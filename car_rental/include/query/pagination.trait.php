<?php

trait Pagination
{
    private $currentPage = 1;
    private $itemsPerPage = 8;
    private $limitClause = '';

    // total records in table
    private $totalRecords = null;

    function getTotalPages()
    {
        if ($this->totalRecords == null) {
            $this->totalRecords = $this->count();
        }

        return ceil($this->totalRecords / $this->itemsPerPage);
    }

    function hasNextPage() {
        return $this->currentPage < $this->getTotalPages();
    }

    function hasPreviousPage() {
        return $this->currentPage > 1;
    }

    function getReturnDescription()
    { /*2*/
    }

    /**
     * Get the value of currentPage
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return static
     */
    public function setCurrentPage($currentPage)
    {
        if ($currentPage !=  null) {
            $this->currentPage = $currentPage;
        }

        return $this;
    }
}
