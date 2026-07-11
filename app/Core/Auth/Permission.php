<?php

namespace app\Core\Auth;

class Permission
{
    private $name;
    private $description;

    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Get permission name identifier.
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get permission description.
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
