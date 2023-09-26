<?php

namespace League\Plates\Template;

use JetBrains\PhpStorm\Pure;
use LogicException;

/**
 * Preassigned template data.
 */
class Data
{
    /**
     * Variables shared by all templates.
     */
    protected array $sharedVariables = [];

    /**
     * Specific template variables.
     */
    protected array $templateVariables = [];

    /**
     * Add template data.
     *
     * @param  null|string|array  $templates
     */
    public function add(array $data, mixed $templates = null): Data
    {
        if (is_null($templates)) {
            return $this->shareWithAll($data);
        }

        if (is_array($templates)) {
            return $this->shareWithSome($data, $templates);
        }

        if (is_string($templates)) {
            return $this->shareWithSome($data, [$templates]);
        }

        throw new LogicException(
            'The templates variable must be null, an array or a string, '.gettype($templates).' given.'
        );
    }

    /**
     * Add data shared with all templates.
     *
     * @param  array  $data ;
     */
    public function shareWithAll(array $data): Data
    {
        $this->sharedVariables = array_merge($this->sharedVariables, $data);

        return $this;
    }

    /**
     * Add data shared with some templates.
     */
    public function shareWithSome(array $data, array $templates): Data
    {
        foreach ($templates as $template) {
            if (isset($this->templateVariables[$template])) {
                $this->templateVariables[$template] = array_merge($this->templateVariables[$template], $data);
            } else {
                $this->templateVariables[$template] = $data;
            }
        }

        return $this;
    }

    /**
     * Get template data.
     */
    #[Pure]
    public function get(string $template = null): array
    {
        if (isset($template, $this->templateVariables[$template])) {
            return array_merge($this->sharedVariables, $this->templateVariables[$template]);
        }

        return $this->sharedVariables;
    }
}
