<?php

class Upload
{
    /**
     * Upload directory.
     *
     * @var string
     */
    private $upload_dir;

    /**
     * Max file upload size.
     *
     * @var int
     */
    private $max_file_size;

    /**
     * List of allowed file extensions.
     *
     * @var array
     */
    private $allowed_extensions;

    /**
     * List of denied mime types.
     *
     * @var array
     */
    private $denied_mime_types;

    /**
     * Whether to use hashed names for uploaded files.
     *
     * @var boolean
     */
    public $hashedNames = true;

    public function __construct(
        string $upload_dir,
        int $max_file_size = 1048576,
        array $allowed_extensions = array('txt', 'jpeg', 'jpg', 'png', 'gif', 'docx', 'pptx'),
        array $denied_mime_types = array('application/x-php', 'application/x-javascript', 'application/zip')
    ) {
        $this->setUploadDir($upload_dir);
        $this->max_file_size = $max_file_size;
        $this->allowed_extensions = $allowed_extensions;
        $this->denied_mime_types = $denied_mime_types;
    }

    public function setUploadDir($upload_dir)
    {
        if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
            throw new RuntimeException("Invalid upload directory!");
        }

        $this->upload_dir = $upload_dir;
    }

    public function getUploadDir()
    {
        return $this->upload_dir;
    }

    private function generateFileName(array $file)
    {
        if ($this->hashedNames) {
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            return sha1($file['name'] . $file['size'] . time()) . ".$fileExtension";
        } else {
            $fileName = rand(1, 9999999) . '_' . $this->make_safe($file['name']);
            $flag = true;
            while ($flag) {
                if (!file_exists($this->getUploadDir() . '/' . $fileName)) {
                    $flag = false;
                } else {
                    $fileName = rand(1, 9999999) . '(' . rand(1, 9999) . ')' . $fileName;
                }
            }
            return $fileName;
        }
    }

    /**
     * Upload file.
     *
     * @param array $file
     *
     * @return UploadedFile
     *
     * @throws UploadException
     * @throws RuntimeException
     */
    public function upload(array $file): UploadedFile
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new UploadException($_FILES['file']['error']);
        }

        $finalFileName = $this->generateFileName($file); // TODO
        $finalFilePath =  $this->upload_dir . $finalFileName;
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['size'] <= 0) {
            throw new RuntimeException($file['name'] . ' uploading failed (Size is 0)');
        } elseif ($file['size'] >= $this->max_file_size) {
            throw new RuntimeException($file['name'] . ' exceeds the MAX_FILE_SIZE');
        } elseif (!in_array($fileExtension, $this->allowed_extensions)) {
            throw new RuntimeException(".$fileExtension is not an allowed extension");
        } elseif (in_array($file['type'], $this->denied_mime_types)) {
            throw new RuntimeException($file['type'] . ' is a denied type');
        }

        if (!move_uploaded_file($file['tmp_name'], $finalFilePath)) {
            throw new RuntimeException($file['name'] . " could not be uploaded");
        }

        return new UploadedFile($file, $finalFileName, $finalFilePath);
    }

    function make_safe($str)
    {
        $illegal_symbols = array(' ', '-', '/', '\\', "'", '"', '*', '?', ':');

        return str_replace($illegal_symbols, '_', $str);
    }
}

class UploadedFile
{
    private $originalName;
    private $type;
    private $size;

    private $name;
    private $path;

    public function __construct(array $file, string $name, string $path)
    {
        $this->originalName = $file['name'];
        $this->type = $file['type'];
        $this->size = $file['size'];

        $this->name = $name;
        $this->path = $path;
    }

    /**
     * Get the value of originalName
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the value of size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of path
     */
    public function getPath()
    {
        return $this->path;
    }
}

/**
 * Custom upload exception
 *
 * Source: https://www.php.net/manual/en/features.file-upload.errors.php#89374
 */
class UploadException extends Exception
{
    public function __construct($code)
    {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }

    private function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            case UPLOAD_ERR_FORM_SIZE:
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            case UPLOAD_ERR_PARTIAL:
                return "The uploaded file was only partially uploaded";
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk";
            case UPLOAD_ERR_EXTENSION:
                return "File upload stopped by extension";
            default:
                return "Unknown upload error";
        }
    }
}
