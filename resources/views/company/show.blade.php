@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <?php if($company[0]->logo_slug!="") { ?>
            <img class="align-self-center mr-3" style="max-width:50px; max-height:50px;" src="{{ asset('storage/') }}/{{ $company[0]->logo_slug }}"></img>
            <?php } else {?>
            <img class="align-self-center mr-3" style="max-width:50px; max-height:50px;" src="{{ asset('storage/logo-placeholder.png') }}"></img> <?php } ?>
            <h2 class="title">{{ $company[0]->name }}</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm">
                <label>Email:</label>
            </div>
            <div class="col-sm">
                <label>Website:</label>
            </div>
            <div class="col-sm">
                <label>Employee count:</label>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm">
                <h4><a href="mailto:{{ $company[0]->email }}">{{ $company[0]->email }}</a></h4>
            </div>
            <div class="col-sm">
                <h4><a href="http://{{ $company[0]->website }}">{{ $company[0]->website }}</a></h4>
            </div>
            <div class="col-sm">
                <h4>{{ $count }}</h4>
            </div>
        </div>
        <br>
        <div class="row justify-content-center">
            <form method="post" action="" style="float:right;">
            @csrf @method('delete')
                <a class="btn btn-primary" href="" id="edit" class="links"> Edit details</a>
                <a class="btn btn-primary" href="" id="upload" class="links"> Edit logo</a>
                <a class="btn btn-danger" id="delete"type="submit"  style="color: white;">Delete</a>
                <a class="btn btn-danger" id="delete_all"type="submit"  style="color: white;">Delete all employees</a>
            </form>
        </div>
        <br><br>
        <h2 class="title" style="text-align: center; margin-bottom:20px;">{{ $company[0]->name }} Employees</h2>
        
        <div class="row m-5">
            <div class="col-12">
                <table id="employees-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="practice_modal">
            <div class="modal-dialog">
                <form id="companydata">
                    <div class="modal-content">
                            <div class="modal-body">
                                <label>Email</label>
                                <input type="text" name="email" id="email" value="{{ $company[0]->email }}" class="form-control">
                                <label>Website</label>
                                <input type="text" name="website" id="website" value="{{ $company[0]->website }}" class="form-control">
                                <label>Name</label>
                                <input type="text" name="name" id="name" value="{{ $company[0]->name }}" class="form-control">
                            </div>
                            <input type="submit" value="Submit" id="submit" class="btn btn-sm btn-outline-danger py-0" style="font-size: 0.8em;">
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="image_modal">
            <div class="modal-dialog">
                <form id="logo_upload">
                    <div class="modal-content">
                            <div class="modal-body">
                            <label for="logo_image" class="col-md-4 col-form-label text-md-right">Company Logo:</label>
                                <input id="logo_slug" type="file" class="form-control" name="logo_slug">
                            </div>
                            <input type="submit" value="Submit" id="submit_upload" class="btn btn-sm btn-outline-danger py-0" style="font-size: 0.8em;">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script src="{{ asset('js/app.js') }}" ></script>
