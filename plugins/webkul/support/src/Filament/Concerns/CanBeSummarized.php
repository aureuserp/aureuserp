<?php

namespace Webkul\Support\Filament\Concerns;

use Closure;
use Webkul\Support\Filament\Summarizers\Summarizer;

trait CanBeSummarized
{
    protected Summarizer|Closure|null $summarizer = null;

    public function summarize(Summarizer|Closure|null $summarizer): static
    {
        $this->summarizer = $summarizer;

        return $this;
    }

    public function getSummarizer(): ?Summarizer
    {
        return $this->evaluate($this->summarizer);
    }

    public function hasSummarizer(): bool
    {
        return filled($this->getSummarizer());
    }
}
