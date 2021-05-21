<?php

class Route
{
    /**
     * Page title for display
     *
     * @var string
     */
    public $pageTitle;

    /**
     * Page file name
     *
     * @var string
     */
    public $pageFile;

    public $pageFolder;

    /**
     * Undocumented function
     *
     * @param string $pageTitle
     * @param string $pageFile
     */
    public function __construct($pageTitle, $pageFile, $pageFolder = "pages")
    {
        $this->pageTitle = $pageTitle;
        $this->pageFile = $pageFile;
        $this->pageFolder = $pageFolder;
    }

    public function includeHeader()
    {
        $file = "$this->pageFolder/parts/$this->pageFile.header.php";

        if (file_exists($file)) {
            include_once($file);
        }
    }

    public function includePage()
    {
        require_once("$this->pageFolder/$this->pageFile.page.php");
    }

    public function includeFooter()
    {
        $file = "$this->pageFolder/parts/$this->pageFile.footer.php";

        if (file_exists($file)) {
            include_once($file);
        }
    }
}

class AuthorizedOnlyRoute extends Route
{
}

class UnauthorizedOnlyRoute extends Route
{
}

class ErrorRoute extends Route
{
    public function __construct($pageTitle, $pageFile, $pageFolder = "pages/errors")
    {
        parent::__construct($pageTitle, $pageFile, $pageFolder);
    }
}
