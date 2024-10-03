<?php

namespace Cat\Helpers;

trait HelperTrait
{
    protected string $title;
    protected string $description;

    public function __construct()
    {
        $this->setTitle($this->title());
        $this->setDescription($this->description());
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

}