<?php

namespace src\admin\Configuration\Domain\Entities;

use src\admin\Configuration\Domain\ValueObjects\ConfigurationType;

class Configuration
{
    public function __construct(
        private ?int $id,
        private string $key,
        private string $value,
        private ?int $eventId,
        private ConfigurationType $type,
        private string $description,
        private ?bool $isActive = true,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): ConfigurationType
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

  
}
