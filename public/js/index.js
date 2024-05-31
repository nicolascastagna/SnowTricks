// create inputs video
const addVideoButton = document.getElementById("add-video");
const videoList = document.getElementById("videos-list");

addVideoButton.addEventListener("click", () => {
    const index = videoList.querySelectorAll("input").length;
    const newWidget = videoList.dataset.prototype.replace(/__name__/g, index);

    const newVideoElement = document.createElement("div");
    newVideoElement.innerHTML = newWidget;
    const videoInputs = newVideoElement.querySelectorAll("input");
    videoInputs.forEach((input) => {
        input.style.marginBottom = "10px";
    });

    videoList.insertAdjacentElement("beforeend", newVideoElement);
});

function deleteConfirmationImage() {
    const deleteLinks = document.querySelectorAll(".delete-image");

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const confirmation = confirm(
                "Êtes-vous sûr de vouloir supprimer cette image ?"
            );
            if (confirmation) {
                window.location.href = this.href;
            }
        });
    });
}

document.addEventListener("DOMContentLoaded", deleteConfirmationImage);

// edit or remove video
document.addEventListener("DOMContentLoaded", function () {
    const videoList = document.getElementById("videos-list");

    const attachEventListeners = (videoInputDiv) => {
        const editButton = videoInputDiv.querySelector(".edit-video-btn");
        const deleteButton = videoInputDiv.querySelector(".delete-video-btn");
        const iframeContainer = videoInputDiv.querySelector(
            ".trick-media-container"
        );
        const videoInputUrl = videoInputDiv.querySelector(".video-input-edit");

        let isDeleted = false;

        editButton.addEventListener("click", function () {
            iframeContainer.style.display = "none";
            videoInputUrl.style.display = "block";
        });

        deleteButton.addEventListener("click", function () {
            if (
                !isDeleted &&
                confirm("Êtes-vous sûr de vouloir supprimer cette vidéo ?")
            ) {
                videoInputDiv.remove();
                isDeleted = true;
            }
        });
    };

    const videoInputs = videoList.querySelectorAll(".video-input");
    videoInputs.forEach(attachEventListeners);
});
