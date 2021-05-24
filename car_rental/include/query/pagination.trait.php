<?php

trait Pagination
{
    private $currentPage = 1;
    private $itemsPerPage = 8;
    private $limitClause = '';

    function getReturnType()
    { /*1*/
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
        $this->currentPage = $currentPage;

        return $this;
    }
}
