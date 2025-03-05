<?php

namespace Coolsam\VisualForms\Commands;

use Illuminate\Console\Command;

class VisualFormsCommand extends Command
{
    public $signature = 'visual-forms';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
