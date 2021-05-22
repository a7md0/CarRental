<?php

$CUSTOM_CLASSES = [
    'body' => [],
    'main' => []
];

$HIDE_FOOTER = false;

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
        global $CUSTOM_CLASSES, $HIDE_FOOTER;
        global $CURRENT_USER;

        $file = "$this->pageFolder/parts/$this->pageFile.header.php";

        if (file_exists($file)) {
            include_once $file;
        }
    }

    public function includePage()
    {
        global $CURRENT_USER;

        require_once "$this->pageFolder/$this->pageFile.page.php";
    }

    public function includeFooter()
    {
        global $CURRENT_USER;

        $file = "$this->pageFolder/parts/$this->pageFile.footer.php";

        if (file_exists($file)) {
            include_once $file;
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
