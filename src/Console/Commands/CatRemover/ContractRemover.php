<?php

namespace Cat\Console\Commands\CatRemover;

use Cat\Console\Commands\CatRemoveCommand;

interface ContractRemover
{
    public function handle(CatRemoveCommand $command);
}