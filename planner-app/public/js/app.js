$(document).ready(function () {
    // Triggered from dropdown "Add Subtask"
    window.prepareSubtaskModal = function (triggerElement) {
        const $header = $(triggerElement).closest('.accordion-header');
        if (!$header.length) return;

        const parentId = $header.data('parent-id');
        const parentTitle = $header.data('parent-title');

        $('#parent_id').val(parentId);
        $('#parent_task_display').val(parentTitle);
        $('#parentTaskWrapper').removeClass('d-none');
    };

    // Top-level +Add Task button
    $('[data-bs-target="#addTaskModal"]').on('click', function () {
        $('#parent_id').val('');
        $('#parent_task_display').val('');
        $('#parentTaskWrapper').addClass('d-none');
    });

    // Reset modal if opened without preset parent_id
    $('#addTaskModal').on('show.bs.modal', function () {
        if (!$('#parent_id').val()) {
            $('#parentTaskWrapper').addClass('d-none');
            $('#parent_id').val('');
            $('#parent_task_display').val('');
        }
    });

    // Handle task creation via API
    $('#addTaskModal form').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        $.ajax({
            url: '/api/tasks',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            xhrFields: { withCredentials: true },
            success: function () {
                $.get('/tasks/flash?message=Task added successfully', function () {
                    location.reload();
                });
            },
            error: function (xhr) {
                alert('Failed to add task: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    window.loadTaskIntoModal = function(task) {
        $('#edit_id').val(task.id);
        $('#edit_title').val(task.title);
        $('#edit_description').val(task.description);
        $('#edit_priority').val(task.priority);
    };

    $('#editTaskForm').on('submit', async function (e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        const data = {
            title: $('#edit_title').val(),
            description: $('#edit_description').val(),
            priority: $('#edit_priority').val(),
        };

        try {
            await $.ajax({
                url: `/api/tasks/${id}`,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                xhrFields: { withCredentials: true }
            });

            location.reload();
        } catch (err) {
            alert('Failed to update task: ' + (err.responseJSON?.message || 'Unknown error'));
        }
    });

    window.completeTask = function(id) {
        if (!confirm('Mark this task as complete?')) return;

        $.ajax({
            url: `/api/tasks/${id}/complete`,
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
            },
            xhrFields: { withCredentials: true },
            success: () => location.reload(),
            error: function (xhr) {
                alert('Failed to complete task: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    };

    window.deleteTask = function(id) {
        if (!confirm('Are you sure you want to delete this task?')) return;

        $.ajax({
            url: `/api/tasks/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
            },
            xhrFields: { withCredentials: true },
            success: () => location.reload(),
            error: function (xhr) {
                alert('Failed to delete task: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    };
});
