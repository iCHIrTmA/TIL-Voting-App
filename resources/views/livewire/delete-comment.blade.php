<x-modal-confirm
  livewireEventToOpenModal="deleteCommentWasSet"
  eventToCloseModal="commentWasDeleted"
  modalTitle="Delete Comment"
  modalDescription="Are you sure you want to delete this comment? This cannot be undone."
  modalConfirmText="Delete"
  wireClick="deleteComment"
/>