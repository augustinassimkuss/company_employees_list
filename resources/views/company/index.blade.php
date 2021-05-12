@extends('layouts.app')
@section('content')
<div class="container">
    <form id="new-company">
        <button type="button" id="new-company-button" class="btn btn-success">Add a company</button>    
    </form>
    <div class="row m-5">
        <div class="col-12">
        <table id="companies-table" class="table table-striped table-bordered">
            <thead>
            <tr>
            <th>Company Name</th>
            <th>Email</th>
            <th>Website</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>
    <div class="modal fade" id="new-company-modal">
            <div class="modal-dialog">
                <form id="new-company-modal-form">
                    <div class="modal-content">
                            <div class="modal-body">
                                <label>Email</label>
                                <input type="text" name="email" id="email" value="" placeholder="example@email.com" class="form-control">
                                <label>Website</label>
                                <input type="text" name="website" id="website" value="" placeholder="www.example.com" class="form-control">
                                <label>Name</label>
                                <input type="text" name="name" id="name" value="" placeholder="UAB Itoma" class="form-control">
                                
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
        $('#companies-table').DataTable({
            order: [],
            processing: true,
            language: {
                processing: '<span>Loading...</span>',
            },
            serverSide: true,
            ajax: {
                url: "{{ route('companies.index') }}",
            },
            columns: [
                { data: 'name',
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        var url = '{{ route("companies.show", ":id") }}';
                        url = url.replace(':id', oData.id);
                        $(nTd).html("<a href='"+url+"'>"+oData.name+"</a>")
                    } },
                { data: 'email'},
                { data: 'website'},
            ],
        });
        $.ajaxSetup({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('body').on('click', '#submit', function (event) {
        event.preventDefault()
        var website = $("#website").val();
        var name = $("#name").val();
        var email = $("#email").val();
    
        $.ajax({
        url: '{{ route("companies.store") }}',
        type: "POST",
        data: {
            website: website,
            name: name,
            email: email,
        },
        dataType: 'json',
        
        success: function (data) {
            var newData = JSON.stringify(data)
            var jsonResponse = JSON.parse(newData);
            var slug = jsonResponse["id"];
            var url = '{{ route("companies.show", ":id") }}';
            url = url.replace(':id', jsonResponse.data);
            alert("Succesfully registered");
            window.location.href = url;
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

    $('body').on('click', '#new-company-button', function (event) {
        event.preventDefault();
        
        var url = '{{ route("companies.create") }}';
        console.log(url);
        $.get(url, function (data) {
            $('#submit').val("Create company");
            $('#new-company-modal').modal('show');
        })
    });
    });
</script>