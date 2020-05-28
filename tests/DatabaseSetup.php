<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

/**
 * Trait DatabaseSetup
 *
 * @package Tests
 * @codeCoverageIgnore
 */
trait DatabaseSetup
{
    protected static bool $migrated = false;

    public function setupDatabase(): void
    {
        if ($this->isInMemory()) {
            $this->setupInMemoryDatabase();
        } else {
            $this->setupTestDatabase();
        }
    }

    /**
     * @return bool
     */
    protected function isInMemory(): bool
    {
        return config('database.connections')[config('database.default')]['database'] === ':memory:';
    }

    protected function setupInMemoryDatabase(): void
    {
        $this->artisan('migrate');
        $this->app[Kernel::class]->setArtisan(null);
    }

    protected function setupTestDatabase(): void
    {
        if (!static::$migrated) {
            $this->artisan('migrate:refresh');
            $this->app[Kernel::class]->setArtisan(null);
            static::$migrated = true;
        }
        $this->beginDatabaseTransaction();
    }

    public function beginDatabaseTransaction(): void
    {
        $database = $this->app->make('db');

        foreach ($this->connectionsToTransact() as $name) {
            $database->connection($name)->beginTransaction();
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $database->connection($name)->rollBack();
            }
        });
    }

    protected function connectionsToTransact(): array
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact : [null];
    }
}
