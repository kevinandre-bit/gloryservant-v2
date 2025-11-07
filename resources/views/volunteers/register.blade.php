<!DOCTYPE html>
<html>
<head>
    <title>Volunteer Registration</title>
</head>
<body>
    <h2>Register as a New Ministry Volunteer</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form method="POST" action="/volunteer/register">
        @csrf

        <label>Name:</label><br>
        <input type="text" name="name" value="{{ old('name') }}"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="{{ old('email') }}"><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" value="{{ old('phone') }}"><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
