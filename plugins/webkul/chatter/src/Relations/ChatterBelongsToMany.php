<?php

namespace Webkul\Chatter\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatterBelongsToMany extends BelongsToMany
{
    public function attach($id, array $attributes = [], $touch = true)
    {
        parent::attach($id, $attributes, $touch);

        $this->syncChatterResponsible('attached', $id);
    }

    public function detach($ids = null, $touch = true)
    {
        $result = parent::detach($ids, $touch);

        $this->syncChatterResponsible('detached', $ids);

        return $result;
    }

    protected function syncChatterResponsible(string $action, $ids): void
    {
        if ($ids === null) {
            return;
        }

        $parent = $this->getParent();

        if (! method_exists($parent, 'getChatterResponsibles')) {
            return;
        }

        if (! in_array($this->getRelationName(), $parent->getChatterResponsibles(), true)) {
            return;
        }

        $userIds = collect($this->parseIds($ids))->all();

        if (empty($userIds)) {
            return;
        }

        $parent->syncChatterResponsibleFollowers($action, $userIds);
    }
}
