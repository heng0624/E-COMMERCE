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


// auto select all the checkbox when i click a certain checkbox
function toggleCheckboxes() {
    const master = document.getElementById("masterCheckBox");
    const slaves = document.querySelectorAll(".slaveCheckbox");
    slaves.forEach(cb => cb.checked = master.checked);
}

function uncheckMasterCheckBox() {
    const master = document.getElementById("masterCheckBox");
    const slaves = document.querySelectorAll(".slaveCheckbox");
    const allChecked = Array.from(slaves).every(cb => cb.checked);
    master.checked = allChecked;
}

//modal form

function toggleForm() {
    const modal = document.getElementById('addressModal');
    modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
    document.getElementById('modal-title').innerText = "Add New Address";
    document.querySelector('form.address-form').reset();
    document.getElementById('addressId').value = "";
}

// Prefill form for editing
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('modal-title').innerText = "Update Address";
        document.getElementById('addressId').value = button.dataset.id;
        document.getElementById('fullName').value = button.dataset.name;
        document.getElementById('phone').value = button.dataset.phone;
        document.getElementById('city').value = button.dataset.city;
        document.getElementById('state').value = button.dataset.state;
        document.getElementById('postalCode').value = button.dataset.postal;
        document.getElementById('country').value = button.dataset.country;
        document.getElementById('address').value = button.dataset.address;

        document.getElementById('addressModal').style.display = 'block';
    });
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addressModal');
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

//set success message timer
window.addEventListener('DOMContentLoaded', () => {
    const msg = document.getElementById('successMsg');
    if (msg) {
        setTimeout(() => {
            msg.style.display = 'none';
        }, 2000); // 2 seconds
    }
});

//filter form 
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filterForm");
  
    // Auto submit on price order change
    document.querySelector("select[name='price_order']").addEventListener("change", () => {
      form.submit();
    });
  
    // Auto submit on category checkbox change
    form.querySelectorAll("input[type='checkbox']").forEach(cb => {
      cb.addEventListener("change", () => {
        form.submit();
      });
    });
  
    // Auto submit on min/max price input change
    const minPrice = form.querySelector("input[name='min_price']");
    const maxPrice = form.querySelector("input[name='max_price']");
  
    [minPrice, maxPrice].forEach(input => {
      input.addEventListener("change", () => {
        form.submit();
      });
    });
  });
  //auto slider
let slideIndex = 0;
showSlides();

function showSlides() {
    let slides = document.getElementsByClassName("slide");
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }    
    slides[slideIndex - 1].style.display = "block";  
    setTimeout(showSlides, 3000); // Change slide every 3 seconds
}
// list the popup list for user chosse
function toggleForms() {
    const modal = document.getElementById('addressModal');
    modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
}

function saveSelectedAddress() {
    const selected = document.querySelector('input[name="addressSelect"]:checked');
    if (selected) {
        document.getElementById('selectedAddressId').value = selected.value;
        toggleForms();
        location.reload(); // Simple: just reload page to show new address
    } else {
        alert("Please select an address!");
    }
}

// Close modal if clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addressModal');
    if (event.target === modal) {
        modal.style.display = "none";
    }
}