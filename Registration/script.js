function changeCoverPhoto(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('cover-image').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}