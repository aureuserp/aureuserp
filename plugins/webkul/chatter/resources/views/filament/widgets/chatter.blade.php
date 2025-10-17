<livewire:chatter-panel
    :record="$record ?? $this->record"
    :activityPlans="$activityPlans ?? $this->activityPlans"
    :resource="$resource ?? $this->resource"
    :followerViewMail="$followerViewMail ?? $this->followerViewMail"
    :messageViewMail="$messageViewMail ?? $this->messageViewMail"
    lazy
/>
