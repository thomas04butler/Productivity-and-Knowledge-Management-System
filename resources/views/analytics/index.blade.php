<x-pulse-layout>
    @if(auth()->user()->can('view all') && request()->query('view') === 'manager')
        <!-- managers views -->

       <livewire:pulse.completed-tasks-timeline cols="4" />

        <livewire:pulse.project-status cols="4" />
        <livewire:pulse.task-status cols="4" />
        <livewire:pulse.task-completion-time cols="6" />
        <livewire:pulse.created-tasks-timeline cols="6" />
        <livewire:pulse.tasks-within-time-frame cols="12" />
        <livewire:pulse.projects-with-most-outstanding-tasks cols="6" />
        <livewire:pulse.users-with-most-tasks cols="6" />
        <livewire:pulse.projects-with-most-overdue-tasks cols="6" />
        <livewire:pulse.users-with-most-overdue-tasks cols="6" />
        <livewire:pulse.users-with-most-complete-tasks cols="6" />
    @elseif(auth()->user()->can('view team') && request()->query('view') === 'leader')
        <!-- leaders views -->
        <livewire:pulse.completed-tasks-timeline-leader cols="4" />
        <livewire:pulse.project-status-leader cols="4" />
        <livewire:pulse.task-status-leader cols="4" />
        <livewire:pulse.task-completion-time-leader cols="6" />
        <livewire:pulse.created-task-timeline-leader cols="6" />
        <livewire:pulse.tasks-within-time-frame-leader cols="12" />
        <livewire:pulse.projects-with-most-outstanding-tasks-leader cols="6" />
        <livewire:pulse.users-with-most-outstanding-tasks-leader cols="6" />
        <livewire:pulse.projects-with-most-overdue-tasks-leader cols="6" />
        <livewire:pulse.users-with-most-overdue-tasks-leader cols="6" />
        <livewire:pulse.users-with-most-complete-tasks-leader cols="6" />
    @elseif(auth()->user()->can('view personal'))
        <!-- members views -->
        <livewire:pulse.completed-tasks-timeline-member cols="8" />
        <livewire:pulse.task-status-member cols="4" />
        <livewire:pulse.task-completion-time-member cols="6" />
        <livewire:pulse.created-task-timeline-member cols="6" />
        <livewire:pulse.tasks-within-timeframe-member cols="12" />
        <livewire:pulse.projects-with-most-outstanding-tasks-member cols="6" />
        <livewire:pulse.projects-with-most-overdue-tasks-member cols="6" />
    @endif

</x-pulse-layout>
