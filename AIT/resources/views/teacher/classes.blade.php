@extends('layouts.app')

@section('content')

    <div class="container my-2">
        <div class="row justify-content-center my-2">
          <h2>Classes</h2>
        </div>
        <div class="row justify-content-center my-2">
            <div class="col-md-8">
                <div class="col-6">
                    <a href="#" data-toggle="modal" data-target="#addClass" class="btn btn-sm btn-primary"><i class="fa fa-sm fa-users" aria-hidden="true"></i> Add Class</a>
                </div>
                <div class="col-6">
                    {{-- can write something --}}
                </div>
            </div> 
        </div>
        {{-- student modal relate to the row --}}
        <div class="modal fade" id="addClass" tabindex="-1" role="dialog" aria-labelledby="example" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ route('addClasses') }}" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="example">Add Class</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                        <div class="row justify-content-center">
                            {{-- <input type="text" name ='grade'> --}}
                            @foreach ($grades[1] as $grade)
                                <div class="col-4">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="selected[]" value="{{ $grade->id }}" 
                                        @foreach ($grades[0] as $select)
                                            @if ($select->id == $grade->id)
                                                disabled
                                            @endif
                                        @endforeach
                                        >
                                        <label class="form-check-label" for="selected[]">{{ $grade->grade }}</label>
                                    </div>
                                </div>  
                            @endforeach
                            
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8 col-12">
                <table class="table table-hover table-sm table-responsive-sm">
                    <thead class="thead-dark">
                      <tr>
                        <th scope="col">Grade</th>
                        <th scope="col"></th>
                        {{-- <th scope="col"></th> --}}
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($grades[0] as $grade)
                            <tr>
                                <td scope="row">{{ $grade->grade }}</td>
    
                                <td class="text-right">
                                    <a class="btn btn-sm btn-primary" href="#" ><i class="fa fa-sm fa-user" aria-hidden="true"></i> Mark attendance</a>
                                    <a class="btn btn-sm btn-primary" href="{{ route('gradeStudent',$grade->id) }}" ><i class="fa fa-sm fa-user" aria-hidden="true"></i> Student</a>
                                    <a class="btn btn-sm btn-danger" href="#" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                  </table>
                  {{-- <div class="row justify-content-center">
                      <div class="col-12 text-center">
                        
                      </div> 
                  </div>       --}}
            </div>
            
        </div>
        <div class="row justify-content-center">
            {{ $grades[0]->links() }}
        </div>
    </div>
    
@endsection