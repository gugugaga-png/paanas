@extends ('layouts.app')
@section('content')
<div class="container-xl px-3">
    <div class="row">
        <div class="col-7">
            <p class="fw-bolder" style="font-size: 50px; width: 30rem;">Menabung Lebih Teratur Dengan Shapphy</p>
            <p style="font-size :20px; width:50rem;">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Delectus, nulla hic cupiditate quod a error ipsa dolore maiores totam odio?</p>
            <a style="width:11rem" href="#" class="btn btn-pill btn-dark" role="button">Log In</a>
            <a style="width:11rem" href="#" class="btn btn-pill btn-dark" role="button">Register</a>
        </div>
        <div class="col-5">
                <img src="{{ asset('images/phone.svg') }}" alt="Banner">
        </div>
    </div>
    <div class="row">
        <div class="col-5">
            <img src="{{ asset('images/chart.svg') }}" alt="Banner">
            <img src="{{ asset('images/statchart.svg') }}" alt="Banner">
        </div>
        <div class="col-7">
            <div style="width: 60rem">
                <p class="fw-bold" style="font-size: 50px; width: ;">Expand your spendings in a proper way</p>
                <p style="font-size :20px; width:50rem;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo provident quod animi tempora debitis cumque, dolore accusamus delectus doloribus odit illum praesentium exercitationem voluptate aspernatur nemo ipsa deserunt consectetur eum.</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-7">
            <div style="width: 60rem">
                <p class="fw-bold" style="font-size: 50px; width: ;">Get your annual summary of your money</p>
                <p style="font-size :20px; width:50rem;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo provident quod animi tempora debitis cumque, dolore accusamus delectus doloribus odit illum praesentium exercitationem voluptate aspernatur nemo ipsa deserunt consectetur eum.</p>
            </div>
        </div>
        <div class="col-5">
            <img src="{{ asset('images/statchart.svg') }}" alt="Banner" class="px-7">
        </div>
    </div>
</div>
    
@endsection