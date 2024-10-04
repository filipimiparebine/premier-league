<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface FixtureGeneratorInterface
{
    public function generateFixtures(Collection $teams): array;
}
