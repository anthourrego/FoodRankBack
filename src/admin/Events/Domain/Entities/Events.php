<?php

namespace src\admin\Events\Domain\Entities;

class Events
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $startDate,
        public string $endDate,
        public int $cityId,
        public bool $isActive,
        public int $createdBy,
        public int $updatedBy,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getCityId(): int
    {
        return $this->cityId;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    
}
