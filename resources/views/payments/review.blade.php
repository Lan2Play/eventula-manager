@extends('layouts.default')

@section('page_title', 'Review Order')

@section('content')

<div class="container">
  <div class="page-header">
    <h1>
      Confirm Order
    </h1> 
  </div>
  <div class="row">
    <div class="col-xs-12 col-md-8">
      {!! Settings::getTermsAndConditions() !!}
      <p>By Clicking on Confirm you are agreeing to the Terms and Conditions as set by {!! Settings::getOrgName() !!}</p>
      {{ Form::open(array('url'=>'/payment/post')) }}
        <button class="btn btn-default">Confirm</button>
      {{ Form::close() }}
    </div>
    <div class="col-xs-12 col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Order Details</h3>
        </div>
        <div class="panel-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <tbody>
                @php ($total = 0)
                @foreach ($basket_items as $item)
                  <tr>
                    <td>
                      <strong>{{ $item->name }}</strong>
                    </td>
                    <td>
                      x {{ $item->quantity }}
                    </td>
                    <td>
                      £{{ $item->price }}
                    </td>
                  </tr>
                  @php ($total += ($item->price * $item->quantity))
                @endforeach
                <tr>
                  <td></td>
                  <td>
                    <strong>Total:</strong>
                  </td>
                  <td>
                    £{{ $total }}
                  </td>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection