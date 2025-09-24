<?php

namespace src\admin\Configuration\Domain\ValueObjects;

class ConfigurationType
{
    public const TEXT = 'text';
    public const TEXTAREA = 'textarea';
    public const IMAGE = 'image';
    public const BOOLEAN = 'boolean';
    public const NUMBER = 'number';
    public const BANNER = 'banner';

    private string $value;

    public function __construct(string $type)
    {
        if (!in_array($type, [self::TEXT, self::TEXTAREA, self::IMAGE, self::BOOLEAN, self::NUMBER, self::BANNER])) {
            throw new \InvalidArgumentException('El tipo de configuración debe ser: text, textarea, image, boolean, number o banner');
        }
        $this->value = $type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isText(): bool
    {
        return $this->value === self::TEXT;
    }

    public function isTextarea(): bool
    {
        return $this->value === self::TEXTAREA;
    }

    public function isImage(): bool
    {
        return $this->value === self::IMAGE;
    }

    public function isBoolean(): bool
    {
        return $this->value === self::BOOLEAN;
    }

}
