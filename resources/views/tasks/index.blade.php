@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">

            <!-- Project Form (left) -->
            <form method="POST" action="{{ route('project.store') }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="name"  class="form-control" placeholder="Project name" required>
                <button type="submit" class="btn btn-success w-100">Add Project</button>
            </form>

            <!-- Create Task Button (right) -->
            <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="editTask('add')">
                Create Task
            </button>

        </div>

        <form method="GET" action="{{ route('tasks.index') }}" class="mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="project_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Modal -->
        <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true" >
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form action="{{ route('tasks.store') }}" method="POST" id="taskForm">
                        @csrf

                            <input name="id" id="editedId" hidden>
                        <!-- Task Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Task Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter task name" required>
                            </div>

                            <!-- Project Dropdown -->
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Select Project</label>
                                <select name="project_id" id="project_id" class="form-select" required>
                                    <option value="">-- Select Project --</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Submit -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Save Task</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this task?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-bordered text-center" id="taskTable">
                    <thead>
                    <tr>
                        <th scope="col">Day</th>
                        <th scope="col">Task Name</th>
                        <th scope="col">Project</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="sortable">

                    @foreach($tasks as $index => $task)

                    <tr data-id="{{ $task->id }}">
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $task->name }}</td>
                        <td>{{ $task->project->name }}</td>

                        <td>
                            <button type="button"
                                    class="btn btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#taskModal"
                                    onclick="editTask('edit' , {{ $task }})"
                            ><i class="fas fa-edit"></i></button>
                            <!-- Delete Button -->
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    onclick="setDeleteTask({{ $task->id }})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>

                    @endforeach

                    </tbody>
                </table>

                {{ $tasks->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        $(function () {
            $('#sortable').sortable({
                update: function (event, ui) {
                    let order = [];
                    $('#sortable tr').each(function (index, element) {
                        order.push({
                            id: $(element).data('id'),
                            priority: index + 1
                        });
                    });

                    // Send AJAX request
                    $.ajax({
                        url: '{{ route("tasks.reorder") }}',
                        method: 'POST',
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            console.log(response.message);
                        }
                    });
                }
            });
        });

        function editTask( type , data = null) {

            let methodInput = document.querySelector("input[name='_method']");
            if (!methodInput) {
                methodInput = document.createElement("input");
                methodInput.type = "hidden";
                methodInput.name = "_method";
                document.getElementById('taskForm').appendChild(methodInput);
            }


            if (type === 'add'){
                document.getElementById('project_id').value = "";
                document.getElementById('name').value = "";
                document.getElementById('editedId').value = null;
                document.getElementById('taskForm').action = "{{ route('tasks.store') }}";
                methodInput.value = "POST";
            }else {
                document.getElementById('taskForm').action = `tasks/${data.id}`;
                document.getElementById('editedId').value = data.id;// Store ID in hidden input
                document.getElementById('name').value = data.name || "";
                const projectSelect = document.getElementById("project_id");
                if (projectSelect && data.project_id) {
                    projectSelect.value = data.project_id.toString(); // convert to string to match the option value
                }
                methodInput.value = "PUT";
            }
        }


        function setDeleteTask(taskId) {
            const form = document.getElementById('deleteForm');
            form.action = `/tasks/${taskId}`;
        }

    </script>
@endpush
