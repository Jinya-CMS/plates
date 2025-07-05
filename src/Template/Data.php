<?php

namespace Jinya\Plates\Template;

use JetBrains\PhpStorm\Pure;

/**
 * Preassigned template data.
 *
 * @internal
 */
class Data
{
    /**
     * Variables shared by all templates.
     *
     * @var array<mixed, mixed>
     */
    protected array $sharedVariables = [];

    /**
     * Specific template variables.
     *
     * @var array<string, array<mixed>>
     */
    protected array $templateVariables = [];

    /**
     * Add template data.
     *
     * @param array<mixed, mixed> $data
     * @param string[]|string|null $templates
     */
    public function add(array $data, array|string|null $templates = null): Data
    {
        if (is_null($templates)) {
            return $this->shareWithAll($data);
        }

        if (is_array($templates)) {
            return $this->shareWithSome($data, $templates);
        }

        return $this->shareWithSome($data, [$templates]);
    }

    /**
     * Add data shared with all templates.
     *
     * @param array<mixed, mixed> $data
     */
    public function shareWithAll(array $data): Data
    {
        $this->sharedVariables = array_merge($this->sharedVariables, $data);

        return $this;
    }

    /**
     * Add data shared with some templates.
     *
     * @param array<mixed, mixed> $data
     * @param string[] $templates
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
     *
     * @return array<mixed, mixed>
     */
    #[Pure]
    public function get(string|null $template = null): array
    {
        if (isset($template, $this->templateVariables[$template])) {
            return array_merge($this->sharedVariables, $this->templateVariables[$template]);
        }

        return $this->sharedVariables;
    }
}
