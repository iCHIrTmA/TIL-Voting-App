@can('update', $idea)
    <livewire:edit-idea
        :idea="$idea"/>
@endcan
@can('delete', $idea)
    <livewire:delete-idea
        :idea="$idea"/>
@endcan
@auth
    <livewire:mark-as-spam
        :idea="$idea"/>
@endauth
@admin
    <livewire:mark-as-not-spam
        :idea="$idea"/>
@endadmin
@auth
    <livewire:edit-comment/>
@endauth
