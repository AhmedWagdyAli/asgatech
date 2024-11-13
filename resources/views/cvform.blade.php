<!DOCTYPE html>
<html>

<head>
    <title>CV Form</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQJf1IHdZtKVCpCGEWAX4gnbi5ZC7EAdZ6ZLlpaP+0zYyG7NrLmX8x3pJo" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <form action="{{ route('upload.cv') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-6 form-group">
                <label for="keywords" class="form-label">Keywords</label>
                <input type="text" name="keywords[]" class="form-control" id="keywords">
            </div>
            <div class="mb-6 form-group">
                <label for="cv" class="form-label">Upload CV</label>
                <input type="file" name="cv" class="form-control" id="cv">
            </div>  
            <button type="submit" class="btn btn-info">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS (optional, for additional interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Xjg3pD5UHVtH1z44QuuCOw4q6l5V" crossorigin="anonymous"></script>
</body>

</html>
