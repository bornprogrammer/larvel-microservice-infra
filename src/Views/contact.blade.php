<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact Us</title>
</head>
<body>
    <h1>Contact US</h1>


    <form action="{{ route('contactsubmit') }}" method="post">
        @csrf
        <input type="text" name="name" placeholder="Enter Name">
        <input type="email" name="email" placeholder="Enter Email">
        <input type="submit" value="submit">
    </form>
    
</body>
</html>