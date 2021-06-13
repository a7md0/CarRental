<?php

trait Pagination
{
    private $currentPage = 1;
    private $itemsPerPage = 8;

    // total records in table
    private $totalRecords = null;

    function getLimitClause()
    {
        $itemsPerPage = $this->itemsPerPage;
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;

        return " LIMIT $offset, $itemsPerPage";
    }

    function getTotalPages()
    {
        if ($this->totalRecords === null) {
            $this->totalRecords = $this->count();
        }

        return ceil($this->totalRecords / $this->itemsPerPage);
    }

    function hasNextPage()
    {
        return $this->currentPage < $this->getTotalPages();
    }

    function hasPreviousPage()
    {
        return $this->currentPage > 1;
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

    /**
     * Get the value of itemsPerPage
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Set the value of itemsPerPage
     *
     * @return  self
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }
}
