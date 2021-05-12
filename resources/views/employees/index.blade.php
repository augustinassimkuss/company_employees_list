@extends('layouts.app')
@section('content')
<div class="container">
    <form id="new-company">
        <button type="button" id="new-employee-button" class="btn btn-success">Add an employee</button>    
    </form>
    <div class="row m-5">
        <div class="col-12">
        <table id="employees-table" class="table table-striped table-bordered">
            <thead>
            <tr>
            <th>Full Name</th>
            <th>Work place</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>
    <div class="modal fade" id="edit-employee-modal">
            <div class="modal-dialog">
                <form id="new-company-modal-form">
                    <div class="modal-content">
                            <div class="modal-body">
                                <label>Full Name</label>
                                <input type="text" name="full_name_update" id="full_name_update" value="" placeholder="Example Smith" class="form-control">
                                <label>Email</label>
                                <input type="text" name="email_update" id="email_update" value="" placeholder="example@mail.com" class="form-control">
                                <label>Phone Number</label>
                                <input type="text" name="phone_number_update" id="phone_number_update" value="" placeholder="3706*******" class="form-control">
                                <label>Company</label>
                                <select class="form-control m-bot15" id="employee_company_update" name="employee_company_update">
                                    @foreach($companies as $company)
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                    @endForeach
                                </select>
                            </div>
                            <input type="submit" value="Submit" id="submit_update" class="btn btn-sm btn-outline-danger py-0" style="font-size: 0.8em;">
                    </div>
                </form>
            </div>
        </div>
    <div class="modal fade" id="new-employee-modal">
            <div class="modal-dialog">
                <form id="new-company-modal-form">
                    <div class="modal-content">
                            <div class="modal-body">
                                <label>Full Name</label>
                                <input type="text" name="full_name" id="full_name" value="" placeholder="Example Smith" class="form-control">
                                <label>Email</label>
                                <input type="text" name="email" id="email" value="" placeholder="example@mail.com" class="form-control">
                                <label>Phone Number</label>
                                <input type="text" name="phone" id="phone_number" value="" placeholder="3706*******" class="form-control">
                                <label>Company</label>
                                <select class="form-control m-bot15" id="employee_company" name="employee_company">
                                    @foreach($companies as $company)
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                    @endForeach
                                </select>
                            </div>
                            <input type="submit" value="Submit" id="submit" class="btn btn-sm btn-outline-danger py-0" style="font-size: 0.8em;">
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection
<script src="{{ asset('js/app.js') }}" ></script>
<script>
    $(document).ready(function () {
        var idtopass = "";
        
        $('#employees-table').DataTable({
            order: [],
            processing: true,
            language: {
                processing: '<span>Loading...</span>',
            },
            serverSide: true,
            ajax: {
                url: "{{ route('employees.index') }}",
            },
            columns: [
                { data: 'full_name' },
                { data: 'name' },
                { data: 'email'},
                { data: 'phone_number'},
                { data: 'action' },
            ],
        });
        $.ajaxSetup({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('body').on('click', '#submit', function (event) {
            event.preventDefault()
            var full_name = $("#full_name").val();
            var phone_number = $("#phone_number").val();
            var email = $("#email").val();
            var employee_company = $("#employee_company").val();
            console.log(employee_company);
            //var url_slug = ;
        
            $.ajax({
                url: '{{ route("employees.store") }}',
                type: "POST",
                data: {
                    full_name: full_name,
                    phone_number: phone_number,
                    email: email,
                    company_id: employee_company,
                },
                dataType: 'json',
                
                success: function (data) {
                    var newData = JSON.stringify(data)
                    var jsonResponse = JSON.parse(newData);
                    alert("Succesfully registered");
                    window.location.reload(true);
                },
                error: function(xhr, status, error) {
                    var data=xhr.responseText;
                    var jsonResponse = JSON.parse(data);
                    var response = "Update failed:";
                    for (var key in jsonResponse.errors) {
                        console.log("Key: " + key);
                        console.log("Value: " + jsonResponse.errors[key]);
                        response = response + jsonResponse.errors[key]+ "\n";
                    }

                    alert(response);
                }
            });
        });

        $('body').on('click', '#new-employee-button', function (event) {
            event.preventDefault();
            
            var url = '{{ route("employees.create") }}';
            //url = url.replace(':url_slug', url_slug);
            console.log(url);
            $.get(url, function (data) {
                //$('#userCrudModal').html("Edit category");
                $('#submit').val("Create employee");
                $('#new-employee-modal').modal('show');
                //$('id').val(data.data.id);
                //$('#website').val(data.data.website);
                //$('#name').val(data.data.name);
                //$('#email').val(data.data.email);
            });
        });
    //Action handler
        $('tbody').on('click', 'button', function(event)
        {
            console.log(this.id);
            if(this.id.charAt(0) == "d" && confirm('Are you sure you want to delete this employee?'))
            {
                
                idtopass = this.id.substring(1);
                var url = '{{ route("employees.destroy", ":id") }}';
                url = url.replace(':id', this.id.substring(1));
                $.ajax({
                    enctype: 'multipart/form-data',
                    url: url,
                    type: "DELETE",
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    
                    success: function (data) {
                        alert("Succesfully deleted");
                        window.location.reload(true);
                    },
                    error: function(xhr, status, error) {
                        var data=xhr.responseText;
                        var jsonResponse = JSON.parse(data);
                        var response = "Update failed:";
                        for (var key in jsonResponse.errors) {
                            console.log("Key: " + key);
                            console.log("Value: " + jsonResponse.errors[key]);
                            response = response + jsonResponse.errors[key]+ "\n";
                        }

                        alert(response);
                    },
                });
            }
            else if(this.id.charAt(0) == "e")
            {
                idtopass = this.id.substring(1);
                console.log(idtopass);
                //var pressed_id = this.id.substring(1);
                //console.log(pressed_id);
                event.preventDefault();
                var url = '{{ route("employees.edit", ":id") }}';
                url = url.replace(':id', this.id.substring(1));
                console.log()
                $.get(url, function (data) {
                    
                    $('#submit_update').val("Edit employee");
                    $('#edit-employee-modal').modal('show')
                    //$('#full_name_update').val('');
                    //$('#phone_number_update').val(data.phone_number);
                    //$('#email').val(data.email);
                    //$('#employee_company_update').val(data.company_id);
                })
            }
        });
        //
        
        $('body').on('click', '#submit_update', function (event) {
            event.preventDefault()
            //idtopass = this.id.substring(1);
            var full_name_update = $("#full_name_update").val();
            var email_update = $("#email_update").val();
            var phone_number_update = $("#phone_number_update").val();
            var employee_company_update = $("#employee_company_update").val()
            var url_slug = idtopass;
            var id = '{{ route("employees.update", ":id") }}';
            id = id.replace(':id', url_slug);
            
            $.ajax({
                url: id,
                type: "PUT",
                data: {
                    full_name: full_name_update,
                    phone_number: phone_number_update,
                    email: email_update,
                    company_id: employee_company_update,
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
                        console.log("Key: " + key);
                        console.log("Value: " + jsonResponse.errors[key]);
                        response = response + jsonResponse.errors[key]+ "\n";
                    }

                    alert(response);
                    },
            });
        });
    });
</script>