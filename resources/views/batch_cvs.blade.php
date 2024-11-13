<!DOCTYPE html>
<html>

<head>
    <title>CV Form</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQJf1IHdZtKVCpCGEWAX4gnbi5ZC7EAdZ6ZLlpaP+0zYyG7NrLmX8x3pJo" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <form action="{{ route('batch.cvs') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="cvs">job title:</label>
                <input type="text" name="job_title">
            </div>
            <div class="form-group">
                <label for="cvs">job keywords:</label>
                <input type="text" name="keywords">
            </div>
            <label for="cvs">Upload CVs:</label>
            <input type="file" name="cvs[]" multiple>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
        
    </div>

    <!-- Bootstrap JS (optional, for additional interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Xjg3pD5UHVtH1z44QuuCOw4q6l5V" crossorigin="anonymous"></script>
</body>

</html>
