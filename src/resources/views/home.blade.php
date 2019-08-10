@extends('web::layouts.grids.12')

@section('title', 'Home')
@section('page_header', 'Home')
@section('page_description', 'Home')

@section('full')

  congratulation this is a Authed page




    <div>
      <b-button href="{{route('auth.logout')}}">Logout</b-button>
      <b-button variant="danger">Button</b-button>
      <b-button variant="success">Button</b-button>
      <b-button variant="outline-primary">Button</b-button>
    </div>


@endsection

@push('javascript')

  <script type="text/javascript">
    Swal.fire(
        'Good job!',
        'You clicked the button!',
        'success'
    )
  </script>


@endpush