<?php

namespace Webkul\Support\Concerns;

use Closure;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    protected mixed $evaluationContext = null;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function setEvaluationContext(mixed $context): static
    {
        $this->evaluationContext = $context;

        return $this;
    }

    public function isHidden(): bool
    {
        if ($this->evaluateCondition($this->isHidden)) {
            return true;
        }

        return ! $this->evaluateCondition($this->isVisible);
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }

    protected function evaluateCondition(bool | Closure $condition): bool
    {
        if ($condition instanceof Closure) {
            if ($this->evaluationContext) {
                return (bool) $condition($this->evaluationContext);
            }
            
            if (method_exists($this, 'evaluate')) {
                return (bool) $this->evaluate($condition);
            }
            
            return (bool) $condition();
        }

        return (bool) $condition;
    }
}