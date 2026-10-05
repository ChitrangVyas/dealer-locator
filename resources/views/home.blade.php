@extends('layouts.adminlte')
@section('title')
{{$title}}
@endsection
@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">        
        <div class="row">
          <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>{{ $dealer_count }}</h3>
                  <p>Dealers</p>
                </div>
              </div>
          </div>
          <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ $category_count }}</h3>
                  <p>Categories</p>
                </div>              
              </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection

@section('plugins.Chartjs', true)
@section('plugins.daterangepicker', true)
@section('plugins.jqvmap', true)
