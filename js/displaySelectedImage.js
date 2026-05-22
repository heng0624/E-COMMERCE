// display the image within the border
function displaySelectedImage(event) {
    const fileInput = event.target;
    const files = fileInput.files;

    if (files.length > 0) {
        const selectedImage = files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            const uploadedImage = document.getElementById('uploadedImage');
            uploadedImage.src = e.target.result;
        };

        reader.readAsDataURL(selectedImage); // Read the selected image as a data URL
    }
}


