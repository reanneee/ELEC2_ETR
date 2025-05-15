<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Login</title> 
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> 
    <style> 
        .login-form { 
            padding: 25px; 
            margin: 0 auto; 
            border-radius: 8px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            background-color: #f9f9f9;
            max-width: 450px; 
            width: 100%;
        } 
 
        .login-button { 
            margin-top: 25px; 
            padding: 1px;   
        } 
 
        .form-container { 
            margin-top: 20px;  
        } 
 
        .form-group input { 
            padding: 12px; 
        } 
 
        .alert { 
            margin-bottom: 20px; 
        } 
 
        .text-danger { 
            font-size: 0.875rem; 
            margin-top: 5px;
            display: block;
        } 

        input.is-invalid {
            border-color: #dc3545;
        }
    </style> 
</head> 
<body class="d-flex justify-content-center align-items-center" style="height: 100vh; margin: 0; background-color: #f0f0f0;"> 
 
    <div class="container"> 
        <div class="row justify-content-center"> 
            <div class="col-md-7 col-lg-6">
                <form method="POST" action="{{ route('login') }}" class="login-form"> 
                    @csrf 
 
                    @if (session('error')) 
                        <div class="alert alert-danger"> 
                            {{ session('error') }} 
                        </div> 
                    @endif

                    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
 
                    <div class="form-group"> 
                        <label for="email" class="font-weight-bold">Email</label> 
                        <input type="email" name="email" id="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" placeholder="Enter your email"> 
                        @error('email') 
                            <div class="text-danger">{{ $message }}</div> 
                        @enderror 
                    </div> 
 
                    <div class="form-group"> 
                        <label for="password" class="font-weight-bold">Password</label> 
                        <input type="password" name="password" id="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Enter your password"> 
                        @error('password') 
                            <div class="text-danger">{{ $message }}</div> 
                        @enderror 
                    </div> 
 
                    <div class="form-group login-button"> 
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button> 
                    </div> 
 
                    <div class="mt-3 text-center"> 
                        <p>If you don't have an account, <a href="{{ route('register') }}">please register</a>.</p> 
                    </div> 
                </form> 
            </div> 
        </div> 
    </div> 
 
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 
</body> 
</html>