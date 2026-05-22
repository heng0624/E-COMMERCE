// auto select all the checkbox when i click a certain checkbox
function toggleCheckboxes() {
    var masterCheckbox = document.getElementById("masterCheckBox");
    var slaveCheckboxes = document.getElementsByClassName("slaveCheckbox");

    for (var i = 0; i < slaveCheckboxes.length; i++) {
        slaveCheckboxes[i].checked = masterCheckbox.checked;
    }
}

function uncheckMasterCheckBox() {
    var masterCheckbox = document.getElementById("masterCheckBox");
    masterCheckbox.checked = false;
}