<?php
// TODO: Replace with simple string assignment

namespace League\Plates\Template;

/**
 * Template file extension.
 */
class FileExtension
{
    /**
     * Template file extension.
     * @var string|null
     */
    protected ?string $fileExtension;

    /**
     * Create new FileExtension instance.
     * @param null|string $fileExtension
     */
    public function __construct(?string $fileExtension = 'php')
    {
        $this->set($fileExtension);
    }

    /**
     * Set the template file extension.
     * @param null|string $fileExtension
     * @return FileExtension
     */
    public function set(?string $fileExtension): FileExtension
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Get the template file extension.
     * @return string|null
     */
    public function get(): ?string
    {
        return $this->fileExtension;
    }
}
