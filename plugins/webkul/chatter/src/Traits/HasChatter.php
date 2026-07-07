<?php

namespace Webkul\Chatter\Traits;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Webkul\Chatter\Models\Attachment;
use Webkul\Chatter\Models\Follower;
use Webkul\Chatter\Models\Message;
use Webkul\Chatter\Relations\ChatterBelongsToMany;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityPlan;

trait HasChatter
{
    public static function bootHasChatter(): void
    {
        static::created(function (Model $model): void {
            $model->addDefaultChatterFollowers();
        });

        static::updated(function (Model $model): void {
            $model->syncResponsibleChatterFollower();
        });
    }

    public function syncResponsibleChatterFollower(): void
    {
        foreach ($this->getChatterResponsibles() as $name) {
            $column = $this->chatterResponsibleColumn($name);

            if (! $column || ! $this->wasChanged($column)) {
                continue;
            }

            try {
                $partnerId = User::whereKey($this->getAttribute($column))->value('partner_id');

                if ($partnerId && $partner = Partner::find($partnerId)) {
                    $this->addFollower($partner);
                }
            } catch (Throwable $e) {
                report($e);
            }
        }
    }

    public function getChatterResponsibleColumn(): ?string
    {
        return 'user_id';
    }

    public function chatterResponsibles(): array
    {
        return [];
    }

    public function getChatterResponsibles(): array
    {
        return array_values(array_filter(array_unique(array_merge(
            [$this->getChatterResponsibleColumn()],
            $this->chatterResponsibles(),
        ))));
    }

    protected function chatterResponsibleRelation(string $name): ?Relation
    {
        if (! method_exists($this, $name)) {
            return null;
        }

        try {
            $relation = $this->{$name}();
        } catch (Throwable $e) {
            return null;
        }

        return $relation instanceof Relation ? $relation : null;
    }

    protected function chatterResponsibleColumn(string $name): ?string
    {
        $relation = $this->chatterResponsibleRelation($name);

        if ($relation instanceof BelongsTo) {
            return $relation->getForeignKeyName();
        }

        return $relation ? null : $name;
    }

    protected function isChatterResponsiblePivot(string $name): bool
    {
        return $this->chatterResponsibleRelation($name) instanceof BelongsToMany;
    }

    protected function chatterResponsibleUserIds(string $name): array
    {
        $relation = $this->chatterResponsibleRelation($name);

        if ($relation instanceof BelongsToMany || $relation instanceof HasMany) {
            return $relation->pluck($relation->getRelated()->getKeyName())->all();
        }

        if ($relation instanceof BelongsTo) {
            $value = $this->getAttribute($relation->getForeignKeyName());

            return $value ? [(int) $value] : [];
        }

        if ($relation instanceof HasOne) {
            $value = optional($this->{$name})->getKey();

            return $value ? [(int) $value] : [];
        }

        $value = $this->getAttribute($name);

        return $value ? [(int) $value] : [];
    }

    public function resolveChatterResponsibleUserIds(): array
    {
        $userIds = [];

        foreach ($this->getChatterResponsibles() as $name) {
            $userIds = array_merge($userIds, $this->chatterResponsibleUserIds($name));
        }

        return array_values(array_unique(array_filter($userIds)));
    }

    public function resolveChatterAssignedUserId(array $properties): ?int
    {
        if (empty($properties)) {
            return null;
        }

        foreach ($this->getChatterResponsibles() as $name) {
            if ($this->isChatterResponsiblePivot($name)) {
                continue;
            }

            $label = $this->chatterResponsibleLabel($name);

            if (! $label || ! array_key_exists($label, $properties)) {
                continue;
            }

            $ids = $this->chatterResponsibleUserIds($name);

            if (! empty($ids)) {
                return (int) $ids[0];
            }
        }

        return null;
    }

