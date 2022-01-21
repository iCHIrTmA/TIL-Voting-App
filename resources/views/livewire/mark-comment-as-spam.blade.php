<x-modal-confirm
  livewireEventToOpenModal="setMarkAsSpamComment"
  eventToCloseModal="commentWasMarkedAsSpam"
  modalTitle="Mark Comment as Spam"
  modalDescription="Are you sure you want to mark this comment as spam?"
  modalConfirmText="Mark as Spam"
  wireClick="markSpam"
/>