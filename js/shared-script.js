const form = document.getElementById("myForm");
const overlay = document.getElementById("overlay");
const levelInput = document.getElementById("txt_levelHID");

const btn_add = document.getElementById("btn_add");
const btn_cancel = document.getElementById("btn_cancel");

btn_add.addEventListener("click", showForm);
btn_cancel.addEventListener("click", hideForm);

overlay.addEventListener("click", hideForm);

document.addEventListener("keydown", (event) => {
    //Whenever the escape button is clicked, hide the form
    if (event.key === "Escape") {
        hideForm();
    }
});

function showForm() {
    const getLevelID = this.dataset.levelid;

    if(getLevelID != undefined){
        levelInput.value = getLevelID;
    }

    form.style.display = "block";
    overlay.style.display = "block";
}

function hideForm() {
    form.style.display = "none";
    overlay.style.display = "none";
}
