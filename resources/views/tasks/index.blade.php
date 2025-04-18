@extends('layouts.app')
@section('content')
    <div class="container">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <input type="text" name="name" placeholder="Task name" required>
            <select name="project_id">
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
            <button type="submit">Add Task</button>
        </form>

        <ul id="taskList">
            @foreach($tasks as $task)
                <li data-id="{{ $task->id }}">{{ $task->name }}</li>
            @endforeach
        </ul>

        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Day</th>
                        <th scope="col">Article Name</th>
                        <th scope="col">Author</th>
                        <th scope="col">Shares</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Bootstrap 4 CDN and Starter Template</td>
                        <td>Cristina</td>
                        <td>2.846</td>
                        <td>
                            <button type="button" class="btn btn-primary"><i class="far fa-eye"></i></button>
                            <button type="button" class="btn btn-success"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Bootstrap Grid 4 Tutorial and Examples</td>
                        <td>Cristina</td>
                        <td>3.417</td>
                        <td>
                            <button type="button" class="btn btn-primary"><i class="far fa-eye"></i></button>
                            <button type="button" class="btn btn-success"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Bootstrap Flexbox Tutorial and Examples</td>
                        <td>Cristina</td>
                        <td>1.234</td>
                        <td>
                            <button type="button" class="btn btn-primary"><i class="far fa-eye"></i></button>
                            <button type="button" class="btn btn-success"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        new Sortable(taskList, {
            onEnd: function () {
                const ids = [...document.querySelectorAll('#taskList li')].map(li => li.dataset.id);
                fetch('/tasks/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ tasks: ids })
                });
            }
        });
    </script>
@endpush
