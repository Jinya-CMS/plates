<?php

namespace League\Plates\Template;

use LogicException;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private Data $template_data;

    public function setUp(): void
    {
        $this->template_data = new Data();
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Data::class, $this->template_data);
    }

    public function testAddDataToAllTemplates(): void
    {
        $this->template_data->add(['name' => 'Jonathan']);
        $data = $this->template_data->get();
        self::assertEquals('Jonathan', $data['name']);
    }

    public function testAddDataToOneTemplate(): void
    {
        $this->template_data->add(['name' => 'Jonathan'], 'template');
        $data = $this->template_data->get('template');
        self::assertEquals('Jonathan', $data['name']);
    }

    public function testAddDataToOneTemplateAgain(): void
    {
        $this->template_data->add(['firstname' => 'Jonathan'], 'template');
        $this->template_data->add(['lastname' => 'Reinink'], 'template');
        $data = $this->template_data->get('template');
        self::assertEquals('Reinink', $data['lastname']);
    }

    public function testAddDataToSomeTemplates(): void
    {
        $this->template_data->add(['name' => 'Jonathan'], ['template1', 'template2']);
        $data = $this->template_data->get('template1');
        self::assertEquals('Jonathan', $data['name']);
    }

    public function testAddDataWithInvalidTemplateFileType(): void
    {
        $this->expectException(LogicException::class);
        $this->template_data->add(['name' => 'Jonathan'], 123);
    }
}