<script>
    //Datatables showing companies
    $(document).ready(function () {
        var url_slug = {!! json_encode($company[0]->id) !!};
        var url = '{{ route("companies.show", ":id") }}';
        url = url.replace(':id', url_slug);
        $('#employees-table').DataTable({
            order: [],
            processing: true,
            language: {
                processing: '<span>Loading...</span>',
            },
            serverSide: true,
            ajax: {
                url: url,
            },
            columns: [
                { data: 'full_name' },
                { data: 'email'},
                { data: 'phone_number' },
                { data: 'action' },
            ],
        });
    $.ajaxSetup({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
    });
    //Company delete
    $('body').on('click', '#delete', function (event) {
        if(confirm('Are you sure you want to delete this company?'))
        {
            var url_slug = {!! json_encode($company[0]->id) !!};
            var id = '{{ route("companies.destroy", ":id") }}';
            id = id.replace(':id', url_slug);
            console.log(id);
            $.ajax({
            enctype: 'multipart/form-data',
            url: id,
            type: "DELETE",
            contentType: false,
            processData: false,
            dataType: 'json',
            
            success: function (data) {
                window.location.href = "{{ route('companies.index') }}";
                alert("Company deleted");
            },
            error: function(xhr, status, error) {
                var data=xhr.responseText;
                var jsonResponse = JSON.parse(data);
                    alert(jsonResponse.error);
            }
            });
        }
    });
    //Delete all employees
    $('body').on('click', '#delete_all', function (event) {
        if(confirm('Are you sure you want to delete all employees?'))
        {
            var url_slug = {!! json_encode($company[0]->id) !!};
            var id = '{{ route("companies.wipe", ":id") }}';
            id = id.replace(':id', url_slug);
            console.log(id);
            $.ajax({
            enctype: 'multipart/form-data',
            url: id,
            type: "DELETE",
            contentType: false,
            processData: false,
            dataType: 'json',
            
            success: function (data) {
                window.location.reload(true);
                alert("Employees deleted");
            },
            error: function(xhr, status, error) {
                var data=xhr.responseText;
                    var jsonResponse = JSON.parse(data);
                    var response = "Update failed:";
                    for (var key in jsonResponse.errors) {
                        response = response + jsonResponse.errors[key]+ "\n";
                    }

                    alert(response);
            }
            });
        }
    });
    
    //Delete single employee
    $('tbody').on('click', 'button', function(event)
    {
        if(confirm('Are you sure you want to delete this company?'))
        {
        var pressed_id = this.id.substring(1);
        var url = '{{ route("employees.destroy", ":id") }}';
        url = url.replace(':id', pressed_id);
        $.ajax({
            enctype: 'multipart/form-data',
            url: url,
            type: "DELETE",
            contentType: false,
            processData: false,
            dataType: 'json',
            
            success: function (data) {
                window.location.reload(true);
            },
            error: function(xhr, status, error) {
                var data=xhr.responseText;
                    var jsonResponse = JSON.parse(data);
                    var response = "Update failed:";
                    for (var key in jsonResponse.errors) {
                        response = response + jsonResponse.errors[key]+ "\n";
                    }

                    alert(response);
            }});
            
        }
    });
    //Submit edit data
    $('body').on('click', '#submit', function (event) {
        event.preventDefault()
        var website = $("#website").val();
        var name = $("#name").val();
        var email = $("#email").val();
        var url_slug = {!! json_encode($company[0]->id) !!};
        var id = '{{ route("companies.update", ":id") }}';
        id = id.replace(':id', url_slug);
        
        $.ajax({
        url: id,
        type: "PUT",
        data: {
            website: website,
            name: name,
            email: email,
        },
        dataType: 'json',
        
        success: function (data) {
            $('#practice_modal').modal('hide');
            window.location.reload(true);
        },
        error: function(xhr, status, error) {
            var data=xhr.responseText;
                    var jsonResponse = JSON.parse(data);
                    var response = "Update failed:";
                    for (var key in jsonResponse.errors) {
                        response = response + jsonResponse.errors[key]+ "\n";
                    }

                    alert(response);
        }
        });
    });

    $('body').on('click', '#edit', function (event) {
        event.preventDefault();
        var url_slug = {!! json_encode($company[0]->id) !!};
        var url = '{{ route("companies.edit", ":id") }}';
        url = url.replace(':id', url_slug);
        $.get(url, function (data) {
            $('#submit').val("Edit company");
            $('#practice_modal').modal('show');
            $('#website').val(data.data.website);
            $('#name').val(data.data.name);
            $('#email').val(data.data.email);
        })
    });
    //Submit image upload
    $('body').on('click', '#submit_upload', function (event) {
        event.preventDefault()
        var logo_slug = $("#logo_image").val();
        var url_slug = {!! json_encode($company[0]->id) !!};
        var id = '{{ route("companies.image", ":id") }}';
        id = id.replace(':id', url_slug);

        $.ajax({
        url: id,
        type: "POST",
        enctype: 'multipart/form-data',
        data: new FormData(document.getElementById('logo_upload')),
        processData: false,
        contentType: false,
        dataType: 'json',
        
        success: function (data) {
            $('#image_modal').modal('hide');
            window.location.reload(true);
        },
        error: function(xhr, status, error) {
            var data=xhr.responseText;
                    var jsonResponse = JSON.parse(data);
                    var response = "Update failed:";
                    for (var key in jsonResponse.errors) {
                        response = response + jsonResponse.errors[key]+ "\n";
                    }

                    alert(response);
        }
        });
    });
    //upload_modal
    $('body').on('click', '#upload', function (event) {
        event.preventDefault();
        var url_slug = {!! json_encode($company[0]->url_slug) !!};
        var url = '{{ route("companies.edit", ":url_slug") }}';
        url = url.replace(':url_slug', url_slug);
        $.get(url, function (data) {
            $('#image_modal').modal('show');
        })
    });
    }); 
</script>