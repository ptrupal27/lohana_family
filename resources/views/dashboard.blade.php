@extends('layouts.app')

@section('content')
    <!-- <div class="row">
        <div class="col-md-12 mb-4">
            <h2 class="fw-bold text-maroon">ડેશબોર્ડ</h2>
        </div>
    </div> -->

    <div class="mb-4 bg-white py-0 shadow-sm rounded">
        <!-- Top Yellow Line -->
        <div style="height: 4px; background-color: #ffd700;"></div>
        
        <!-- Header Section -->
        <div class="py-4 border-bottom" style="border-bottom: 2px solid #ffd700 !important;">
            <div class="d-flex flex-column flex-md-row align-items-center px-4">
                <div class="mb-3 mb-md-0 me-md-4 ps-md-2">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Lohana Logo" style="height: 125px; width: auto;" class="rounded">
                </div>
                <div class="text-center flex-grow-1">
                    <h1 class="fw-bold mb-1" style="color: #e31e24; font-size: clamp(2rem, 5vw, 3.2rem); letter-spacing: 1px; font-family: 'Noto Sans Gujarati', sans-serif;">શ્રી ઘોઘારી લોહાણા મહાજન</h1>
                    
                    <div class="mt-2">
                        <p class="mb-1 fw-bold text-dark" style="font-size: clamp(0.95rem, 2vw, 1.2rem);">
                            9/638, સિધ્ધમાતાની શેરી, વાડી ફળીયા કોટસફિલ રોડ, સુરત - 395003
                        </p>
                        
                        <p class="mb-1 fw-bold text-dark" style="font-size: clamp(0.95rem, 2vw, 1.2rem);">
                            ટેલિફોન નંબર : 0261-2418235, મો. +91 99799 05631 (મહેતાજી)
                        </p>
                        
                        <p class="mb-0 fw-bold" style="font-size: clamp(0.95rem, 2vw, 1.2rem);">
                            <span style="color: #28a745;">ટ્રસ્ટ રજી. એ-324-સુરત</span> 
                            <span style="color: #e31e24;" class="mx-1">•</span> 
                            <span style="color: #333;">E-mail :</span> <span style="color: #0000ee;">sglmsurat@gmail.com</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>



        <!-- Committee Members Section - Organized Format -->
        <div class="py-2">
            <div class="container-fluid px-2">
                <div class="row g-0 text-center fw-bold text-dark" style="font-size: clamp(0.7rem, 1.4vw, 0.9rem);">
                    <div class="col-md-4 py-1 border-end-md" style="border-right: 1px solid #ffd70080;">
                        પ્રમુખશ્રી : શૈલેષભાઈ બી. સોનપાલ - મો. ૭૨૨૭૯ ૭૯૮૬૬
                    </div>
                    <div class="col-md-4 py-1 border-end-md" style="border-right: 1px solid #ffd70080;">
                        મંત્રીશ્રી : રસિકભાઈ જી. રાયઠઠા - મો. ૯૮૨૫૧ ૨૩૭૮૯
                    </div>
                    <div class="col-md-4 py-1">
                        ખજાનચીશ્રી : વી. પી. પાનવાલા - મો. ૯૮૯૮૯ ૦૪૫૫૮
                    </div>
                </div>
            </div>
        </div>
        <div class="py-2 border-bottom" style="border-bottom: 2px solid #ffd700 !important;">
            <div class="container-fluid px-2">
                <div class="row g-0 text-center fw-bold text-dark" style="font-size: clamp(0.7rem, 1.4vw, 0.9rem);">
                    <div class="col-md-4 py-1 border-end-md" style="border-right: 1px solid #ffd70080;">
                        ઉપપ્રમુખશ્રી : વ્રજેશભાઈ બી. ઉનડકર - મો. ૯૩૭૪૯ ૯૯૯૯૯
                    </div>
                    <div class="col-md-4 py-1 border-end-md" style="border-right: 1px solid #ffd70080;">
                        સહમંત્રીશ્રી : દિનેશભાઈ પી. કારીયા - મો. ૯૪૨૬૧ ૮૭૧૨૦
                    </div>
                    <div class="col-md-4 py-1">
                        સહખજાનચીશ્રી : કેતનભાઈ આર. વડેસ - મો. ૯૮૨૫૦ ૩૩૯૨૫
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-3">
        <div class="col-md-3">
            <a href="{{ route('members.index') }}" class="text-decoration-none">
                <div class="card card-maroon shadow-sm border-0 overflow-hidden h-100 hover-zoom">
                    <div class="card-body p-3 position-relative">
                        <div class="position-absolute top-0 end-0 p-2 opacity-25">
                            <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-white">કુલ સભ્યો</h6>
                        <h3 class="fw-bold mb-0 text-white">{{ $totalMembers + $totalFamilyMembers }}</h3>
                        <div class="mt-1">
                            <small style="font-size: 0.75rem;" class="text-white opacity-75">કુલ રજીસ્ટર્ડ સભ્યો</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('members.main') }}" class="text-decoration-none">
                <div class="card card-crimson shadow-sm border-0 overflow-hidden h-100 hover-zoom">
                    <div class="card-body p-3 position-relative">
                        <div class="position-absolute top-0 end-0 p-2 opacity-25">
                            <i class="bi bi-house-door-fill" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-white">કુલ પરિવાર</h6>
                        <h3 class="fw-bold mb-0 text-white">{{ $totalMembers }}</h3>
                        <div class="mt-1">
                            <small style="font-size: 0.75rem;" class="text-white opacity-75">રજીસ્ટર્ડ કુટુંબોની સંખ્યા</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('members.index', ['gender' => 'Male']) }}" class="text-decoration-none">
                <div class="card card-blue shadow-sm border-0 overflow-hidden h-100 hover-zoom">
                    <div class="card-body p-3 position-relative">
                        <div class="position-absolute top-0 end-0 p-2 opacity-25">
                            <i class="bi bi-gender-male" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-white">કુલ પુરુષો</h6>
                        <h3 class="fw-bold mb-0 text-white">{{ $totalMales }}</h3>
                        <div class="mt-1">
                            <small style="font-size: 0.75rem;" class="text-white opacity-75">પુરુષ સભ્યોની સંખ્યા</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('members.index', ['gender' => 'Female']) }}" class="text-decoration-none">
                <div class="card card-pink shadow-sm border-0 overflow-hidden h-100 hover-zoom">
                    <div class="card-body p-3 position-relative">
                        <div class="position-absolute top-0 end-0 p-2 opacity-25">
                            <i class="bi bi-gender-female" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-white">કુલ મહિલાઓ</h6>
                        <h3 class="fw-bold mb-0 text-white">{{ $totalFemales }}</h3>
                        <div class="mt-1">
                            <small style="font-size: 0.75rem;" class="text-white opacity-75">મહિલા સભ્યોની સંખ્યા</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <style>
        .hover-zoom {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            cursor: pointer;
        }
        .hover-zoom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>




@endsection