    protected function chatterResponsibleLabel(string $name): ?string
    {
        if (! method_exists($this, 'getLogAttributeLabels')) {
            return null;
        }

        $labels = $this->getLogAttributeLabels();

        if ($this->chatterResponsibleRelation($name)) {
            return $labels[$name.'.name'] ?? null;
        }

        $relation = str_ends_with($name, '_id') ? substr($name, 0, -3) : $name;

        return $labels[$relation.'.name'] ?? $labels[$name] ?? null;
    }

    protected function newBelongsToMany(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
    ) {
        return new ChatterBelongsToMany($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }

    public function syncChatterResponsibleFollowers(string $action, array $userIds): void
    {
        $userIds = array_values(array_filter(array_map('intval', $userIds)));

        if (empty($userIds)) {
            return;
        }

        try {
            $users = User::with('partner')->whereIn('id', $userIds)->get();

            if ($users->isEmpty()) {
                return;
            }

            foreach ($users as $user) {
                if (! $user->partner) {
                    continue;
                }

                $action === 'attached'
                    ? $this->addFollower($user->partner)
                    : $this->removeFollower($user->partner);
            }

            if ($action === 'attached') {
                $this->logChatterResponsibleAssignment($users);
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function logChatterResponsibleAssignment(Collection $users): void
    {
        $label = __('chatter::traits/has-chatter.responsible.label');

        $this->addMessage([
            'type'        => 'notification',
            'event'       => 'updated',
            'assigned_to' => $users->count() === 1 ? $users->first()->getKey() : null,
            'body'        => method_exists($this, 'generateActivityDescription')
                ? $this->generateActivityDescription('updated')
                : $label,
            'properties'  => [
                $label => [
                    'type'      => 'added',
                    'new_value' => $users->pluck('name')->filter()->implode(', '),
                ],
            ],
        ]);
    }

    public function getChatterResponsibleLabel(): ?string
    {
        $column = $this->getChatterResponsibleColumn();

        if (! $column || ! method_exists($this, 'getLogAttributeLabels')) {
            return null;
        }

        $labels = $this->getLogAttributeLabels();
        $relation = str_ends_with($column, '_id') ? substr($column, 0, -3) : $column;

        return $labels[$relation.'.name'] ?? $labels[$column] ?? null;
    }

    public function getChatterFollowerUserIds(): array
    {
        $userIds = [];

        if ($creatorId = $this->getAttribute('creator_id')) {
            $userIds[] = (int) $creatorId;
        }

        return array_values(array_unique(array_merge($userIds, $this->resolveChatterResponsibleUserIds())));
    }

    public function addDefaultChatterFollowers(): void
    {
        try {
            $userIds = $this->getChatterFollowerUserIds();

            if (empty($userIds)) {
                return;
            }

            $partnerIds = User::whereIn('id', $userIds)
                ->pluck('partner_id')
                ->filter()
                ->unique();

            foreach ($partnerIds as $partnerId) {
                if ($partner = Partner::find($partnerId)) {
                    $this->addFollower($partner);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function messages(): MorphMany
    {
        $owner = $this->resolveChatterMessageOwner();

        return $owner->morphMany(Message::class, 'messageable')
            ->whereNot('type', 'activity')
            ->orderBy('created_at', 'desc');
    }

    public function withFilters($filters)
    {
        $query = $this->messages();

        $this->applyMessageFilters($query, $filters);

        return $query->get();
    }

    private function applyMessageFilters($query, array $filters)
    {
        if (! empty($filters['type'])) {
            $query->whereIn('type', $filters['type']);
        }

        if (isset($filters['is_internal'])) {
            $query->where('is_internal', $filters['is_internal']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['causer_id'])) {
            $query->where('causer_id', $filters['causer_id']);

            if (! empty($filters['causer_type'])) {
                $query->where('causer_type', $filters['causer_type']);
            }
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['activity_type_id'])) {
            $query->where('activity_type_id', $filters['activity_type_id']);
        }

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = '%'.$filters['search'].'%';

            $query->where(function ($query) use ($searchTerm) {
                $query->where('subject', 'like', $searchTerm)
                    ->orWhere('body', 'like', $searchTerm)
                    ->orWhere('summary', 'like', $searchTerm)
                    ->orWhere('name', 'like', $searchTerm);
            });
        }

        return $query;
    }

    public function unRead()
    {
        return $this->messages()->where('is_read', false)->get();
    }

    public function markAsRead(): int
    {
        return $this->messages()->where('is_read', false)->update(['is_read' => true]);
    }

    public function activities(): MorphMany
    {
        $owner = $this->resolveChatterMessageOwner();

        return $owner->morphMany(Message::class, 'messageable')
            ->where('type', 'activity')
            ->orderBy('created_at', 'desc');
    }

    public function activityPlans(): mixed
    {
        $plugin = $this->activityPlanPlugin();

        return $plugin
            ? ActivityPlan::forPlugin($plugin)->pluck('name', 'id')
            : collect();
    }

    public function activityPlanPlugin(): ?string
    {
        $constantName = static::class.'::ACTIVITY_PLAN_PLUGIN';

        return defined($constantName) ? (string) constant($constantName) : null;
    }

    public function followable()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function addMessage(array $data): Message
    {
        $message = new Message;

        $user = Filament::auth()->user() ?? Auth::user();

        $message->fill(array_merge([
            'date_deadline' => $data['date_deadline'] ?? now(),
            'causer_type'   => $user?->getMorphClass(),
            'causer_id'     => $user?->id,
            'company_id'    => $data['company_id'] ?? ($user?->defaultCompany?->id ?? null),
        ], $data));

        $this->messages()->save($message);

        return $message;
    }

    public function addActivity(array $data): Message
    {
        $user = Filament::auth()->user() ?? Auth::user();

        $data['assigned_to'] ??= $user?->id;

        return $this->addMessage(array_merge($data, [
            'type' => 'activity',
        ]));
    }

    protected function resolveChatterMessageOwner(): Model
    {
        if (method_exists($this, 'chatterMessageOwner')) {
            $owner = $this->chatterMessageOwner();
            if ($owner instanceof Model) {
                return $owner;
            }
        }

        return $this;
    }

    public function chatterMessageOwner(): Model
    {
        $baseClass = $this->resolveChatterModelClass();

        if ($baseClass === get_class($this)) {
            return $this;
        }

        $owner = new $baseClass;
        $owner->setAttribute($owner->getKeyName(), $this->getKey());
        $owner->exists = true;
        $owner->syncOriginal();

        return $owner;
    }

    public function resolveChatterModelClass(): string
    {
        $class = get_class($this);

        while (
            ($parent = get_parent_class($class)) !== false
            && str_starts_with($parent, 'Webkul\\')
        ) {
            $class = $parent;
        }

        return $class;
    }

    public function getChatterMorphClass(): string
    {
        return $this->resolveChatterMessageOwner()->getMorphClass();
    }

    public function getChatterResourceUrl(): string
    {
        try {
            $panel = Filament::getCurrentPanel() ?? Filament::getPanel('admin');

            if (! $panel) {
                return '';
            }

            foreach ($panel->getResources() as $resource) {
                if ($resource::getModel() !== static::class) {
                    continue;
                }

                $pages = $resource::getPages();

                foreach (['view', 'edit'] as $page) {
                    if (array_key_exists($page, $pages)) {
                        return $resource::getUrl($page, ['record' => $this], panel: $panel->getId());
                    }
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return '';
    }

    protected function ownsChatterRecord(Model $record): bool
    {
        $owner = $this->resolveChatterMessageOwner();

        return $record->messageable_id === $owner->getKey()
            && $record->messageable_type === $owner->getMorphClass();
    }

    public function replyToMessage(Message $parentMessage, array $data): Message
    {
        return $this->addMessage(array_merge($data, [
            'parent_id'        => $parentMessage->id,
            'company_id'       => $parentMessage->company_id,
            'activity_type_id' => $parentMessage->activity_type_id,
        ]));
    }

    public function removeMessage($messageId, $type = 'messages'): bool
    {
        $message = $this->{$type}()->find($messageId);

        if (! $message || ! $this->ownsChatterRecord($message)) {
            return false;
        }

        return $message->delete();
    }

    public function pinMessage(Message $message): bool
    {
        if (! $this->ownsChatterRecord($message)) {
            return false;
        }

        $message->pinned_at = now();

        return $message->save();
    }

    public function unpinMessage(Message $message): bool
    {
        if (! $this->ownsChatterRecord($message)) {
            return false;
        }

        $message->pinned_at = null;

        return $message->save();
    }

    public function getPinnedMessages(): Collection
    {
        return $this->messages()->whereNotNull('pinned_at')->orderBy('pinned_at', 'desc')->get();
    }

    public function getMessagesByType(string $type): Collection
    {
        return $this->messages()->where('type', $type)->get();
    }

    public function getInternalMessages(): Collection
    {
        return $this->messages()->where('is_internal', true)->get();
    }

    public function getMessagesByDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->messages()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
    }

    public function getMessagesByActivityType(int $activityTypeId): Collection
    {
        return $this->messages()
            ->where('activity_type_id', $activityTypeId)
            ->get();
    }

    public function attachments(): MorphMany
    {
        $owner = $this->resolveChatterMessageOwner();

        return $owner->morphMany(Attachment::class, 'messageable')->orderBy('created_at', 'desc');
    }

    public function addAttachments(array $files, array $additionalData = []): Collection
    {
        if (empty($files)) {
            return collect();
        }

        return $this->attachments()
            ->createMany(
                collect($files)
                    ->map(fn ($filePath) => [
                        'file_path'          => $filePath,
                        'original_file_name' => basename($filePath),
                        'mime_type'          => mime_content_type($storagePath = storage_path('app/public/'.$filePath)) ?: 'application/octet-stream',
                        'file_size'          => filesize($storagePath) ?: 0,
                        'creator_id'         => Filament::auth()->id() ?? Auth::id(),
                        ...$additionalData,
                    ])
                    ->filter()
                    ->toArray()
            );
    }

    public function removeAttachment($attachmentId): bool
    {
        $attachment = $this->attachments()->find($attachmentId);

        if (! $attachment || ! $this->ownsChatterRecord($attachment)) {
            return false;
        }

        if (Storage::exists('public/'.$attachment->file_path)) {
            Storage::delete('public/'.$attachment->file_path);
        }

        return $attachment->delete();
    }

    public function getAttachmentsByType(string $mimeType): Collection
    {
        return $this->attachments()
            ->where('mime_type', 'LIKE', $mimeType.'%')
            ->get();
    }

    public function getAttachmentsByDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->attachments()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function getImageAttachments(): Collection
    {
        return $this->getAttachmentsByType('image/');
    }

    public function getDocumentAttachments(): Collection
    {
        return $this->attachments()
            ->where('mime_type', 'NOT LIKE', 'image/%')
            ->get();
    }

    public function attachmentExists($attachmentId): bool
    {
        $attachment = $this->attachments()->find($attachmentId);

        return $attachment && Storage::exists('public/'.$attachment->file_path);
    }

    public function followers(): MorphMany
    {
        return $this
            ->resolveChatterMessageOwner()
            ->morphMany(Follower::class, 'followable');
    }

    public function addFollower(Partner $partner): Follower
    {
        return $this->followers()->firstOrCreate(
            ['partner_id' => $partner->id],
            ['followed_at' => now()],
        );
    }

    public function removeFollower(Partner $partner): bool
    {
        return (bool) $this->followers()
            ->where('partner_id', $partner->id)
            ->delete();
    }

    public function isFollowedBy(Partner $partner): bool
    {
        return $this->followers()
            ->where('partner_id', $partner->id)
            ->exists();
    }
}
