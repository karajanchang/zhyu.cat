<?php

namespace Cat\Console\Commands\CatEater;

use Cat\Console\Commands\CatEatCommand;

interface ContractEater
{
    public function handle(CatEatCommand $command);
}